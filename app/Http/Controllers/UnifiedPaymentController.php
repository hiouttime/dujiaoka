<?php

namespace App\Http\Controllers;

use App\PaymentGateways\PaymentManager;
use App\Exceptions\RuleValidationException;
use Illuminate\Http\Request;

/**
 * 统一支付控制器
 * 替代原来分散的支付控制器
 */
class UnifiedPaymentController extends Controller
{
    protected PaymentManager $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    /**
     * 支付网关入口
     */
    public function gateway(string $driver, string $payway, string $orderSN)
    {
        try {
            if (!$this->paymentManager->hasDriver($driver)) {
                throw new RuleValidationException(__('dujiaoka.prompt.payment_driver_not_found'));
            }

            $paymentDriver = $this->paymentManager->driver($driver);
            
            // 加载订单和支付网关信息
            $orderService = app('App\\Services\\Orders');
            $order = $orderService->detailOrderSN($orderSN);
            
            if (!$order) {
                throw new RuleValidationException(__('dujiaoka.prompt.order_does_not_exist'));
            }

            $payService = app('App\\Services\\Payment');
            $payGateway = $payService->detail($order->pay_id);
            
            if (!$payGateway) {
                throw new RuleValidationException(__('dujiaoka.prompt.abnormal_payment_channel'));
            }

            return $paymentDriver->gateway($payway, $orderSN, $order, $payGateway);

        } catch (RuleValidationException $exception) {
            return view('common.error', ['message' => $exception->getMessage()]);
        } catch (\Exception $exception) {
            return view('common.error', ['message' => __('dujiaoka.prompt.system_error')]);
        }
    }

    /**
     * 支付异步通知
     */
    public function notify(Request $request, string $driver)
    {
        try {
            if (!$this->paymentManager->hasDriver($driver)) {
                return 'error';
            }

            $paymentDriver = $this->paymentManager->driver($driver);
            return $paymentDriver->notify($request);

        } catch (\Exception $exception) {
            \Log::error("Payment notify error for driver {$driver}: " . $exception->getMessage());
            return 'error';
        }
    }

    /**
     * 支付返回页面
     */
    public function return(Request $request, string $driver)
    {
        try {
            if (!$this->paymentManager->hasDriver($driver)) {
                return redirect()->route('home')->with('error', __('dujiaoka.prompt.payment_driver_not_found'));
            }

            // 大多数支付驱动的返回处理都是跳转到订单详情页
            $orderSN = $request->input('orderSN') ?? $request->input('out_trade_no');
            
            if ($orderSN) {
                return redirect()->route('detail-order-sn', ['orderSN' => $orderSN]);
            }

            return redirect()->route('home');

        } catch (\Exception $exception) {
            return redirect()->route('home')->with('error', __('dujiaoka.prompt.system_error'));
        }
    }

    /**
     * 获取所有支付驱动信息（用于管理后台）
     */
    public function getDriversInfo()
    {
        return response()->json([
            'drivers' => $this->paymentManager->getAllDriversInfo()
        ]);
    }
}