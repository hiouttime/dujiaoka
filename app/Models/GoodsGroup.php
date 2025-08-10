<?php

namespace App\Models;


use App\Events\GoodsGroupDeleted;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsGroup extends BaseModel
{

    use SoftDeletes;

    protected $table = 'goods_group';
    
    protected $fillable = [
        'gp_name',
        'is_open', 
        'ord'
    ];

    protected $dispatchesEvents = [
        'deleted' => GoodsGroupDeleted::class
    ];

    /**
     * 关联商品
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     */
    public function goods()
    {
        return $this->hasMany(Goods::class, 'group_id');
    }

}
