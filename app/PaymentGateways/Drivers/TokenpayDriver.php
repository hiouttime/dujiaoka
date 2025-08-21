<?php

namespace App\PaymentGateways\Drivers;

use App\PaymentGateways\AbstractPaymentDriver;
use App\Exceptions\RuleValidationException;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Pay;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * TokenPay支付驱动
 */
class TokenpayDriver extends AbstractPaymentDriver
{
    /**
     * 支付网关处理
     */
    public function gateway(string $payway, string $orderSN, Order $order, Pay $payGateway)
    {
        try {
            $this->order = $order;
            $this->payGateway = $payGateway;

            // 验证支付方式
            if (!in_array($payway, $this->getSupportedPayways())) {
                throw new RuleValidationException(__('dujiaoka.prompt.payment_method_not_supported'));
            }

            // 构造请求参数
            $parameter = [
                "ActualAmount" => (float)$this->order->actual_price,
                "OutOrderId" => $this->order->order_sn, 
                "OrderUserKey" => $this->order->email, 
                "Currency" => $this->payGateway->merchant_id,
                'RedirectUrl' => url('/pay/tokenpay/return?orderSN=' . $this->order->order_sn),
                'NotifyUrl' => url('/pay/tokenpay/notify'),
            ];
            
            $parameter['Signature'] = $this->generateSignature($parameter, $this->payGateway->merchant_key);
            
            $client = new Client([
                'headers' => ['Content-Type' => 'application/json']
            ]);
            
            $response = $client->post($this->payGateway->merchant_pem . '/CreateOrder', [
                'body' => json_encode($parameter)
            ]);
            
            $body = json_decode($response->getBody()->getContents(), true);
            
            if (!isset($body['success']) || $body['success'] != true) {
                throw new RuleValidationException(__('dujiaoka.prompt.abnormal_payment_channel') . ': ' . ($body['message'] ?? ''));
            }
            
            return redirect()->away($body['data']);
            
        } catch (RuleValidationException $exception) {
            throw $exception;
        } catch (GuzzleException $exception) {
            throw new RuleValidationException($exception->getMessage());
        }
    }

    /**
     * 异步通知处理
     */
    public function notify(Request $request): string
    {
        $data = $request->all();
        
        if (empty($data) || !isset($data['OutOrderId'])) {
            return 'fail';
        }

        $orderService = app('App\\Services\\Orders');
        $order = $orderService->detailOrderSN($data['OutOrderId']);
        
        if (!$order) {
            return 'fail';
        }

        $payService = app('App\\Services\\Payment');
        $payGateway = $payService->detail($order->pay_id);
        
        if (!$payGateway || $payGateway->pay_handleroute != 'tokenpay') {
            return 'fail';
        }

        // 验证签名
        $signature = $this->generateSignature($data, $payGateway->merchant_key);
        if ($data['Signature'] != $signature) {
            return 'fail';
        }

        // 处理支付结果
        if (isset($data['Status']) && $data['Status'] == 'TRADE_SUCCESS') {
            $orderService->completedOrder($data['OutOrderId'], $data['ActualAmount'], $data['OutOrderId']);
        }

        return 'success';
    }

    /**
     * 验证支付结果
     */
    public function verify(array $config, Request $request): array
    {
        // TokenPay通过异步通知验证
        return [];
    }

    /**
     * 获取支持的支付方式
     */
    public function getSupportedPayways(): array
    {
        return [
            'tokenpay-usdt-trc',
            'tokenpay-trx',
            'tokenpay-eth',
            'tokenpay-usdt-erc',
            'tokenpay-usdc-erc'
        ];
    }

    /**
     * 获取驱动名称
     */
    public function getName(): string
    {
        return 'tokenpay';
    }

    /**
     * 获取驱动显示名称
     */
    public function getDisplayName(): string
    {
        return 'TokenPay加密货币支付';
    }

    /**
     * 生成签名
     */
    private function generateSignature(array $parameter, string $signKey): string
    {
        ksort($parameter);
        reset($parameter);
        $sign = '';
        
        foreach ($parameter as $key => $val) {
            if ($key != 'Signature') {
                if ($sign != '') {
                    $sign .= "&";
                }
                $sign .= "$key=$val";
            }
        }
        
        return md5($sign . $signKey);
    }
}