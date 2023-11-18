<?php
namespace App\Http\Controllers\Pay;

use GuzzleHttp\Client;
use App\Exceptions\RuleValidationException;
use App\Http\Controllers\PayController;
use Illuminate\Http\Request;

class LingxPayController extends PayController
{
    
    private $gatway = "https://pay.lingpay.vip/api/cashierPage/convertPayway/";

    public function gateway(string $payway, string $orderSN)
    {
        try {
            // 加载网关
            $this->loadGateWay($orderSN, $payway);
            //组装支付参数
            $parameter = [
                "wayCode" => "QR_CASHIER",
                "payDataType" => "codeUrl",
                "buyerRemark" => $this->order->order_sn,  //订单号
                "amount" => (float)$this->order->actual_price
            ];
            $client = new Client();
            $response = $client->post($this->gateway.$this->payGateway->merchant_id, ['body' =>  $parameter]);
            try{
                $body = json_decode($response->getBody()->getContents(), true);
                $result['payname'] = $this->order->order_sn;
                $result['actual_price'] = (float)$this->order->actual_price;
                $result['orderid'] = $this->order->order_sn;
                $result['qr_code'] = $body['data']['payData'];
                return $this->render('static_pages/qrpay', $result, __('dujiaoka.scan_qrcode_to_pay'));
            } catch (\Exception $e) {
                return $this->err(__('dujiaoka.prompt.abnormal_payment_channel') . $e->getMessage());
            }
        } catch (RuleValidationException $exception) {
            return $this->err($exception->getMessage());
        }
    }

    public function notifyUrl(Request $request)
    {
        $data = $request->all();
        $order = $this->orderService->detailOrderSN($data['out_trade_no']);
        if (!$order) {
            return 'fail';
        }
        $payGateway = $this->payService->detail($order->pay_id);
        if (!$payGateway) {
            return 'fail';
        }
        if($payGateway->pay_handleroute != '/pay/yipay'){
            return 'fail';
        }
        ksort($data); //重新排序$data数组
        reset($data); //内部指针指向数组中的第一个元素
        $sign = '';
        foreach ($data as $key => $val) {
            if ($key == "sign" || $key == "sign_type" || $val == "") continue;
            if ($key != 'sign') {
                if ($sign != '') {
                    $sign .= "&";
                }
                $sign .= "$key=$val"; //拼接为url参数形式
            }
        }
        if (!$data['trade_no'] || md5($sign . $payGateway->merchant_pem) != $data['sign']) { //不合法的数据
            return 'fail';  //返回失败 继续补单
        } else {
            //合法的数据
            //业务处理
            $this->orderProcessService->completedOrder($data['out_trade_no'], $data['money'], $data['trade_no']);
            return 'success';
        }
    }

    public function returnUrl(Request $request)
    {
        $oid = $request->get('order_id');
        // 有些易支付太垃了，异步通知还没到就跳转了，导致订单显示待支付，其实已经支付了，所以这里休眠2秒
        sleep(2);
        return redirect(url('detail-order-sn', ['orderSN' => $oid]));
    }

}
