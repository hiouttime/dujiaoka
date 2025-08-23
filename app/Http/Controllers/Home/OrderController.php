<?php

namespace App\Http\Controllers\Home;

use App\Exceptions\RuleValidationException;
use App\Http\Controllers\BaseController;
use App\Models\Order;
use App\Models\Goods;
use App\Models\Carmis;
use App\Models\Pay;
use App\Models\FrontUser;
use App\Services\OrderProcess;
use App\Services\CacheManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


/**
 * 订单控制器
 *
 * Class OrderController
 * @package App\Http\Controllers\Home
 * @author: Assimon
 * @email: Ashang@utf8.hk
 * @blog: https://utf8.hk
 * Date: 2021/5/30
 */
class OrderController extends BaseController
{


    /**
     * 订单服务层
     * @var \App\Services\Orders
     */
    private $orderService;

    /**
     * 订单处理层.
     * @var OrderProcessService
     */
    private $orderProcessService;

    public function __construct()
    {
        $this->orderService = app('App\Services\Orders');
        $this->orderProcessService = app('App\Services\OrderProcess');
    }

    /**
     * 创建订单
     */
    public function createOrder(Request $request)
    {
        DB::beginTransaction();
        try {
            $cartItems = $request->input('cart_items', []);
            if (empty($cartItems)) {
                throw new RuleValidationException('购物车为空');
            }

            // 获取用户信息以决定验证规则
            $user = Auth::guard('web')->user();
            $contactRequired = cfg('contact_required', 'email');
            
            // 根据设置和用户状态决定email字段验证规则
            $emailRule = 'nullable';
            if ($contactRequired === 'email') {
                $emailRule = $user ? 'nullable|email' : 'required|email';
            } elseif ($contactRequired === 'any') {
                $emailRule = $user ? 'nullable|string' : 'required|string|min:6';
            }
            
            $validated = $request->validate([
                'email' => $emailRule,
                'payway' => 'required|integer',
                'search_pwd' => 'nullable|string',
                'cart_items' => 'required|array',
                'cart_items.*.goods_id' => 'required|integer',
                'cart_items.*.sub_id' => 'required|integer', 
                'cart_items.*.quantity' => 'required|integer|min:1',
                'use_balance' => 'boolean',
                'balance_amount' => 'numeric|min:0'
            ]);

            $userDiscountRate = 1.00;
            $userId = null;
            
            if ($user) {
                $userId = $user->id;
                $userDiscountRate = $user->discount_rate;
                // 如果用户已登录但未提供邮箱，使用用户邮箱
                if (empty($validated['email'])) {
                    $validated['email'] = $user->email;
                }
            }

            $totalPrice = 0;
            $orderItems = [];

            // 获取库存模式配置
            $stockMode = cfg('stock_mode', 2); // 默认发货时减库存
            $orderSn = strtoupper(\Illuminate\Support\Str::random(16)); // 提前生成订单号用于库存锁定

            foreach ($cartItems as $item) {
                $goods = Goods::with('goods_sub')->find($item['goods_id']);
                if (!$goods || !$goods->is_open) {
                    throw new RuleValidationException("商品不存在或已下架");
                }

                // 检查是否需要登录购买
                if ($goods->require_login && !$user) {
                    throw new RuleValidationException("{$goods->gd_name} 需要登录后才能购买");
                }

                $sub = $goods->goods_sub()->find($item['sub_id']);
                if (!$sub) {
                    throw new RuleValidationException("商品规格不存在");
                }

                // 计算实际库存
                $actualStock = $goods->type == Goods::AUTOMATIC_DELIVERY 
                    ? Carmis::where('sub_id', $item['sub_id'])->where('status', 1)->count()
                    : $sub->stock;

                // 根据库存模式检查库存
                if ($stockMode == 1) {
                    // 下单即减库存模式：需要考虑已锁定的库存
                    if (!CacheManager::checkStockAvailable($item['sub_id'], $item['quantity'], $actualStock)) {
                        throw new RuleValidationException("{$goods->gd_name} 库存不足");
                    }
                } else {
                    // 发货时减库存模式：直接检查实际库存
                    if ($item['quantity'] > $actualStock) {
                        throw new RuleValidationException("{$goods->gd_name} 库存不足");
                    }
                }

                // 检查购买数量限制
                if ($goods->buy_limit_num > 0) {
                    if ($goods->require_login && $user) {
                        // 检查所有有效订单（待支付、待处理、处理中、已完成）
                        $purchasedQuantity = \App\Models\OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                            ->where('orders.user_id', $user->id)
                            ->where('order_items.goods_id', $goods->id)
                            ->whereIn('orders.status', [
                                \App\Models\Order::STATUS_WAIT_PAY,
                                \App\Models\Order::STATUS_PENDING,
                                \App\Models\Order::STATUS_PROCESSING,
                                \App\Models\Order::STATUS_COMPLETED
                            ])
                            ->sum('order_items.quantity');
                            
                        if ($purchasedQuantity + $item['quantity'] > $goods->buy_limit_num) {
                            throw new RuleValidationException("{$goods->gd_name} 超出最大购买数量（已下单 {$purchasedQuantity} 件，限购 {$goods->buy_limit_num} 件）");
                        }
                    } else {
                        if ($item['quantity'] > $goods->buy_limit_num) {
                            throw new RuleValidationException("{$goods->gd_name} 超出限购数量");
                        }
                    }
                }

                // 应用用户等级折扣
                $originalPrice = $sub->price;
                $discountedPrice = $originalPrice * $userDiscountRate;
                $subtotal = $discountedPrice * $item['quantity'];
                $totalPrice += $subtotal;

                // 处理自定义字段
                $customFields = $item['custom_fields'] ?? [];
                $infoHtml = '';
                if (!empty($customFields)) {
                    $infoItems = [];
                    foreach ($customFields as $key => $value) {
                        $displayValue = in_array($value, ['0', '1', 0, 1]) ? ($value == 1 ? '是' : '否') : $value;
                        $infoItems[] = "{$key}: {$displayValue}";
                    }
                    $infoHtml = implode('<br>', $infoItems);
                }

                $orderItems[] = [
                    'goods_id' => $goods->id,
                    'sub_id' => $sub->id,
                    'goods_name' => $goods->gd_name . ' [' . $sub->name . ']',
                    'unit_price' => $discountedPrice,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                    'type' => $goods->type,
                    'info' => $infoHtml
                ];
            }

            $payway = cache()->remember("pay_method_{$validated['payway']}", 43200, function () use ($validated) {
                return Pay::find($validated['payway']);
            });
            if (!$payway?->enable) {
                throw new RuleValidationException('支付方式无效');
            }

            if ($payway->china_only) {
                $isoCode = get_ip_country($request->getClientIp());
                if($isoCode != 'CN') {
                    throw new RuleValidationException(__('dujiaoka.prompt.payment_china_only'));
                }
            }

            // 计算折扣金额
            $originalTotalPrice = $totalPrice / $userDiscountRate;
            $userDiscountAmount = $originalTotalPrice - $totalPrice;
            
            // 处理余额支付
            $balanceUsed = 0;
            $useBalance = $validated['use_balance'] ?? false;
            $paymentMethod = Order::PAYMENT_ONLINE;
            
            if ($useBalance && $user && $user->balance > 0) {
                $balanceAmount = min($user->balance, $totalPrice);
                if (isset($validated['balance_amount']) && $validated['balance_amount'] > 0) {
                    $balanceAmount = min($validated['balance_amount'], $user->balance, $totalPrice);
                }
                
                if ($balanceAmount > 0) {
                    $balanceUsed = $balanceAmount;
                    $totalPrice -= $balanceAmount;
                    
                    if ($totalPrice <= 0) {
                        $paymentMethod = Order::PAYMENT_BALANCE;
                        $totalPrice = 0;
                    } else {
                        $paymentMethod = Order::PAYMENT_MIXED;
                    }
                }
            }

            $order = Order::create([
                'order_sn' => $orderSn,
                'user_id' => $userId,
                'email' => $validated['email'],
                'total_price' => $originalTotalPrice,
                'actual_price' => $totalPrice,
                'coupon_discount_price' => 0, // 暂时不支持优惠券
                'user_discount_rate' => $userDiscountRate,
                'user_discount_amount' => $userDiscountAmount,
                'payment_method' => $paymentMethod,
                'balance_used' => $balanceUsed,
                'status' => $totalPrice <= 0 ? Order::STATUS_PENDING : Order::STATUS_WAIT_PAY,
                'pay_id' => $validated['payway'],
                'search_pwd' => $validated['search_pwd'] ?? '',
                'buy_ip' => $request->getClientIp(),
            ]);
            
            // 如果使用了余额，扣除用户余额
            if ($balanceUsed > 0 && $user) {
                $user->deductBalance($balanceUsed, 'consume', '订单消费', $orderSn);
            }

            // 创建订单项
            foreach ($orderItems as $itemData) {
                $order->orderItems()->create($itemData);
            }

            // 如果是下单即减库存模式，锁定库存
            if ($stockMode == 1) {
                foreach ($cartItems as $item) {
                    CacheManager::lockStock($item['sub_id'], $item['quantity'], $orderSn);
                }
            }

            DB::commit();
            $this->queueCookie($order->order_sn);
            
            return response()->json([
                'success' => true,
                'redirect' => url('/order/bill/' . $order->order_sn)
            ]);

        } catch (RuleValidationException $exception) {
            DB::rollBack();
            // 如果是下单即减库存模式，释放可能已锁定的库存
            if (isset($stockMode) && $stockMode == 1 && isset($orderSn)) {
                CacheManager::unlockStock($orderSn);
            }
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            // 如果是下单即减库存模式，释放可能已锁定的库存
            if (isset($stockMode) && $stockMode == 1 && isset($orderSn)) {
                CacheManager::unlockStock($orderSn);
            }
            return response()->json([
                'success' => false,
                'message' => '订单创建失败，请重试',
                'debug' => config('app.debug') ? [
                    'trace' => $exception->getTraceAsString(),
                    'file' => $exception->getFile() . ':' . $exception->getLine()
                ] : null
            ]);
        }
    }

    /**
     * 设置订单cookie.
     * @param string $orderSN 订单号.
     */
    private function queueCookie(string $orderSN) : void
    {
        // 设置订单cookie
        $cookies = Cookie::get('dujiaoka_orders');
        if (empty($cookies)) {
            Cookie::queue('dujiaoka_orders', json_encode([$orderSN]));
        } else {
            $cookies = json_decode($cookies, true);
            array_push($cookies, $orderSN);
            Cookie::queue('dujiaoka_orders', json_encode($cookies));
        }
    }

    /**
     * 结账
     */
    public function bill(string $orderSN)
    {
        $order = Order::with(['orderItems', 'pay'])->where('order_sn', $orderSN)->first();
        
        if (empty($order)) {
            return $this->err(__('dujiaoka.prompt.order_does_not_exist'));
        }
        if ($order->status == Order::STATUS_EXPIRED) {
            return $this->err(__('dujiaoka.prompt.order_is_expired'));
        }
        
        // 准备视图数据，保持兼容原有的变量结构
        $data = [
            // 订单商品项
            'orderItems' => $order->orderItems,
            
            // 订单基本信息
            'order_sn' => $order->order_sn,
            'email' => $order->email,
            'actual_price' => $order->actual_price,
            'total_price' => $order->total_price,
            'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            
            // 支付信息
            'pay' => $order->pay,
            
            // 商品类型（从第一个商品项获取）
            'type' => $order->orderItems->first()->type ?? 1,
            
            // 优惠券和折扣信息（当前订单结构中暂无，设为默认值）
            'coupon_discount_price' => $order->coupon_discount_price ?? 0,
            'wholesale_discount_price' => 0,
            'coupon' => null
        ];
        
        return $this->render('static_pages/bill', $data, __('dujiaoka.page-title.bill'));
    }


    /**
     * 订单状态监测
     *
     * @param string $orderSN 订单号
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function checkOrderStatus(string $orderSN)
    {
        $order = $this->orderService->detailOrderSN($orderSN);
        // 订单不存在或者已经过期
        if (!$order || $order->status == Order::STATUS_EXPIRED) {
            return response()->json(['msg' => 'expired', 'code' => 400001]);
        }
        // 订单已经支付
        if ($order->status == Order::STATUS_WAIT_PAY) {
            return response()->json(['msg' => 'wait....', 'code' => 400000]);
        }
        // 成功
        if ($order->status > Order::STATUS_WAIT_PAY) {
            return response()->json(['msg' => 'success', 'code' => 200]);
        }
    }

    /**
     * 通过订单号展示订单详情
     *
     * @param string $orderSN 订单号.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     */
    public function detailOrderSN(string $orderSN)
    {
        $order = $this->orderService->detailOrderSN($orderSN);
        // 订单不存在或者已经过期
        if (!$order) {
            return $this->err(__('dujiaoka.prompt.order_does_not_exist'));
        }
        return $this->render('static_pages/orderinfo', ['orders' => [$order]], __('dujiaoka.page-title.order-detail'));
    }

    /**
     * 订单号查询
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     */
    public function searchOrderBySN(Request $request)
    {
        return $this->detailOrderSN($request->input('order_sn'));
    }

    /**
     * 通过邮箱查询
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     */
    public function searchOrderByEmail(Request $request)
    {
        if (
            !$request->has('email') ||
            (
                cfg('is_open_search_pwd', \App\Models\BaseModel::STATUS_CLOSE) == \App\Models\BaseModel::STATUS_OPEN &&
                !$request->has('search_pwd')
            )
        ) {
            return $this->err(__('dujiaoka.prompt.server_illegal_request'));
        }
        $orders = $this->orderService->withEmailAndPassword($request->input('email'), $request->input('search_pwd',''));
        if (!$orders) {
            return $this->err(__('dujiaoka.prompt.no_related_order_found'));
        }
        return $this->render('static_pages/orderinfo', ['orders' => $orders], __('dujiaoka.page-title.order-detail'));
    }

    /**
     * 通过浏览器缓存查询
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     */
    public function searchOrderByBrowser(Request $request)
    {
        $cookies = Cookie::get('dujiaoka_orders');
        if (empty($cookies)) {
            return $this->err(__('dujiaoka.prompt.no_related_order_found_for_cache'));
        }
        $orderSNS = json_decode($cookies, true);
        $orders = $this->orderService->byOrderSNS($orderSNS);
        return $this->render('static_pages/orderinfo', ['orders' => $orders], __('dujiaoka.page-title.order-detail'));
    }

    /**
     * 订单查询页
     *
     * @param Request $request
     * @return mixed
     *
     */
    public function orderSearch(Request $request)
    {
        return $this->render('static_pages/searchOrder', [], __('dujiaoka.page-title.order-search'));
    }

}
