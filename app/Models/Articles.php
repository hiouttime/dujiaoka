<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Overtrue\Pinyin\Pinyin;

class Articles extends BaseModel
{
    use SoftDeletes;
    protected $table = 'articles';
    
    protected $fillable = [
        'category_id',
        'title',
        'content',
        'is_open',
        'sort',
        'link'
    ];
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($article) {
            if (empty($article->link) && !empty($article->title)) {
                $article->link = static::generateSlug($article->title);
            }
        });
        
        static::updating(function ($article) {
            if (empty($article->link) && !empty($article->title)) {
                $article->link = static::generateSlug($article->title);
            }
        });
    }
    
    public static function generateSlug($title)
    {
        $slug = Pinyin::permalink($title, '-');
        
        if (empty($slug)) {
            $slug = preg_replace('/[^a-zA-Z0-9\s]/', '', $title);
            $slug = str_replace(' ', '-', trim($slug));
            $slug = strtolower($slug);
            
            if (empty($slug)) {
                $slug = 'article-' . time();
            }
        }
        
        // 确保唯一性
        $originalSlug = $slug;
        $counter = 1;
        while (static::where('link', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * 关联分类
     */
    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }
    
    /**
     * 关联商品
     */
    public function goods()
    {
        return $this->belongsToMany(Goods::class, 'article_goods', 'article_id', 'goods_id')
                    ->withTimestamps()
                    ->withPivot('sort')
                    ->orderBy('pivot_sort', 'desc');
    }
    
    public function getSummary()
    {
        //简单地输出摘要
        $summary = substr($this->content, 0, 200);
        return strip_tags($summary);
    }
}
