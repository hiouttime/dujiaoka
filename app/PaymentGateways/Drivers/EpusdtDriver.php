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
 * Epusdt支付驱动
 */
class EpusdtDriver extends AbstractPaymentDriver
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
                "amount" => (float)$this->order->actual_price,
                "order_id" => $this->order->order_sn,
                'redirect_url' => url('/pay/epusdt/return?orderSN=' . $this->order->order_sn),
                'notify_url' => url('/pay/epusdt/notify'),
            ];
            
            $parameter['signature'] = $this->generateSignature($parameter, $this->payGateway->merchant_id);
            
            $client = new Client([
                'headers' => ['Content-Type' => 'application/json']
            ]);
            
            $response = $client->post($this->payGateway->merchant_pem, [
                'body' => json_encode($parameter)
            ]);
            
            $body = json_decode($response->getBody()->getContents(), true);
            
            if (!isset($body['status_code']) || $body['status_code'] != 200) {
                throw new RuleValidationException(__('dujiaoka.prompt.abnormal_payment_channel') . ': ' . ($body['message'] ?? ''));
            }
            
            return redirect()->away($body['data']['payment_url']);
            
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
        
        if (empty($data) || !isset($data['order_id'])) {
            return 'fail';
        }

        $orderService = app('App\\Services\\Orders');
        $order = $orderService->detailOrderSN($data['order_id']);
        
        if (!$order) {
            return 'fail';
        }

        $payService = app('App\\Services\\Payment');
        $payGateway = $payService->detail($order->pay_id);
        
        if (!$payGateway || $payGateway->pay_handleroute != 'epusdt') {
            return 'fail';
        }

        // 验证签名
        $signature = $this->generateSignature($data, $payGateway->merchant_id);
        if ($data['signature'] != $signature) {
            return 'fail';
        }

        // 处理支付结果
        if (isset($data['status']) && $data['status'] == 2) {
            $orderService->completedOrder($data['order_id'], $data['amount'], $data['trade_id']);
        }

        return 'ok';
    }

    /**
     * 验证支付结果
     */
    public function verify(array $config, Request $request): array
    {
        return [];
    }

    /**
     * 获取支持的支付方式
     */
    public function getSupportedPayways(): array
    {
        return ['epusdt-trc20'];
    }

    /**
     * 获取驱动名称
     */
    public function getName(): string
    {
        return 'epusdt';
    }

    /**
     * 获取驱动显示名称
     */
    public function getDisplayName(): string
    {
        return 'Epusdt USDT支付';
    }

    /**
     * 生成签名
     */
    private function generateSignature(array $parameter, string $token): string
    {
        ksort($parameter);
        $signStr = '';
        
        foreach ($parameter as $key => $value) {
            if ($key !== 'signature' && $value !== '') {
                $signStr .= $key . '=' . $value . '&';
            }
        }
        
        $signStr = rtrim($signStr, '&');
        return md5($signStr . $token);
    }
}