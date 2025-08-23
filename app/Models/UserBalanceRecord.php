<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBalanceRecord extends BaseModel
{
    protected $table = 'user_balance_records';

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'related_order_sn',
        'admin_id',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'user_id' => 'integer',
        'admin_id' => 'integer',
        'meta' => 'array',
    ];

    // 余额变动类型
    const TYPE_RECHARGE = 'recharge';      // 充值
    const TYPE_CONSUME = 'consume';        // 消费
    const TYPE_REFUND = 'refund';          // 退款
    const TYPE_ADMIN = 'admin';            // 管理员调整

    public static function getTypeMap()
    {
        return [
            self::TYPE_RECHARGE => '充值',
            self::TYPE_CONSUME => '消费',
            self::TYPE_REFUND => '退款',
            self::TYPE_ADMIN => '管理员调整',
        ];
    }

    // 关联用户
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 关联管理员
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // 关联订单
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'related_order_sn', 'order_sn');
    }

    // 获取类型文本
    public function getTypeTextAttribute()
    {
        return self::getTypeMap()[$this->type] ?? '未知';
    }

    // 获取变动金额（带符号）
    public function getSignedAmountAttribute()
    {
        return $this->amount >= 0 ? '+' . $this->amount : $this->amount;
    }

    // 获取类型样式类
    public function getTypeClassAttribute()
    {
        return match($this->type) {
            self::TYPE_RECHARGE => 'text-green-600',
            self::TYPE_CONSUME => 'text-red-600',
            self::TYPE_REFUND => 'text-blue-600',
            self::TYPE_ADMIN => 'text-purple-600',
            default => 'text-gray-600',
        };
    }

    // 获取图标
    public function getIconAttribute()
    {
        return match($this->type) {
            self::TYPE_RECHARGE => 'heroicon-o-plus-circle',
            self::TYPE_CONSUME => 'heroicon-o-minus-circle',
            self::TYPE_REFUND => 'heroicon-o-arrow-uturn-left',
            self::TYPE_ADMIN => 'heroicon-o-wrench-screwdriver',
            default => 'heroicon-o-question-mark-circle',
        };
    }

    // 是否为收入类型
    public function isIncomeType()
    {
        return in_array($this->type, [self::TYPE_RECHARGE, self::TYPE_REFUND]);
    }

    // 是否为支出类型
    public function isExpenseType()
    {
        return $this->type === self::TYPE_CONSUME;
    }

    // 作用域：按用户筛选
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // 作用域：按类型筛选
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // 作用域：收入记录
    public function scopeIncome($query)
    {
        return $query->whereIn('type', [self::TYPE_RECHARGE, self::TYPE_REFUND]);
    }

    // 作用域：支出记录
    public function scopeExpense($query)
    {
        return $query->where('type', self::TYPE_CONSUME);
    }

    // 作用域：按日期范围筛选
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // 作用域：最近的记录
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    // 创建充值记录
    public static function createRechargeRecord($userId, $amount, $balanceBefore, $balanceAfter, $description = '', $meta = [])
    {
        return static::create([
            'user_id' => $userId,
            'type' => self::TYPE_RECHARGE,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description ?: '余额充值',
            'meta' => $meta,
        ]);
    }

    // 创建消费记录
    public static function createConsumeRecord($userId, $amount, $balanceBefore, $balanceAfter, $orderSn = '', $description = '')
    {
        return static::create([
            'user_id' => $userId,
            'type' => self::TYPE_CONSUME,
            'amount' => -abs($amount),
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description ?: '订单消费',
            'related_order_sn' => $orderSn,
        ]);
    }

    // 创建退款记录
    public static function createRefundRecord($userId, $amount, $balanceBefore, $balanceAfter, $orderSn = '', $description = '')
    {
        return static::create([
            'user_id' => $userId,
            'type' => self::TYPE_REFUND,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description ?: '订单退款',
            'related_order_sn' => $orderSn,
        ]);
    }

    // 创建管理员调整记录
    public static function createAdminRecord($userId, $amount, $balanceBefore, $balanceAfter, $adminId, $description = '')
    {
        return static::create([
            'user_id' => $userId,
            'type' => self::TYPE_ADMIN,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description ?: '管理员调整',
            'admin_id' => $adminId,
        ]);
    }
}