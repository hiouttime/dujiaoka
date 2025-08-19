<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FrontUser extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'email',
        'password',
        'nickname',
        'phone',
        'balance',
        'total_spent',
        'level_id',
        'status',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'balance' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'last_login_at' => 'datetime',
        'status' => 'integer',
        'level_id' => 'integer',
    ];

    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 2;

    public static function getStatusMap()
    {
        return [
            self::STATUS_ACTIVE => '正常',
            self::STATUS_DISABLED => '禁用',
        ];
    }

    // 关联用户等级
    public function level(): BelongsTo
    {
        return $this->belongsTo(UserLevel::class, 'level_id');
    }

    // 关联订单
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    // 关联余额记录
    public function balanceRecords(): HasMany
    {
        return $this->hasMany(UserBalanceRecord::class, 'user_id');
    }

    // 获取用户折扣率
    public function getDiscountRateAttribute()
    {
        return $this->level ? $this->level->discount_rate : 1.00;
    }

    // 获取用户等级名称
    public function getLevelNameAttribute()
    {
        return $this->level ? $this->level->name : '普通用户';
    }

    // 获取用户等级颜色
    public function getLevelColorAttribute()
    {
        return $this->level ? $this->level->color : '#6b7280';
    }

    // 检查是否可以升级
    public function canUpgradeLevel()
    {
        $nextLevel = UserLevel::where('min_spent', '>', $this->total_spent)
            ->where('min_spent', '>', $this->level->min_spent ?? 0)
            ->orderBy('min_spent', 'asc')
            ->first();

        return $nextLevel !== null;
    }

    // 获取下一个等级
    public function getNextLevel()
    {
        return UserLevel::where('min_spent', '>', $this->total_spent)
            ->where('min_spent', '>', $this->level->min_spent ?? 0)
            ->orderBy('min_spent', 'asc')
            ->first();
    }

    // 检查并升级用户等级
    public function checkAndUpgradeLevel()
    {
        $availableLevel = UserLevel::where('min_spent', '<=', $this->total_spent)
            ->where('status', 1)
            ->orderBy('min_spent', 'desc')
            ->first();

        if ($availableLevel && $availableLevel->id !== $this->level_id) {
            $this->update(['level_id' => $availableLevel->id]);
            return true;
        }

        return false;
    }

    // 增加余额
    public function addBalance($amount, $type = 'recharge', $description = '', $relatedOrderSn = null, $adminId = null)
    {
        $balanceBefore = $this->balance;
        $balanceAfter = $balanceBefore + $amount;

        $this->update(['balance' => $balanceAfter]);

        // 记录余额变动
        $this->balanceRecords()->create([
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'related_order_sn' => $relatedOrderSn,
            'admin_id' => $adminId,
        ]);

        return $this;
    }

    // 扣除余额
    public function deductBalance($amount, $type = 'consume', $description = '', $relatedOrderSn = null)
    {
        if ($this->balance < $amount) {
            throw new \Exception('余额不足');
        }

        $balanceBefore = $this->balance;
        $balanceAfter = $balanceBefore - $amount;

        $this->update(['balance' => $balanceAfter]);

        // 记录余额变动
        $this->balanceRecords()->create([
            'type' => $type,
            'amount' => -$amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'related_order_sn' => $relatedOrderSn,
        ]);

        return $this;
    }

    // 增加总消费并检查等级升级
    public function addTotalSpent($amount)
    {
        $this->increment('total_spent', $amount);
        $this->checkAndUpgradeLevel();
        return $this;
    }

    // 检查是否可以使用指定金额的余额
    public function canUseBalance($amount)
    {
        return $this->balance >= $amount;
    }

    // 获取状态文本
    public function getStatusTextAttribute()
    {
        return self::getStatusMap()[$this->status] ?? '未知';
    }

    // 是否激活状态
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    // 更新最后登录信息
    public function updateLastLogin($ip = null)
    {
        $this->update([
            'last_login_at' => Carbon::now(),
            'last_login_ip' => $ip ?? request()->ip(),
        ]);
    }
}