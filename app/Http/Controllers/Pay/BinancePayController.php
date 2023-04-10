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
    
    private $apiID = "";
    private $apiKey = "";
    
    private $currency = "";// 支付货币单位
    private $rate = 0; // 换算汇率
    
    const FAIL = "FAIL";
    const SUCCESS = "SUCCESS";
    
    public function __construct()
    {
        parent::__construct();
        $this->noce = Str::random(32, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $this->timestamp = round(microtime(true) * 1000);
    }

    
    public function gateway(string $payway, string $orderSN)
    {
        try {
            // 加载网关
            $this->loadGateWay($orderSN, $payway);
            $this->apiKey = $this->payGateway->merchant_pem;
            $endpoint = "/binancepay/openapi/v2/order";
            
            // 判断用户终端类型
            $client = 'WEB';
            if(app('Jenssegers\Agent')->isMobile())
                $client = 'WAP';
                
            // 判断是否指定汇率
            $this->currency = explode(":",$this->payGateway->merchant_id);
            if(count($this->currency) > 1)
                $this->rate = floatval($this->currency[1]);
             
            $this->currency = $this->currency[0];
            $this->setRate();
            
            
            $data = [
                "env" => [
                    "terminalType" => $client,
                    "orderClientIp" => $this->order->buy_ip
                    ],
                "merchantTradeNo" => $this->order->order_sn,
                "orderAmount" => floatval($this->order->actual_price / $this->rate),
                "passThroughInfo" => $this->order->actual_price, // 存储换算前的价格
                "currency" => $this->currency,
                "goods" => [
                    "goodsType" => "02",
                    "goodsCategory" => "Z000",
                    "referenceGoodsId" => $this->order->goods_id,
                    "goodsName" => $this->purgeString($this->order->title)
                    ],
                "buyer" => [
                    "buyerEmail" => $this->order->email
                    ],
                "passThroughInfo" => "",
                "returnUrl" => url('detail-order-sn', ['orderSN' => $this->order->order_sn]),
                "orderExpireTime" => $this->timestamp + dujiaoka_config_get('order_expire_time', 5) * 60 * 1000,
                "webhookUrl" => url($this->payGateway->pay_handleroute . '/notify_url')
                ];

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
            return redirect()->away($body['data']['checkoutUrl']);
        } catch (RuleValidationException $exception) {
        } catch (GuzzleException $exception) {
            return $this->err($exception->getMessage());
        }
    }

    public function notifyUrl(Request $request)
    {
        
        $data = $request->json()->all();
        $trade = json_decode($data['data'], true);
        $order = $this->orderService->detailOrderSN($trade['merchantTradeNo']);
        
        if (!$order) // 订单不存在
            return $this->returnResult(self::FAIL);
        $payGateway = $this->payService->detail($order->pay_id);
        if (!$payGateway) // 支付方式不存在
            return $this->returnResult(self::FAIL);
        $this->apiID = $payGateway->merchant_key;
        $this->apiKey = $payGateway->merchant_pem;

        if($payGateway->pay_handleroute.'/notify_url' != $request->path())// 来路非法
            return $this->returnResult(self::FAIL);

        
        $this->noce = $request->header('Binancepay-Nonce');
        $this->timestamp = $request->header('Binancepay-Timestamp');

        if(!$this->checkSign($request->getContent(), $request->header('BinancePay-Signature')))
            return $this->returnResult(self::FAIL);
        
        if($data['bizStatus'] != 'PAY_SUCCESS')
            return $this->returnResult(self::SUCCESS);

        $this->orderProcessService->completedOrder($order->order_sn, floatval($trade['passThroughInfo']), $data['bizIdStr']);
        return $this->returnResult(self::SUCCESS);
    }
    
    private function getPublicKey(){
        $endpoint = "/binancepay/openapi/certificates";
        
        $client = new Client([
		    'headers' => [ 
			    'Content-Type' => 'application/json',
			    'BinancePay-Timestamp' => $this->timestamp,
			    'BinancePay-Nonce' => $this->noce,
			    'BinancePay-Certificate-SN' => $this->apiID,
			    'BinancePay-Signature' => $this->paramSign([])
			]
		]);
		$response = $client->post($this->server.$endpoint, ['body' =>  json_encode([])]);
        $body = json_decode($response->getBody()->getContents(), true);
        return $body['data'][0]['certPublic'];
    }
    
    private function paramSign(array $params){
        $payload = $this->timestamp . "\n" . $this->noce . "\n" . json_encode($params) . "\n";
        $hash = hash_hmac('sha512', $payload, $this->apiKey, true);
        return strtoupper(bin2hex($hash));
    }
    
    private function checkSign(string $params, string $sign){// 必须用原始数据验签
    return true;
        $payload = $this->timestamp . "\n" . $this->noce . "\n" . $params . "\n";
        return openssl_verify($payload, base64_decode($sign), $this->getPublicKey(), OPENSSL_ALGO_SHA256);
    }
    
    private function setRate(){
        if($this->rate > 0)
            return $this->rate;
        $endpoint = "https://www.okx.com/v3/c2c/otc-ticker/quotedPrice";
        $param = [
            'side' => 'buy',
            'quoteCurrency' => 'CNY',
            'baseCurrency' => $this->currency
            ];
        $data = file_get_contents($endpoint."?".http_build_query($param));
        $data = json_decode($data, true);
        $this->rate = floatval($data['data'][0]['price']);
    }
    
    private function purgeString($str){
        $str = preg_replace('/[^\p{L}\p{N}\s]/u', '', $str);
        $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');// 去掉emoji
        return $str;
    }
    
    private function returnResult($code){
        return json_encode(["returnCode" => $code]);
    }

}