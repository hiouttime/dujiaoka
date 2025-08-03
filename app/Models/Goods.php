<?php

namespace App\Models;


use App\Events\GoodsDeleted;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\CacheManager;

class Goods extends BaseModel
{

    use SoftDeletes;

    protected $table = 'goods';

    protected $dispatchesEvents = [
        'deleted' => GoodsDeleted::class
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::updated(function ($goods) {
            CacheManager::forgetGoods($goods->id);
        });
        
        static::deleted(function ($goods) {
            CacheManager::forgetGoods($goods->id);
        });
    }
    

    /**
     * 关联分类
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     */
    public function group()
    {
        return $this->belongsTo(GoodsGroup::class, 'group_id');
    }

    /**
     * 关联优惠券
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     *
     */
    public function coupon()
    {
        return $this->belongsToMany(Coupon::class, 'coupons_goods', 'goods_id', 'coupons_id');
    }

    /**
     * 关联卡密
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     */
    public function carmis()
    {
        return $this->hasMany(Carmis::class, 'goods_id');
    }

    /**
     * 库存读取器,将自动发货的库存更改为未出售卡密的数量
     *
     */
    public function getStockAttribute()
    {
        if (isset($this->attributes['carmis_count'])
            &&
            $this->attributes['type'] == self::AUTOMATIC_DELIVERY
        ) {
           $this->attributes['stock'] = $this->attributes['carmis_count'];
        }
        return $this->attributes['stock'];
    }

    /**
     * 获取组建映射
     *
     * @return array
     *
     */
    public static function getGoodsTypeMap()
    {
        return [
            self::AUTOMATIC_DELIVERY => __('goods.fields.automatic_delivery'),
            self::MANUAL_PROCESSING => __('goods.fields.manual_processing'),
            self::AUTOMATIC_PROCESSING => __('goods.fields.automatic_processing'),
        ];
    }
    
    public function goods_sub()
    {
        return $this->hasMany(GoodsSub::class);
    }
    
    
}
