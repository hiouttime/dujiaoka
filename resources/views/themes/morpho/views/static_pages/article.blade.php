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
</style>
@endsection