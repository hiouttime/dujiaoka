<?php
/**
 * The file was created by Assimon.
 *
 */

namespace App\Services;


use App\Models\Coupon;

class Coupons
{

    /**
     * 获得优惠码，通过商品关联
     *
     * @param string $coupon 优惠码
     * @param int $goodsID 商品id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     *
     */
    public function withHasGoods(string $coupon, int $goodsID)
    {
        $coupon = Coupon::query()->whereHas('goods', function ($query) use ($goodsID) {
            $query->where('goods_id', $goodsID);
        })->where('is_open', Coupon::STATUS_OPEN)->where('coupon', $coupon)->first();
        return $coupon;
    }

    /**
     * 设置优惠券使用次数 -1
     * @param string $coupon
     * @return int
     *
     */
    public function retDecr(string $coupon)
    {
        return Coupon::query()
            ->where('coupon',  $coupon)
            ->decrement('ret', 1);
    }

    /**
     * 设置优惠券次数+1
     *
     * @param int $id
     * @return int
     *
     */
    public function retIncrByID(int $id)
    {
        return Coupon::query()->where('id',  $id)->increment('ret', 1);
    }

}
