<?php
/**
 * The file was created by Assimon.
 *
 */

namespace App\Services;


use App\Models\Carmis;

class Cards
{

    /**
     * 通过商品查询一些数量未使用的卡密
     *
     * @param int $goodsID 商品id
     * @param int $byAmount 数量
     * @return array|null
     *
     */
    public function takes(int $goodsID, int $byAmount, int $sub_id)
    {
        $carmis = Carmis::query()
            ->where('goods_id', $goodsID)
            ->where('sub_id', $sub_id)
            ->where('status', Carmis::STATUS_UNSOLD)
            ->take($byAmount)
            ->get();
        return $carmis ? $carmis->toArray() : null;
    }
    
     /**
     * 通过卡密ID获得卡密
     *
     * @param int $id 卡密id
     * @return string|null
     *
     * @author    outtime<i@treeo.cn>
     * @copyright outtime<i@treeo.cn>
     * @link      https://outti.me
     */
     public function getCarmiById(int $id){
         return Carmis::find($id);
     }

    /**
     * 通过id集合设置卡密已售出
     *
     * @param array $ids 卡密id集合
     * @return bool
     *
     */
    public function soldByIDS(array $ids): bool
    {
        return Carmis::query()->whereIn('id', $ids)->where('is_loop', 0)->update(['status' => Carmis::STATUS_SOLD]);
    }

}
