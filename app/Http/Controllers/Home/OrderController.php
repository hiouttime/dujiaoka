<?php

namespace App\Http\Controllers\Home;

use App\Exceptions\RuleValidationException;
use App\Http\Controllers\BaseController;
use App\Models\Order;
use App\Models\Goods;
use App\Models\Carmis;
use App\Models\Pay;
use App\Services\OrderProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;


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

            $validated = $request->validate([
                'email' => 'required|email',
                'payway' => 'required|integer',
                'search_pwd' => 'nullable|string',
                'cart_items' => 'required|array',
                'cart_items.*.goods_id' => 'required|integer',
                'cart_items.*.sub_id' => 'required|integer', 
                'cart_items.*.quantity' => 'required|integer|min:1'
            ]);

            $totalPrice = 0;
            $orderItems = [];

            foreach ($cartItems as $item) {
                $goods = Goods::with('goods_sub')->find($item['goods_id']);
                if (!$goods || !$goods->is_open) {
                    throw new RuleValidationException("商品不存在或已下架");
                }

                $sub = $goods->goods_sub()->find($item['sub_id']);
                if (!$sub) {
                    throw new RuleValidationException("商品规格不存在");
                }

                $stock = $goods->type == Goods::AUTOMATIC_DELIVERY 
                    ? Carmis::where('sub_id', $item['sub_id'])->where('status', 1)->count()
                    : $sub->stock;

                if ($item['quantity'] > $stock) {
                    throw new RuleValidationException("{$goods->gd_name} 库存不足");
                }

                if ($goods->buy_limit_num > 0 && $item['quantity'] > $goods->buy_limit_num) {
                    throw new RuleValidationException("{$goods->gd_name} 超出限购数量");
                }

                $subtotal = $sub->price * $item['quantity'];
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
                    'unit_price' => $sub->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                    'type' => $goods->type,
                    'info' => $infoHtml
                ];
            }

            $payway = Pay::find($validated['payway']);
            if (!$payway || !$payway->is_open) {
                throw new RuleValidationException('支付方式无效');
            }

            if ($payway->china_only) {
                $isoCode = get_ip_country($request->getClientIp());
                if($isoCode != 'CN') {
                    throw new RuleValidationException(__('dujiaoka.prompt.payment_china_only'));
                }
            }

            $orderSn = 'DJ' . date('YmdHis') . mt_rand(1000, 9999);

            $order = Order::create([
                'order_sn' => $orderSn,
                'email' => $validated['email'],
                'total_price' => $totalPrice,
                'actual_price' => $totalPrice,
                'status' => Order::STATUS_WAIT_PAY,
                'pay_id' => $validated['payway'],
                'search_pwd' => $validated['search_pwd'] ?? '',
                'buy_ip' => $request->getClientIp(),
            ]);

            foreach ($orderItems as $itemData) {
                $order->orderItems()->create($itemData);
            }

            DB::commit();
            $this->queueCookie($order->order_sn);
            
            return response()->json([
                'success' => true,
                'redirect' => url('/order/bill/' . $order->order_sn)
            ]);

        } catch (RuleValidationException $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => '订单创建失败，请重试'
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
