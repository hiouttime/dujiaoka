@extends('hyper.layouts.default')
@section('content')
<style>
@media (max-width: 767.98px){
    .page-title-box .page-title-right {
        width: 100%;
    }
    .page-title-right {
        margin-bottom: 17px;
    }
    .app-search {
        width: 100%;
    }
    .phone {
        display: none;
    }
}
.notice img {
    max-width: 288px;
    height: auto;
}
</style>

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
            <h4 class="page-title d-none d-md-block">{{ __('hyper.home_title') }}</h4>
        </div>
    </div>
</div>
<div class="row">
	<div class="col-12">
        <div class="card">
            <div class="card-body">
            	<h4 class="header-title mb-3">{{ __('hyper.notice_announcement') }}</h4>
                <div class="notice">{!! dujiaoka_config_get('notice') !!}</div>
            </div>
        </div>
    </div>
</div>
    @foreach($data as $group)
    @if(count($group['goods']) > 0)
    <div class="row category">
        <div class="col-md-12">
            <h3>
                {{-- 分类名称 --}}
                <span class="badge badge-info">{{ $group['gp_name'] }}</span>
            </h3>
        </div>
        <div class="col-md-12">
            <div class="card pl-1 pr-1">
                <table class="table table-centered mb-0">
                    <thead>
                        <tr>
                            {{-- 名称 --}}
                            <th width="40%">{{ __('hyper.home_product_name') }}</th>
                            {{-- 类型 --}}
                            <th width="10%" class="phone">{{ __('hyper.home_product_class') }}</th>
                            {{-- 库存 --}}
                            <th width="10%" class="phone">{{ __('hyper.home_in_stock') }}</th>
                            {{-- 价格 --}}
                            <th width="10%">{{ __('hyper.home_price') }}</th>
                            {{-- 操作 --}}
                            <th width="10%" class="text-center">{{ __('hyper.home_place_an_order') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group['goods'] as $goods)
                        <tr class="category">
                            <td class="d-none">{{ $group['gp_name'] }}-{{ $goods['gd_name'] }}</td>
                            <td class="table-user">
                                {{-- 商品名称 --}}
                                @if($goods['in_stock'] > 0)
                                <a href="{{ url("/buy/{$goods['id']}") }}" class="text-body">
                                <img src="{{ picture_ulr($goods['picture']) }}" class="mr-2 avatar-sm">
                                    {{ $goods['gd_name'] }}
                                </a>
                                @else
                                <a href="javascript:void(0);" class="text-body" onclick="sell_out_tip()">
                                <img src="{{ picture_ulr($goods['picture']) }}" class="mr-2 avatar-sm">
                                    {{ $goods['gd_name'] }}
                                </a>
                                @endif
                                @if($goods['wholesale_price_cnf'])
                                    {{-- 折扣 --}}
                                    <span class="badge badge-outline-warning">{{ __('hyper.home_discount') }}</span>
                                @endif
                            </td>
                            <td class="phone">
                                @if($goods['type'] == \App\Models\Goods::AUTOMATIC_DELIVERY)
                                    {{-- 自动发货 --}}
                                    <span class="badge badge-outline-primary">{{ __('hyper.home_automatic_delivery') }}</span>
                                @else
                                    {{-- 人工发货 --}}
                                    <span class="badge badge-outline-danger">{{ __('hyper.home_charge') }}</span>
                                @endif
                            </td>
                            {{-- 库存 --}}
                            <td class="phone">
                                @if($goods['in_stock'] > 0)
                                    库存充足
                                @else
                                    库存不足
                                @endif
                            </td>
                            {{-- 价格 --}}
                            <td>￥<b>{{ $goods['sell_price'] }}</b></td>
                            <td class="text-center">
                                @if($goods['in_stock'] > 0)
                                    {{-- 购买 --}}
                                    <a class="btn btn-outline-primary" href="{{ url("/buy/{$goods['id']}") }}">{{ __('hyper.home_buy') }}</a>
                                @else
                                    {{-- 缺货 --}}
                                    <a class="btn btn-outline-secondary disabled" href="javascript:void(0);">{{ __('hyper.home_out_of_stock') }}</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    @endforeach
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