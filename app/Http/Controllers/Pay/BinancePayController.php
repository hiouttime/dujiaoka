<?php
namespace App\Http\Controllers\Pay;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\PayController;
use GuzzleHttp\Exception\GuzzleException;
use App\Exceptions\RuleValidationException;

class BinancePayController extends PayController
{
    
    private $server = "https://bpay.binanceapi.com";
    private $timestamp = 0;
    private $noce = "";
    
    const FAIL = "FAIL";
    const SUCCESS = "SUCCESS";
    
    public function gateway(string $payway, string $orderSN)
    {
        try {
            // 加载网关
            $this->loadGateWay($orderSN, $payway);
            $endpoint = "/binancepay/openapi/v2/order";
            $this->timestamp = round(microtime(true) * 1000);
            
            // 判断用户终端类型
            $client = 'WEB';
            if(app('Jenssegers\Agent')->isMobile())
                $client = 'WAP';
            
            $data = [
                "env" => [
                    "terminalType" => $client,
                    "orderClientIp" => $this->order->buy_ip
                    ],
                "merchantTradeNo" => $this->order->order_sn . "M",
                "orderAmount" => (float)$this->order->actual_price,
                "currency" => $this->payGateway->merchant_id,
                "goods" => [
                    "goodsType" => "02",
                    "goodsCategory" => "Z000",
                    "referenceGoodsId" => $this->order->goods_id,
                    "goodsName" => $this->purgeString($this->order->title)
                    ],
                "buyer" => [
                    "buyerEmail" => $this->order->email
                    ],
                "returnUrl" => url('detail-order-sn', ['orderSN' => $this->order->order_sn]),
                "orderExpireTime" => $this->timestamp + dujiaoka_config_get('order_expire_time', 5) * 60 * 1000,
                "webhookUrl" => url($this->payGateway->pay_handleroute . '/notify_url')
                ];
            $this->noce = Str::random(32, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

            $client = new Client([
				'headers' => [ 
				    'Content-Type' => 'application/json',
				    'BinancePay-Timestamp' => $this->timestamp,
				    'BinancePay-Nonce' => $this->noce,
				    'BinancePay-Certificate-SN' => $this->payGateway->merchant_key,
				    'BinancePay-Signature' => $this->paramSign($data)
				    ]
			]);
            $response = $client->post($this->server.$endpoint, ['body' =>  json_encode($data)]);
            $body = json_decode($response->getBody()->getContents(), true);
            if (!isset($body['status']) || $body['status'] != "SUCCESS") {
                return $this->err(__('dujiaoka.prompt.abnormal_payment_channel') . $body['errorMessage']);
            }
            return redirect()->away($body['checkoutUrl']);
        } catch (RuleValidationException $exception) {
        } catch (GuzzleException $exception) {
            return $this->err($exception->getMessage());
        }
    }

    private function paramSign(array $params){
        $payload = $this->timestamp . "\n" . $this->noce . "\n" . json_encode($params) . "\n";
        $hash = hash_hmac('sha512', $payload, $this->payGateway->merchant_pem, true);
        return strtoupper(bin2hex($hash));
    }

    public function notifyUrl(Request $request)
    {
        $data = $request->json()->all();
        $endpoint = "/binancepay/openapi/certificates";
        /*
        $order = $this->orderService->detailOrderSN($data['data']['merchantTradeNo']);
        if (!$order) // 订单不存在
            return $this->returnResult(self::FAIL);
    
        $payGateway = $this->payService->detail($order->pay_id);
        if (!$payGateway) // 支付方式不存在
            return $this->returnResult(self::FAIL);
        
        if($payGateway->pay_handleroute.'/notify_url' != $request->path())// 来路非法
            return $this->returnResult(self::FAIL);
        
        //合法的数据
        $this->timestamp = intval($request->header('BinancePay-Timestamp'));
        $this->noce = intval($request->header('BinancePay-Nonce'));
        */
        $this->timestamp = round(microtime(true) * 1000);
        $client = new Client([
		    'headers' => [
			    'Content-Type' => 'application/json',
			    'BinancePay-Timestamp' => $this->timestamp,
			    //'BinancePay-Certificate-SN' => $payGateway->merchant_key,
			    ]
		]);
        $response = $client->post($this->server.$endpoint, ['body' =>  json_encode($data)]);
        $body = json_decode($response->getBody()->getContents(), true);
        var_dump($body);
        exit();

        if ($request->header('BinancePay-Signature') != $this->paramSign($data['data'])) { //不合法的数据
            return 'fail';  //返回失败 继续补单
        } else {
            //合法的数据
            //业务处理
            $this->orderProcessService->completedOrder($data['OutOrderId'], $data['ActualAmount'], $data['Id']);
            return 'ok';
        }
    }
    
    private function returnResult($code){
        return json_encode(["returnCode" => $code]);
    }

    public function returnUrl(Request $request)
    {
        $oid = $request->get('order_id');
        // 异步通知还没到就跳转了，所以这里休眠2秒
        sleep(2);
        return redirect(url('detail-order-sn', ['orderSN' => $oid]));
    }
    
    private function purgeString($str){
        $str = preg_replace('/[^\p{L}\p{N}\s]/u', '', $str);
        $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');// 去掉emoji
        return $str;
    }

}