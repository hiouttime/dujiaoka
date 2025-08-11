<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Carmis extends BaseModel
{

    use SoftDeletes;

    protected $table = 'carmis';

    protected $fillable = [
        'goods_id',
        'sub_id', 
        'carmi',
        'status'
    ];

    /**
     * 未售出状态
     */
    const STATUS_UNSOLD = 1;

    /**
     * 已售出状态
     */
    const STATUS_SOLD = 2;

    /**
     * 获取状态映射
     *
     * @return array
     */
    public static function getStatusMap()
    {
        return [
            self::STATUS_UNSOLD => __('carmis.fields.status_unsold'),
            self::STATUS_SOLD => __('carmis.fields.status_sold')
        ];
    }

    /**
     * 关联商品规格
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goodsSub()
    {
        return $this->belongsTo(GoodsSub::class, 'sub_id');
    }
    
    /**
     * 关联商品（通过规格）
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id')->withDefault(function () {
            return $this->goodsSub?->goods;
        });
    }

}
