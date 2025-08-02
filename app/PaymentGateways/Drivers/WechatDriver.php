<?php

namespace App\PaymentGateways\Drivers;

use App\PaymentGateways\AbstractPaymentDriver;
use Illuminate\Http\Request;
use Yansongda\Pay\Pay;
use App\Models\Order;
use App\Models\Pay as PayModel;

class WechatDriver extends AbstractPaymentDriver
{
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
            
            if (!$payGateway || $payGateway->pay_handleroute !== '/pay/wechat') {
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

    public function verify(array $config, Request $request): array
    {
        $pay = Pay::wechat($config);
        $result = $pay->verify();

        if ($result->return_code === 'SUCCESS' && $result->result_code === 'SUCCESS') {
            return [
                'status' => 'success',
                'out_trade_no' => $result->out_trade_no,
                'total_amount' => $result->total_fee / 100,
                'trade_no' => $result->transaction_id,
            ];
        }

        return ['status' => 'failed'];
    }

    public function getSupportedPayways(): array
    {
        return ['wxpay', 'wxscan', 'wxh5', 'wxapp'];
    }

    public function getName(): string
    {
        return 'wechat';
    }

    public function getDisplayName(): string
    {
        return '微信支付';
    }

    protected function buildConfig(): array
    {
        return [
            'app_id' => $this->payGateway->merchant_id,
            'mch_id' => $this->payGateway->merchant_key,
            'key' => $this->payGateway->merchant_pem,
            'notify_url' => $this->getNotifyUrl(),
            'http' => [
                'timeout' => 10.0,
                'connect_timeout' => 10.0,
            ],
        ];
    }

    protected function buildConfigFromGateway(PayModel $payGateway): array
    {
        return [
            'app_id' => $payGateway->merchant_id,
            'mch_id' => $payGateway->merchant_key,
            'key' => $payGateway->merchant_pem,
        ];
    }

    protected function processPayway(string $payway, array $config, array $orderData)
    {
        switch ($payway) {
            case 'wxscan':
                return $this->handleScanPay($config, $orderData);
            
            case 'wxh5':
                return $this->handleH5Pay($config, $orderData);
            
            case 'wxapp':
                return $this->handleAppPay($config, $orderData);
                
            default:
                return $this->err(__('dujiaoka.prompt.payment_method_not_supported'));
        }
    }

    protected function handleScanPay(array $config, array $orderData)
    {
        $result = Pay::wechat($config)->scan($orderData);
        
        return $this->render('static_pages/qrpay', [
            'payname' => $this->order->order_sn,
            'actual_price' => (float)$this->order->actual_price,
            'orderid' => $this->order->order_sn,
            'jump_payuri' => $result->code_url,
        ], __('dujiaoka.scan_qrcode_to_pay'));
    }

    protected function handleH5Pay(array $config, array $orderData)
    {
        $orderData['scene_info'] = [
            'h5_info' => [
                'type' => 'Wap',
                'wap_url' => request()->getHost(),
                'wap_name' => '商城支付'
            ]
        ];
        
        $result = Pay::wechat($config)->wap($orderData);
        return redirect($result->mweb_url);
    }

    protected function handleAppPay(array $config, array $orderData)
    {
        $result = Pay::wechat($config)->app($orderData);
        return response()->json($result->toArray());
    }
}