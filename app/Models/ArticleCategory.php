<?php

namespace App\Models;

class ArticleCategory extends BaseModel
{
    protected $table = 'article_categories';
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort',
        'is_active'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];
    
    /**
     * 关联文章
     */
    public function articles()
    {
        return $this->hasMany(Articles::class, 'category_id');
    }
    
    /**
     * 获取启用的分类
     */
    public static function active()
    {
        return self::where('is_active', true)->orderBy('sort', 'desc')->orderBy('id', 'asc');
    }
}
