@extends('unicorn.layouts.default')
@section('content')
<section class="main-container">
        <div class="container">
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">{{ isset($title) ? __('article.article') : __('article.total') }}</h4>
        </div>
    </div>
</div>
<div class="card card-body">
    @if (empty($content))
    <table class="table article-list">
    <thead>
        <tr>
            <th>{{ __('article.fields.title') }}</th>
            <th>{{ __('admin.updated_at') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($articles as $article)
        <tr>
            <td>
                <div>
                    <a href="{{ url("/article/{$article->link}") }}">{{ $article->title }}</a>
                </div>
                <div class="summary">{{ $article->getSummary() }}</div>
            </td>
            <td>{{ $article->updated_at }}</td>
        </tr>
        @endforeach
    </tbody>
    </table>
    @else
        <div class="row articles">
            <h3>{{ $title }}</h3>
        </div>
        <hr/>
        {!! $content !!}
    @endif
</div>
</div>
</section>
@endsection