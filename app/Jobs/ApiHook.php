<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ApiHook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 任务最大尝试次数。
     *
     * @var int
     */
    public $tries = 2;

    /**
     * 任务运行的超时时间。
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * @var Order
     */
    private $order;

    /**
     * 商品服务层.
     * @var \App\Service\PayService
     */
    private $goodsService;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $goodInfo = app('Service\GoodsService')->detail($this->order->goods_id);
        // 判断是否有配置支付回调
        if(empty($goodInfo->api_hook))
            return;
        $result = app('Service\RemoteServerService')->execute($goodInfo->api_hook,$this->order);
        if($result)
            $this->order->status = Order::STATUS_COMPLETED;
        else
            $this->order->status = Order::STATUS_FAILURE;
        $this->order->save();
        
        if(!$result)
            throw new \Exception("Fail to complete order: (".$this->order->order_sn.")");
    }
}
