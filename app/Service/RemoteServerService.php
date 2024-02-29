<?php
/**
 * Proccess Monitoring and verifying actions on remote servers.
 *
 * @author   outtime<i@treeo.cn>
 * @copyright outtime<i@treeo.cn>
 * @link      https://outti.me
 */

namespace App\Service;

use App\Models\RemoteServer;
use Illuminate\Support\Facades\Http;

class RemoteServerService

{
    
    static private $order; // 订单对象
    static private $rconServer; //RCON服务器会话
    
    /**
     * 执行远程服务器命令
     *
     * @param int $server 远程服务器ID
     * @param object $order 订单信息
     * @return bool 执行结果
     *
     * @author   outtime<i@treeo.cn>
     * @copyright outtime<i@treeo.cn>
     * @link      https://outti.me
     */
    static public function execute($server, $order):bool{
        self::$order = $order;
        $server = RemoteServer::find($server);
        if(!$server) return false;
        
        switch ($server->type) {
            case RemoteServer::HTTP_SERVER:
                $result = self::sendHTTP(unserialize($server->data));
                break;
            case RemoteServer::RCON_COMMAND:
                $result = self::sendHTTP(unserialize($server->data));
                break;
            case RemoteServer::SQL_EXECUTE:
                $result = self::sendHTTP(unserialize($server->data));
                break;
            default:
                return false;
                break;
        }
        return $result;
        
    }

    /**
     * 格式化命令模板
     *
     * @param string $command 请求参数
     * @return string 填充模板后的字符串
     *
     * @author   outtime<i@treeo.cn>
     * @copyright outtime<i@treeo.cn>
     * @link      https://outti.me
     */
    static private function parse(string $command):string
    {
        $command = str_replace([
            '{$title}',
            '{$gd_id}',
            '{$order_sn}',
            '{$email}',
            '{$price}'
            ],[
                self::$order->title,
                self::$order->goods_id,
                self::$order->order_sn,
                self::$order->email,
                self::$order->actual_price
                ],$command);
        foreach (self::formatInput(self::$order->info) as $key => $value)
            $command = str_replace('{$'.$key.'}',$value,$command);
        return $command;
    }
    /**
     * 格式化其他输入框
     *
     * @param string $input 其他输入框信息
     * @return array 格式化后的数组
     *
     * @author   outtime<i@treeo.cn>
     * @copyright outtime<i@treeo.cn>
     * @link      https://outti.me
     */
    static function formatInput(string $input): ?array
    {
        $format = [];
        $input = explode(PHP_EOL, $input);
        foreach ($input as $line){
            $line = explode(":",$line,3);
            $format[$line[0]] = trim($line[2]);
        }
        return $format;
    }
    
    /**
     * 处理HTTP服务器
     *
     * @param array $data 服务器参数
     * @return bool 处理结果
     *
     * @author   outtime<i@treeo.cn>
     * @copyright outtime<i@treeo.cn>
     * @link      https://outti.me
     */
    static private function sendHTTP($data){
        parse_str(self::parse($data['request']), $params);
        switch (strtolower($data['http_method'])) {
            case 'get':
                $response = Http::get($data['http_url'], $params);break;
            case 'post':
                $response = Http::post($data['http_url'], $params);break;
            default:
                throw new Exception("Unsupported request method: ".$data['http_method']);
        }
        if($response->body() === $data['http_return'])
            return true;
        return false;
    }
    
    private function sendRCON(){
        if(!self::$rconServer)
            self::$rconServer = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        if (!self::rconServer) 
            throw new \Exception("Could not connect to RCON server: $errstr ($errno)");
        
    }

}
