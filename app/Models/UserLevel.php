<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class UserLevel extends BaseModel
{
    protected $table = 'user_levels';

    protected $fillable = [
        'name',
        'min_spent',
        'discount_rate',
        'color',
        'description',
        'sort',
        'status',
    ];

    protected $casts = [
        'min_spent' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'sort' => 'integer',
        'status' => 'integer',
    ];

    const STATUS_ACTIVE = 1;
    const STATUS_DISABLED = 0;

    public static function getStatusMap()
    {
        return [
            self::STATUS_ACTIVE => '启用',
            self::STATUS_DISABLED => '禁用',
        ];
    }

    // 关联用户
    public function users(): HasMany
    {
        return $this->hasMany(FrontUser::class, 'level_id');
    }

    // 获取折扣百分比文本
    public function getDiscountPercentAttribute()
    {
        return round((1 - $this->discount_rate) * 100, 1) . '%';
    }

    // 获取状态文本
    public function getStatusTextAttribute()
    {
        return self::getStatusMap()[$this->status] ?? '未知';
    }

    // 是否启用
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    // 获取所有启用的等级
    public static function getActiveLevels()
    {
        return static::where('status', self::STATUS_ACTIVE)
            ->orderBy('sort')
            ->orderBy('min_spent')
            ->get();
    }

    // 根据消费金额获取对应等级
    public static function getLevelBySpent($totalSpent)
    {
        return static::where('min_spent', '<=', $totalSpent)
            ->where('status', self::STATUS_ACTIVE)
            ->orderBy('min_spent', 'desc')
            ->first();
    }

    // 获取下一个等级
    public function getNextLevel()
    {
        return static::where('min_spent', '>', $this->min_spent)
            ->where('status', self::STATUS_ACTIVE)
            ->orderBy('min_spent', 'asc')
            ->first();
    }

    // 获取升级所需金额
    public function getUpgradeRequiredAmount($currentSpent = 0)
    {
        $nextLevel = $this->getNextLevel();
        if (!$nextLevel) {
            return 0;
        }

        return max(0, $nextLevel->min_spent - $currentSpent);
    }

    // 作用域：启用的等级
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    // 作用域：按消费金额排序
    public function scopeOrderBySpent($query, $direction = 'asc')
    {
        return $query->orderBy('min_spent', $direction);
    }

    // 作用域：按排序字段排序
    public function scopeOrderBySort($query, $direction = 'asc')
    {
        return $query->orderBy('sort', $direction)->orderBy('min_spent', $direction);
    }
}