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
        $goodsId = $request->input('goods_id');
        $subId = $request->input('sub_id');
        $quantity = (int) $request->input('quantity', 1);

        $goods = Goods::with('goods_sub')->find($goodsId);
        if (!$goods || !$goods->is_open) {
            return response()->json(['success' => false, 'message' => '商品不存在或已下架']);
        }

        $sub = $goods->goods_sub()->find($subId);
        if (!$sub) {
            return response()->json(['success' => false, 'message' => '商品规格不存在']);
        }

        $stock = $goods->type == Goods::AUTOMATIC_DELIVERY 
            ? Carmis::where('sub_id', $subId)->where('status', 1)->count()
            : $sub->stock;

        if ($quantity > $stock) {
            return response()->json(['success' => false, 'message' => '库存不足，当前库存：' . $stock]);
        }

        if ($goods->buy_limit_num > 0 && $quantity > $goods->buy_limit_num) {
            return response()->json(['success' => false, 'message' => '超出限购数量：' . $goods->buy_limit_num]);
        }

        $enabled = Pay::where('is_open', 1)->get();
        
        if (!empty($goods->payment_limit)) {
            $payways = $enabled->whereIn('id', $goods->payment_limit)->values()->toArray();
        } else {
            $payways = $enabled->toArray();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'goods_id' => $goods->id,
                'sub_id' => $sub->id,
                'name' => $goods->gd_name . ' [' . $sub->name . ']',
                'price' => $sub->price,
                'image' => $goods->picture,
                'stock' => $stock,
                'max_quantity' => min($stock, $goods->buy_limit_num ?: $stock),
                'payways' => $payways
            ]
        ]);
    }

}