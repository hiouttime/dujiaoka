<?php
namespace App\Http\Controllers\Home;

use App\Models\Articles;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class ArticleController extends BaseController {
    
    public function listAll(){
        $articles = Articles::select('title', 'link', 'category', 'content', 'updated_at')
        ->get()
        ->map(function ($article) {
            $article->summary = $article->getSummary();
            return $article;
        });
        $articles_sort = $articles->groupBy('category');
        $categories = $articles_sort->keys();
        
        return $this->render('static_pages/article', [
            'articles' => $articles_sort,
            'category' => $categories
        ], __('dujiaoka.page-title.article'));
    }
    
    public function show($link) {
        
        // 根据 $link 查询文章内容
        $article = Articles::where('link', $link)->first();

        if (!$article) {
            abort(404);
        }

        $title = $article->title;
        $content = $article->content;
        
        return $this->render('static_pages/article', [
        'title' => $title,
        'content' => $content
        ],
        $title." | ". __('dujiaoka.page-title.article'));
    }
    
}