<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\BaseController;
use App\Models\Goods;
use App\Models\GoodsSub;
use App\Models\Carmis;
use App\Models\Pay;
use Illuminate\Http\Request;

class CartController extends BaseController
{
    public function index()
    {
        return $this->render('static_pages/cart', [], '购物车');
    }

    public function validateItem(Request $request)
    {
        $params = $request->only(['goods_id', 'sub_id', 'quantity']);
        $qty = (int) ($params['quantity'] ?? 1);

        $goods = Goods::with('goods_sub')->find($params['goods_id']);
        if (!$goods?->is_open) {
            return $this->fail('商品不存在或已下架');
        }

        $sub = $goods->goods_sub()->find($params['sub_id']);
        if (!$sub) {
            return $this->fail('商品规格不存在');
        }

        $stock = $goods->type == Goods::AUTOMATIC_DELIVERY 
            ? Carmis::where('sub_id', $params['sub_id'])->where('status', 1)->count()
            : $sub->stock;

        if ($qty > $stock) {
            return $this->fail("库存不足，当前库存：{$stock}");
        }

        if ($goods->buy_limit_num > 0 && $qty > $goods->buy_limit_num) {
            return $this->fail("超出限购数量：{$goods->buy_limit_num}");
        }

        $enabledPays = Pay::enabled()->get();
        $payways = empty($goods->payment_limit) 
            ? $enabledPays->toArray()
            : $enabledPays->whereIn('id', $goods->payment_limit)->values()->toArray();

        return $this->success([
            'goods_id' => $goods->id,
            'sub_id' => $sub->id,
            'name' => "{$goods->gd_name} [{$sub->name}]",
            'price' => $sub->price,
            'image' => $goods->picture,
            'stock' => $stock,
            'max_quantity' => min($stock, $goods->buy_limit_num ?: $stock),
            'payways' => $payways
        ]);
    }

    private function success($data = [])
    {
        return response()->json(['success' => true, 'data' => $data]);
    }

    private function fail($message)
    {
        return response()->json(['success' => false, 'message' => $message]);
    }

}