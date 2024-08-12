<?php

namespace App\Models;


use App\Events\GoodsDeleted;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsSub extends BaseModel
{

    protected $table = 'goods_sub';
    
    protected $fillable = [
        'goods_id',
        'name',
        'price',
        'stock',
        'sales_volume'
    ];

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }
    
    /**
     * 自动发货自动计算库存
     *
     * @author    outtime<beprivacy@icloud.com>
     * @copyright outtime<beprivacy@icloud.com>
     * @link      https://outti.me
     */
     public function getStockAttribute()
     {
        
        if ($this->goods->type == self::AUTOMATIC_DELIVERY) {
            return Carmis::where('sub_id', $this->id)
                ->where('status', Carmis::STATUS_UNSOLD)
                ->count();
        }
        return $this->attributes['stock'];
     }
}
