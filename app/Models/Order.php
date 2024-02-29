<?php

namespace App\Models;

use App\Events\OrderUpdated;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends BaseModel
{

    use SoftDeletes;

    protected $table = 'orders';

    /**
     * 待支付
     */
    const STATUS_WAIT_PAY = 1;

    /**
     * 待处理
     */
    const STATUS_PENDING = 2;

    /**
     * 处理中
     */
    const STATUS_PROCESSING = 3;

    /**
     * 已完成
     */
    const STATUS_COMPLETED = 4;

    /**
     * 失败
     */
    const STATUS_FAILURE = 5;

    /**
     * 过期
     */
    const STATUS_EXPIRED = -1;

    /**
     * 异常
     */
    const STATUS_ABNORMAL = 6;

    protected $dispatchesEvents = [
        'updated' => OrderUpdated::class
    ];


    /**
     * 状态映射
     *
     * @return array
     *
     * @author    assimon<ashang@utf8.hk>
     * @copyright assimon<ashang@utf8.hk>
     * @link      http://utf8.hk/
     */
    public static function getStatusMap()
    {
        return [
            self::STATUS_WAIT_PAY => admin_trans('order.fields.status_wait_pay'),
            self::STATUS_PENDING => admin_trans('order.fields.status_pending'),
            self::STATUS_PROCESSING => admin_trans('order.fields.status_processing'),
            self::STATUS_COMPLETED => admin_trans('order.fields.status_completed'),
            self::STATUS_FAILURE => admin_trans('order.fields.status_failure'),
            self::STATUS_ABNORMAL => admin_trans('order.fields.status_abnormal'),
            self::STATUS_EXPIRED => admin_trans('order.fields.status_expired')
        ];
    }

    /**
     * 关联商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @author    assimon<ashang@utf8.hk>
     * @copyright assimon<ashang@utf8.hk>
     * @link      http://utf8.hk/
     */
    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }

    /**
     * 关联优惠券
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @author    assimon<ashang@utf8.hk>
     * @copyright assimon<ashang@utf8.hk>
     * @link      http://utf8.hk/
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    /**
     * 关联支付
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @author    assimon<ashang@utf8.hk>
     * @copyright assimon<ashang@utf8.hk>
     * @link      http://utf8.hk/
     */
    public function pay()
    {
        return $this->belongsTo(Pay::class, 'pay_id');
    }

    /**
     * 订单状态更新时处理
     *
     * @param Order $order
     * @return mixed
     *
     * @author    outtime<i@treeo.cn>
     * @copyright outtime<i@treeo.cn>
     * @link      https://outti.me
     */
    public function setStatusAttribute($value){
        $this->attributes['status'] = $value;
        // 人工处理订单直接设置
        if($this->type == Order::MANUAL_PROCESSING)
            return;
        // 设置为处理中时执行订单事物
        if($this->getOriginal('status') != self::STATUS_PROCESSING && $value == self::STATUS_PROCESSING)
            app('Service\OrderProcessService')->completedOrder($this->order_sn, $this->actual_price);
    }
}
