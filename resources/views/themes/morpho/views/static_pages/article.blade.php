@extends('themes.morpho.views.layouts.default')

@section('content')
<div class="container my-4">
    @if(isset($title) && isset($content))
        {{-- 单篇文章显示 --}}
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('article.list') }}">文章</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </nav>
                
                <div class="card">
                    <div class="card-body">
                        <h1 class="mb-4">{{ $title }}</h1>
                        <div class="article-content">
                            {!! $content !!}
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        
        {{-- 关联商品显示 - 独立区域 --}}
        @if(isset($relatedGoods) && $relatedGoods->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body py-3">
                            <h6 class="mb-3 text-primary d-flex align-items-center">
                                <i class="fas fa-shopping-bag me-2"></i>相关商品
                            </h6>
                            <div class="row g-3">
                                @foreach($relatedGoods as $goods)
                                    <div class="col-6 col-lg-4">
                                        <div class="goods-ad-item d-flex align-items-center">
                                            <a href="{{ route('goods.show', $goods->id) }}" class="text-decoration-none d-flex align-items-center w-100">
                                                <div class="goods-image-ad me-3">
                                                    <img src="{{ pictureUrl($goods->picture) }}" 
                                                         alt="{{ $goods->gd_name }}" 
                                                         class="goods-image">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="goods-title-ad mb-1">{{ $goods->gd_name }}</h6>
                                                    <p class="goods-desc-ad text-muted small mb-0">{{ Str::limit($goods->gd_description, 40) }}</p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        {{-- 文章列表显示 --}}
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">文章列表</h1>
                
                @if(isset($articles) && $articles->count() > 0)
                    @foreach($articles as $categoryName => $categoryArticles)
                        <div class="mb-4">
                            <h3 class="text-primary mb-3">{{ $categoryName }}</h3>
                            <div class="row">
                                @foreach($categoryArticles as $article)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body d-flex flex-column">
                                                <h5 class="card-title">
                                                    <a href="{{ route('article.show', $article->link) }}" class="text-decoration-none">
                                                        {{ $article->title }}
                                                    </a>
                                                </h5>
                                                <p class="card-text text-muted flex-grow-1">{{ $article->summary }}</p>
                                                <div class="mt-auto">
                                                    <small class="text-muted">
                                                        {{ $article->updated_at->format('Y-m-d') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-5">
                        <h4 class="text-muted">暂无文章</h4>
                        <p class="text-muted">还没有发布任何文章，请稍后再来查看。</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<style>
.article-content {
    line-height: 1.7;
}

.article-content h1, .article-content h2, .article-content h3, 
.article-content h4, .article-content h5, .article-content h6 {
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.article-content p {
    margin-bottom: 1.2rem;
}

.article-content img {
    max-width: 100%;
    height: auto;
}

.card-title a:hover {
    text-decoration: underline !important;
}

/* 相关商品推荐样式 - 小广告风格 */
.goods-ad-item {
    transition: all 0.2s ease-in-out;
    padding: 0.75rem;
    border-radius: 6px;
    border: 1px solid transparent;
}

.goods-ad-item:hover {
    transform: translateY(-1px);
    border-color: var(--bs-border-color);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

/* 深色模式适配 */
[data-bs-theme="dark"] .goods-ad-item:hover {
    background-color: rgba(255, 255, 255, 0.03);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

[data-bs-theme="light"] .goods-ad-item:hover,
.goods-ad-item:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.goods-image-ad {
    width: 60px;
    height: 60px;
    flex-shrink: 0;
    overflow: hidden;
    border-radius: 6px;
    border: 1px solid var(--bs-border-color-translucent);
}

/* 深色模式适配 */
[data-bs-theme="dark"] .goods-image-ad {
    background-color: rgba(255, 255, 255, 0.02);
}

[data-bs-theme="light"] .goods-image-ad,
.goods-image-ad {
    background-color: rgba(0, 0, 0, 0.02);
}

.goods-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.2s ease-in-out;
}

.goods-ad-item:hover .goods-image {
    transform: scale(1.05);
}

.goods-title-ad {
    font-size: 0.875rem;
    font-weight: 600;
    line-height: 1.3;
    transition: color 0.2s ease-in-out;
    color: var(--bs-body-color);
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.goods-ad-item:hover .goods-title-ad {
    color: var(--bs-primary);
}

.goods-desc-ad {
    font-size: 0.75rem;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection