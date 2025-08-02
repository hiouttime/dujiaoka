<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends BaseModel
{

    use SoftDeletes;

    protected $table = 'coupons';

    const TYPE_PERCENT = 1; //系数优惠
    const TYPE_FIXED = 2; //整体固定金额优惠
    const TYPE_EACH = 3; //每件固定金额优惠

    /**
     * 关联商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     *
     * @author    assimon<ashang@utf8.hk>
     * @copyright assimon<ashang@utf8.hk>
     * @link      http://utf8.hk/
     */
    public function goods()
    {
        return $this->belongsToMany(Goods::class, 'coupons_goods', 'coupons_id', 'goods_id');
    }


    public static function getTypeMap()
    {
        return [
            self::TYPE_PERCENT => __('coupon.fields.type_percent'),
            self::TYPE_FIXED => __('coupon.fields.type_fixed'),
            self::TYPE_EACH => __('coupon.fields.type_each'),
        ];
    }


}
