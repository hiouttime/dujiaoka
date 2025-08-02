<?php

namespace App\PaymentGateways\Drivers;

use App\PaymentGateways\AbstractPaymentDriver;
use Illuminate\Http\Request;
use Yansongda\Pay\Pay;
use App\Models\Order;
use App\Models\Pay as PayModel;

/**
 * 支付宝支付驱动
 */
class AlipayDriver extends AbstractPaymentDriver
{
    /**
     * 支付网关处理
     */
    public function gateway(string $payway, string $orderSN, Order $order, PayModel $payGateway)
    {
        try {
            $this->order = $order;
            $this->payGateway = $payGateway;
            $this->validateOrderStatus();

            $config = $this->buildConfig();
            $orderData = $this->getOrderInfo();

            return $this->processPayway($payway, $config, $orderData);

        } catch (\Exception $e) {
            return $this->err(__('dujiaoka.prompt.abnormal_payment_channel') . $e->getMessage());
        }
    }

    /**
     * 异步通知处理
     */
    public function notify(Request $request): string
    {
        try {
            $orderSN = $request->input('out_trade_no');
            $orderService = app('App\\Service\\OrderService');
            $order = $orderService->detailOrderSN($orderSN);
            
            if (!$order) {
                return 'error';
            }

            $payService = app('App\\Service\\PayService');
            $payGateway = $payService->detail($order->pay_id);
            
            if (!$payGateway || $payGateway->pay_handleroute !== '/pay/alipay') {
                return 'error';
            }

            $config = $this->buildConfigFromGateway($payGateway);
            $result = $this->verify($config, $request);

            if ($result['status'] === 'success') {
                $this->processPaymentSuccess(
                    $result['out_trade_no'],
                    $result['total_amount'],
                    $result['trade_no']
                );
            }

            return 'success';
        } catch (\Exception $e) {
            return 'fail';
        }
    }

    /**
     * 验证支付结果
     */
    public function verify(array $config, Request $request): array
    {
        $pay = Pay::alipay($config);
        $result = $pay->verify();

        if ($result->trade_status === 'TRADE_SUCCESS' || $result->trade_status === 'TRADE_FINISHED') {
            return [
                'status' => 'success',
                'out_trade_no' => $result->out_trade_no,
                'total_amount' => $result->total_amount,
                'trade_no' => $result->trade_no,
            ];
        }

        return ['status' => 'failed'];
    }

    /**
     * 获取支持的支付方式
     */
    public function getSupportedPayways(): array
    {
        return ['zfbf2f', 'alipayscan', 'aliweb', 'aliwap'];
    }

    /**
     * 获取驱动名称
     */
    public function getName(): string
    {
        return 'alipay';
    }

    /**
     * 获取驱动显示名称
     */
    public function getDisplayName(): string
    {
        return '支付宝';
    }

    /**
     * 构建支付配置
     */
    protected function buildConfig(): array
    {
        return [
            'app_id' => $this->payGateway->merchant_id,
            'ali_public_key' => $this->payGateway->merchant_key,
            'private_key' => $this->payGateway->merchant_pem,
            'notify_url' => $this->getNotifyUrl(),
            'return_url' => $this->getReturnUrl($this->order->order_sn),
            'http' => [
                'timeout' => 10.0,
                'connect_timeout' => 10.0,
            ],
        ];
    }

    /**
     * 从网关配置构建配置
     */
    protected function buildConfigFromGateway(PayModel $payGateway): array
    {
        return [
            'app_id' => $payGateway->merchant_id,
            'ali_public_key' => $payGateway->merchant_key,
            'private_key' => $payGateway->merchant_pem,
        ];
    }

    /**
     * 处理不同支付方式
     */
    protected function processPayway(string $payway, array $config, array $orderData)
    {
        switch ($payway) {
            case 'zfbf2f':
            case 'alipayscan':
                return $this->handleScanPay($config, $orderData);
            
            case 'aliweb':
                return $this->handleWebPay($config, $orderData);
            
            case 'aliwap':
                return $this->handleWapPay($config, $orderData);
                
            default:
                return $this->err(__('dujiaoka.prompt.payment_method_not_supported'));
        }
    }

    /**
     * 处理扫码支付
     */
    protected function handleScanPay(array $config, array $orderData)
    {
        $result = Pay::alipay($config)->scan($orderData)->toArray();
        
        return $this->render('static_pages/qrpay', [
            'payname' => $this->order->order_sn,
            'actual_price' => (float)$this->order->actual_price,
            'orderid' => $this->order->order_sn,
            'jump_payuri' => $result['qr_code'],
        ], __('dujiaoka.scan_qrcode_to_pay'));
    }

    /**
     * 处理网页支付
     */
    protected function handleWebPay(array $config, array $orderData)
    {
        return Pay::alipay($config)->web($orderData);
    }

    /**
     * 处理手机网页支付
     */
    protected function handleWapPay(array $config, array $orderData)
    {
        return Pay::alipay($config)->wap($orderData);
    }
}