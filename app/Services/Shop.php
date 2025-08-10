<?php

namespace App\Services;

use App\Exceptions\RuleValidationException;
use App\Models\Carmis;
use App\Models\Goods;
use App\Models\GoodsGroup;
class Shop
{
    /**
     * 获取所有分类并加载该分类下的商品
     *
     * @return array|null
     */
    public function withGroup(): ?array
    {
        $goods = GoodsGroup::query()
            ->with(['goods' => fn($query) => $query->with('goods_sub')->where('is_open', Goods::STATUS_OPEN)->orderBy('ord', 'DESC')])
            ->where('is_open', GoodsGroup::STATUS_OPEN)
            ->orderBy('ord', 'DESC')
            ->get();
        return $goods?->toArray();
    }

    /**
     * 商品详情
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function detail(int $id)
    {
        return CacheManager::rememberGoods($id, fn() => 
            Goods::with(['coupon', 'goods_sub'])->find($id)
        );
    }

    /**
     * 格式化商品信息
     *
     * @param Goods $goods
     * @return Goods
     */
    public function format(Goods $goods)
    {
        $goods->wholesale_price_cnf = $goods->wholesale_price_cnf ? formatWholesalePrice($goods->wholesale_price_cnf) : null;
        $goods->other_ipu = $goods->other_ipu_cnf ? formatChargeInput($goods->other_ipu_cnf) : null;
        return $goods;
    }

    /**
     * 验证商品状态
     *
     * @param Goods $goods
     * @return Goods
     * @throws RuleValidationException
     */
    public function validatorGoodsStatus(Goods $goods): Goods
    {
        if (empty($goods)) {
            throw new RuleValidationException(__('dujiaoka.prompt.goods_does_not_exist'));
        }
        if ($goods->is_open != Goods::STATUS_OPEN) {
            throw new RuleValidationException(__('dujiaoka.prompt.the_goods_is_not_on_the_shelves'));
        }
        return $goods;
    }

    /**
     * 库存减少
     *
     * @param int $subId
     * @param int $number
     * @return bool
     */
    public function inStockDecr(int $subId, int $number = 1): bool
    {
        return \App\Models\GoodsSub::where('id', $subId)->decrement('stock', $number);
    }

    /**
     * 商品销量增加
     *
     * @param int $id
     * @param int $number
     * @return bool
     */
    public function salesVolumeIncr(int $id, int $number = 1): bool
    {
        return Goods::where('id', $id)->increment('sales_volume', $number);
    }
    
    /**
     * 获取商品可供选择的卡密
     *
     * @param int $id
     * @return array
     */
    public function getSelectableCarmis(int $id): array
    {
        $goods = Goods::with('goods_sub')->find($id);
        if (!$goods) return [];
        
        return Carmis::whereIn('sub_id', $goods->goods_sub->pluck('id'))
            ->where('status', Carmis::STATUS_UNSOLD)
            ->get(['id', 'info'])
            ->toArray();
    }
    
    /**
     * 检查卡密归属
     *
     * @param int $good_id
     * @param int $carmi_id
     * @return bool
     */
    public function checkCarmiBelong(int $good_id, int $carmi_id): bool
    {
        $goods = Goods::with('goods_sub')->find($good_id);
        if (!$goods) return false;
        
        return Carmis::where('id', $carmi_id)
            ->whereIn('sub_id', $goods->goods_sub->pluck('id'))
            ->where('status', Carmis::STATUS_UNSOLD)
            ->exists();
    }

}
