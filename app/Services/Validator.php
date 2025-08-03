<?php

namespace App\Services;

use App\Exceptions\RuleValidationException;
use App\Models\Goods;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as LaravelValidator;

/**
 * 统一验证服务
 */
class Validator
{
    protected Shop $goodsService;
    protected Coupons $couponService;

    public function __construct()
    {
        $this->goodsService = app('App\Services\Shop');
        $this->couponService = app('App\Services\Coupons');
    }

    /**
     * 验证商品状态
     */
    public function validateGoodsStatus(Goods $goods): Goods
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
     * 验证商品库存
     */
    public function validateGoodsStock(Goods $goods, int $quantity = 1): void
    {
        if ($goods->type == Goods::AUTOMATIC_DELIVERY && $goods->stock < $quantity) {
            throw new RuleValidationException(__('dujiaoka.prompt.insufficient_inventory'));
        }
    }

    /**
     * 验证优惠券
     */
    public function validateCoupon(Request $request): ?Coupon
    {
        if ($request->filled('coupon_code')) {
            $coupon = $this->couponService->withHasGoods($request->input('coupon_code'), $request->input('gid'));
            
            if (!$coupon) {
                throw new RuleValidationException(__('dujiaoka.prompt.coupon_does_not_exist'));
            }
            
            if ($coupon->is_open != Coupon::STATUS_OPEN) {
                throw new RuleValidationException(__('dujiaoka.prompt.coupon_disabled'));
            }
            
            return $coupon;
        }
        
        return null;
    }

    /**
     * 验证订单创建请求
     */
    public function validateOrderRequest(Request $request): void
    {
        $validator = LaravelValidator::make($request->all(), [
            'gid' => 'required|integer',
            'email' => ['required', 'email'],
            'payway' => ['required', 'integer'],
            'search_pwd' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            throw new RuleValidationException($validator->errors()->first());
        }
    }

    /**
     * 验证商品限购
     */
    public function validatePurchaseLimit(Goods $goods, string $email, int $quantity): void
    {
        if ($goods->buy_limit_num > 0) {
            $count = Order::where('goods_id', $goods->id)
                ->where('email', $email)
                ->whereIn('status', [Order::STATUS_COMPLETED, Order::STATUS_PROCESSING, Order::STATUS_PENDING])
                ->sum('buy_amount');
                
            if (($count + $quantity) > $goods->buy_limit_num) {
                throw new RuleValidationException(__('dujiaoka.prompt.purchase_limit_exceeded'));
            }
        }
    }
}