<?php

namespace App\Models;

use App\Events\OrderUpdated;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\CacheManager;

class Order extends BaseModel
{
    use SoftDeletes;

    protected $table = 'orders';

    protected $fillable = [
        'order_sn',
        'user_id',
        'email',
        'total_price',
        'actual_price', 
        'coupon_discount_price',
        'user_discount_rate',
        'user_discount_amount',
        'payment_method',
        'balance_used',
        'status',
        'pay_id',
        'search_pwd',
        'buy_ip',
        'trade_no'
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'actual_price' => 'decimal:2',
        'coupon_discount_price' => 'decimal:2',
        'user_discount_rate' => 'decimal:2',
        'user_discount_amount' => 'decimal:2',
        'balance_used' => 'decimal:2',
        'payment_method' => 'integer',
        'status' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::updated(function ($order) {
            CacheManager::forgetOrder($order->order_sn);
        });
        
        static::deleted(function ($order) {
            CacheManager::forgetOrder($order->order_sn);
        });
    }

    const STATUS_WAIT_PAY = 1;
    const STATUS_PENDING = 2;
    const STATUS_PROCESSING = 3;
    const STATUS_COMPLETED = 4;
    const STATUS_FAILURE = 5;
    const STATUS_ABNORMAL = 6;
    const STATUS_EXPIRED = -1;

    const PAYMENT_ONLINE = 1;
    const PAYMENT_BALANCE = 2;
    const PAYMENT_MIXED = 3;

    protected $dispatchesEvents = [
        'updated' => OrderUpdated::class
    ];

    public static function getStatusMap()
    {
        return [
            self::STATUS_WAIT_PAY => __('order.fields.status_wait_pay'),
            self::STATUS_PENDING => __('order.fields.status_pending'),
            self::STATUS_PROCESSING => __('order.fields.status_processing'),
            self::STATUS_COMPLETED => __('order.fields.status_completed'),
            self::STATUS_FAILURE => __('order.fields.status_failure'),
            self::STATUS_ABNORMAL => __('order.fields.status_abnormal'),
            self::STATUS_EXPIRED => __('order.fields.status_expired')
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function pay(): BelongsTo
    {
        return $this->belongsTo(Pay::class, 'pay_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getTotalQuantityAttribute(): int
    {
        return $this->orderItems->sum('quantity');
    }

    public function getGoodsSummaryAttribute(): string
    {
        $items = $this->orderItems->take(3);
        $names = $items->pluck('goods_name')->toArray();
        
        if ($this->orderItems->count() > 3) {
            $names[] = '等' . $this->orderItems->count() . '件商品';
        }
        
        return implode(', ', $names);
    }

    public function setStatusAttribute($value)
    {
        if($this->status != self::STATUS_WAIT_PAY || intval($value) != self::STATUS_COMPLETED){
            $this->attributes['status'] = $value;
            return;
        }

        $this->attributes['status'] = $value;
        
        if($value == self::STATUS_COMPLETED) {
            app('App\Services\OrderProcess')->completedOrder($this->order_sn, $this->actual_price);
        }
    }
}
