<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Articles extends Model
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
