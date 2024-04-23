@extends('hyper.layouts.default')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">{{ isset($title) ? __('article.article') : __('article.total') }}</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card card-body">
            @if (empty($content))
            <div class="tab-pane show active" id="bordered-tabs-preview">
                <ul class="nav nav-tabs nav-bordered mb-3">
                    <li class="nav-item">
                        <a href="#all" data-toggle="tab" aria-expanded="false" class="nav-link active">
                            <span>全部文章</span>
                        </a>
                    </li>
                    @foreach ($category as $index => $key)
                    <li class="nav-item">
                        <a href="#category{{ $index }}" data-toggle="tab" aria-expanded="false" class="nav-link">
                            <span>{{ $key }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
                <div class="tab-content articles">
                    <div class="tab-pane show active" id="all">
                        @foreach ($articles as $one)
                            @foreach ($one as $article)
                                <div>
                                    <h2>{{ $article->title }}</h5>
                                    <p>{{ $article->summary }}</p>
                                    <a href="{{ url('article/'.$article->link) }}">阅读更多</a>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                    @foreach ($articles as $index => $one)
                    <div class="tab-pane" id="category{{ $loop->index }}">
                        @foreach ($one as $article)
                            <div>
                                <h2>{{ $article->title }}</h5>
                                <p>{{ $article->summary }}</p>
                                <a href="{{ url('article/'.$article->link) }}">阅读更多</a>
                            </div>
                           @endforeach
                    </div>
                    @endforeach
                </div>                                          
            </div>
            @else
                <div class="row articles">
                    <h3>{{ $title }}</h3>
                </div>
             <hr/>
            {!! $content !!}
            @endif
        </div>
    </div>
</div>
@stop
