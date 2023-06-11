<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Articles extends BaseModel
{
    use SoftDeletes;
    protected $table = 'articles';
    
    public function getSummary()
    {
        //简单地输出摘要
        $summary = substr($this->content, 0, 200);
        return strip_tags($summary);
    }
}
