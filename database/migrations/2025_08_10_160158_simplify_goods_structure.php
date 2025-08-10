<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Goods;
use App\Models\GoodsSub;
use App\Models\Carmis;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 先处理历史数据：为所有没有子规格的商品创建默认子规格
        $goods = Goods::whereDoesntHave('goods_sub')->get();
        
        foreach ($goods as $good) {
            // 为单规格商品创建默认子规格
            $sub = GoodsSub::create([
                'goods_id' => $good->id,
                'name' => '默认规格',
                'price' => $good->price ?? 0,
                'stock' => $good->stock ?? 0,
                'sales_volume' => 0
            ]);
            
            // 将关联到goods_id的carmis转移到sub_id
            Carmis::where('goods_id', $good->id)
                ->whereNull('sub_id')
                ->update(['sub_id' => $sub->id]);
        }
        
        // 移除不再需要的字段
        Schema::table('goods', function (Blueprint $table) {
            $table->dropColumn(['is_sub', 'price', 'stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 恢复字段
        Schema::table('goods', function (Blueprint $table) {
            $table->tinyInteger('is_sub')->unsigned()->default(0)->comment('是否是多规格商品');
            $table->decimal('price', 8, 2)->default(0)->comment('商品价格');
            $table->integer('stock')->default(0)->comment('库存数量');
        });
        
        // 恢复历史数据
        $goods = Goods::with('goods_sub')->get();
        
        foreach ($goods as $good) {
            $firstSub = $good->goods_sub->first();
            if ($firstSub && $good->goods_sub->count() == 1 && $firstSub->name == '默认规格') {
                // 这是之前的单规格商品，恢复原始数据
                $good->update([
                    'is_sub' => 0,
                    'price' => $firstSub->price,
                    'stock' => $firstSub->stock
                ]);
                
                // 将carmis关联回goods_id
                Carmis::where('sub_id', $firstSub->id)->update([
                    'goods_id' => $good->id,
                    'sub_id' => null
                ]);
                
                // 删除默认子规格
                $firstSub->delete();
            } else {
                // 多规格商品
                $good->update(['is_sub' => 1]);
            }
        }
    }
};
