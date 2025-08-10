<?php

namespace App\Http\Controllers\Home;

use App\Exceptions\RuleValidationException;
use App\Http\Controllers\BaseController;
use App\Models\Order;
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
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Validation\ValidationException
     *
     */
    public function createOrder(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->orderService->validatorCreateOrder($request);
            $goods = $this->orderService->validatorGoods($request);
            $subid = $request->input('sub_id');
            if(!$subid)
                throw new RuleValidationException(__('dujiaoka.prompt.need_sub_id'));
            $goods_sub = $goods->goods_sub()->where('id', $subid);
            if(!$goods_sub->exists())
                throw new RuleValidationException(__('dujiaoka.prompt.server_illegal_request'));
            $goods_sub = $goods_sub->first();
            $goods->price = $goods_sub->price;
            $goods->gd_name = $goods->gd_name . " [$goods_sub->name]";
            $this->orderService->validatorLoopCarmis($request);
            // 设置商品
            $this->orderProcessService->setGoods($goods);
            // 优惠码
            $coupon = $this->orderService->validatorCoupon($request);
            // 设置优惠码
            $this->orderProcessService->setCoupon($coupon);
            $otherIpt = $this->orderService->validatorChargeInput($goods, $request);
            $this->orderProcessService->setOtherIpt($otherIpt);
            //设置预选卡密
            $carmi_id = intval($request->input('carmi_id'));
            $this->orderProcessService->setCarmi($carmi_id);
            // 数量，如果预选了卡密，则数量只能是1
            $this->orderProcessService->setBuyAmount($carmi_id ? 1 : $request->input('by_amount'));
            // 支付方式
            $payment_limit = json_decode($goods->payment_limit,true);
            if(is_array($payment_limit) && count($payment_limit) && !in_array($request->input('payway'),$payment_limit))
                return $this->err(__('dujiaoka.prompt.payment_limit'));
            // 支付方式适用检查
            $payway = Pay::find($request->input('payway'));
            if ($payway->china_only) {
                $isoCode = get_ip_country($request->getClientIp());
                if($isoCode != 'CN')
                     return $this->err(__('dujiaoka.prompt.payment_china_only'));
            }
            $this->orderProcessService->setPayID($request->input('payway'));
            // 下单邮箱
            $this->orderProcessService->setEmail($request->input('email'));
            // ip地址
            $this->orderProcessService->setBuyIP($request->getClientIp());
            // 查询密码
            $this->orderProcessService->setSearchPwd($request->input('search_pwd', ''));
            // 商品规格ID
            $this->orderProcessService->setSubID($request->input('sub_id', 0));
            // 创建订单
            $order = $this->orderProcessService->createOrder();
            DB::commit();
            // 设置订单cookie
            $this->queueCookie($order->order_sn);
            return redirect(url('/bill', ['orderSN' => $order->order_sn]));
        } catch (RuleValidationException $exception) {
            DB::rollBack();
            return $this->err($exception->getMessage());
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
     *
     * @param string $orderSN
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     */
    public function bill(string $orderSN)
    {
        $order = $this->orderService->detailOrderSN($orderSN);
        if (empty($order)) {
            return $this->err(__('dujiaoka.prompt.order_does_not_exist'));
        }
        if ($order->status == Order::STATUS_EXPIRED) {
            return $this->err(__('dujiaoka.prompt.order_is_expired'));
        }
        if ($order->carmi_id){
            $order->preselection = Carmis::find($order->carmi_id)->info;
        }
        return $this->render('static_pages/bill', $order, __('dujiaoka.page-title.bill'));
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
