<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Carmis extends BaseModel
{

    use SoftDeletes;

    protected $table = 'carmis';

    /**
     * 未售出
     */
    const STATUS_UNSOLD = 1;

    /**
     * 已售出
     */
    const STATUS_SOLD = 2;

    /**
     * 获取组建映射
     *
     * @return array
     *
     */
    public static function getStatusMap()
    {
        return [
            self::STATUS_UNSOLD => __('carmis.fields.status_unsold'),
            self::STATUS_SOLD => __('carmis.fields.status_sold')
        ];
    }

    /**
     * 关联商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     */
    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }

}
