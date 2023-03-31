@extends('hyper.layouts.default')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <div class="app-search">
                    <div class="position-relative">
                        <input type="text" class="form-control" id="search" placeholder="{{ __('hyper.home_search_box') }}">
                        <span class="uil-search"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card card-body">
    <h4 class="page-title d-md-block">
        {{ __('hyper.notice_announcement') }}
    </h4>
        {!! dujiaoka_config_get('notice') !!}
</div>
<div class="nav nav-list">
    <a href="#group-all" class="tab-link active" data-bs-toggle="tab" aria-expanded="false" role="tab" data-toggle="tab">
        <span class="tab-title">
        {{-- 全部 --}}
        {{ __('hyper.home_whole') }}
        </span>
        <div class="img-checkmark">
            <img src="/assets/hyper/images/check.png">
        </div>
    </a>
    @foreach($data as  $index => $group)
    <a href="#group-{{ $group['id'] }}" class="tab-link" data-bs-toggle="tab" aria-expanded="false" role="tab" data-toggle="tab">
        <span class="tab-title">
            {{ $group['gp_name'] }}
        </span>
        <div class="img-checkmark">
            <img src="/assets/hyper/images/check.png">
        </div>
    </a>
    @endforeach
</div>
<div class="tab-content">
    <div class="tab-pane active" id="group-all">
        <div class="hyper-wrapper">
            @foreach($data as $group)
                @foreach($group['goods'] as $goods)
                    @if($goods['in_stock'] > 0)
                    <a href="{{ url("/buy/{$goods['id']}") }}" class="home-card category">
                    @else
                    <a href="javascript:void(0);" onclick="sell_out_tip()" class="home-card category ribbon-box">
                        <div class="ribbon-two ribbon-two-danger">
                            {{-- 缺货 --}}
                            <span>{{ __('hyper.home_out_of_stock') }}</span>
                        </div>
                    @endif
                        <img class="home-img" src="/assets/hyper/images/loading.gif" data-src="{{ picture_ulr($goods['picture']) }}">
                        <div class="flex">
                            <p class="name">
                                {{ $goods['gd_name'] }}
                            </p>
                            <div class="price">
                                {{ __('hyper.global_currency') }}<b>{{ $goods['actual_price'] }}</b>
                            </div>
                        </div>
                    </a>
                @endforeach
            @endforeach
        </div>
    </div>
    @foreach($data as  $index => $group)
        <div class="tab-pane" id="group-{{ $group['id'] }}">
            <div class="hyper-wrapper">
                @foreach($group['goods'] as $goods)
                    @if($goods['in_stock'] > 0)
                    <a href="{{ url("/buy/{$goods['id']}") }}" class="home-card category">
                    @else
                    <a href="javascript:void(0);" onclick="sell_out_tip()" class="home-card category ribbon-box">
                        <div class="ribbon-two ribbon-two-danger">
                            {{-- 缺货 --}}
                            <span>{{ __('hyper.home_out_of_stock') }}</span>
                        </div>
                    @endif
                        <img class="home-img" src="/assets/hyper/images/loading.gif" data-src="{{ picture_ulr($goods['picture']) }}">
                        <div class="flex">
                            <p class="name">
                                {{ $goods['gd_name'] }}
                            </p>
                            <div class="price">
                                {{ __('hyper.global_currency') }}<b>{{ $goods['actual_price'] }}</b>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
<div class="row">
    <div class="col-12">
    <div class="page-title-box">
        <h4 class="page-title d-md-block">
            {{ __('article.total') }}
        </h4>
    </div>
    </div>
</div>
<div class="article-grid">
<div class="card card-body">
    <div class="article-title">
        <h4>{{ __('article.newest') }}</h4>
        <a href="/article">{{ __('article.more') }}>></a>
    </div>
    <table class="articles">
        <tbody>
            @foreach ($articles as $article)
            <tr>
                <td><a href="{{ url("/article/{$article->link}") }}">{{ $article->title }}</a></td>
                <td>{{ $article->updated_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="card card-body">
    <div class="article-title">
        <h4>{{ __('article.recommend') }}</h4>
        <a href="/article">{{ __('article.more') }}>></a>
    </div>
    <table class="articles">
        <tbody>
            @foreach ($articles as $article)
            <tr>
                <td><a href="{{ url("/article/{$article->link}") }}">{{ $article->title }}</a></td>
                <td>{{ $article->updated_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
    
@stop 
@section('js')
<script>
    $("#search").on("input",function(e){
        var txt = $("#search").val();
        if($.trim(txt)!="") {
            $(".category").hide().filter(":contains('"+txt+"')").show();
        } else {
            $(".category").show();
        }
    });
    function sell_out_tip() {
        $.NotificationApp.send("{{ __('hyper.home_tip') }}","{{ __('hyper.home_sell_out_tip') }}","top-center","rgba(0,0,0,0.2)","info");
    }
</script>
@stop