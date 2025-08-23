<?php

namespace App\Models;


use App\Events\GoodsDeleted;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\CacheManager;

class Goods extends BaseModel
{

    use SoftDeletes;

    protected $table = 'goods';

    protected $fillable = [
        'group_id', 'gd_name', 'gd_description', 'gd_keywords', 'picture', 'picture_url',
        'sales_volume', 'ord', 'payment_limit',
        'buy_limit_num', 'buy_min_num', 'buy_prompt', 'description', 'usage_instructions',
        'type', 'wholesale_price_cnf', 'wholesale_prices', 'other_ipu_cnf', 
        'customer_form_fields', 'api_hook', 'preselection', 'is_open', 'require_login'
    ];

    protected $casts = [
        'customer_form_fields' => 'array',
        'wholesale_prices' => 'array',
        'payment_limit' => 'array',
        'preselection' => 'decimal:2',
        'sales_volume' => 'integer',
        'ord' => 'integer',
        'buy_limit_num' => 'integer',
        'buy_min_num' => 'integer',
        'type' => 'integer',
        'api_hook' => 'integer',
        'is_open' => 'boolean',
        'require_login' => 'boolean',
    ];

    protected $dispatchesEvents = [
        'deleted' => GoodsDeleted::class
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::updated(function ($goods) {
            CacheManager::forgetGoodsWithSub($goods->id);
        });
        
        static::deleted(function ($goods) {
            CacheManager::forgetGoodsWithSub($goods->id);
        });
    }
    

    /**
     * 关联分组
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(GoodsGroup::class, 'group_id');
    }

    /**
     * 关联优惠券
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function coupon()
    {
        return $this->belongsToMany(Coupon::class, 'coupons_goods', 'goods_id', 'coupons_id');
    }

    /**
     * 关联卡密（通过子规格）
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function carmis()
    {
        return $this->hasManyThrough(Carmis::class, GoodsSub::class, 'goods_id', 'sub_id');
    }

    /**
     * 获取商品类型映射
     *
     * @return array
     */
    public static function getGoodsTypeMap()
    {
        return [
            self::AUTOMATIC_DELIVERY => __('goods.fields.automatic_delivery'),
            self::MANUAL_PROCESSING => __('goods.fields.manual_processing'),
            self::AUTOMATIC_PROCESSING => __('goods.fields.automatic_processing'),
        ];
    }
    
    /**
     * 关联商品规格
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goods_sub()
    {
        return $this->hasMany(GoodsSub::class);
    }
    
    /**
     * 关联文章
     */
    public function articles()
    {
        return $this->belongsToMany(Articles::class, 'article_goods', 'goods_id', 'article_id')
                    ->withTimestamps()
                    ->withPivot('sort')
                    ->orderBy('pivot_sort', 'desc');
    }
    
    
}
