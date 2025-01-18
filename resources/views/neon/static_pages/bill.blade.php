@extends('riniba_03.layouts.default')
@section('content')



<main class="content-wrapper">
  <nav class="container pt-3 my-3 my-md-4" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">主页</a></li>
      <li class="breadcrumb-item"><a href="{{ url('/') }}">购物</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ __('dujiaoka.confirm_order') }}</li>
    </ol>
  </nav>

  <!-- Items in the cart + Order summary -->
  <section class="container pb-5 mb-2 mb-md-3 mb-lg-4 mb-xl-5">
    <div class="row">

      <!-- 左侧：商品列表 -->
      <div class="col-lg-8">
        <div class="pe-lg-2 pe-xl-3 me-xl-3">
          <!-- 商品表格 -->
          <table class="table position-relative z-2 mb-4">
            <thead>
              <tr>
                <th scope="col" class="fs-sm fw-normal py-3 ps-0"><span class="text-body">商品</span></th>
                <th scope="col" class="text-body fs-sm fw-normal py-3 d-xl-table-cell"><span class="text-body">价格</span></th>
                <th scope="col" class="text-body fs-sm fw-normal py-3 d-none d-md-table-cell"><span class="text-body">数量</span></th>
                <th scope="col" class="text-body fs-sm fw-normal py-3 d-none d-md-table-cell"><span class="text-body">订单号</span></th>
              </tr>
            </thead>
            <tbody class="align-middle">


              <tr>
                <td class="py-0 ps-0">
                  <div class="d-flex align-items-center">
                    <div class="w-100 min-w-0 ps-2 ps-xl-3">
                      <h5 class="d-flex animate-underline mb-2">
                        <a class="d-block fs-sm fw-medium text-truncate animate-target">
                          {{ $title }} x {{ $buy_amount }}
                        </a>
                      </h5>

                      <ul class="list-unstyled gap-1 fs-xs mb-0">
                        @if(!empty($coupon))
                          <li>
                            <span class="text-body-secondary">{{ __('order.fields.coupon_id') }}:</span>
                            <span class="text-dark-emphasis fw-medium">
                              {{ $coupon['coupon'] }}
                            </span>
                          </li>
                        @endif
                        @if($wholesale_discount_price > 0)
                          <li>
                            <span class="text-body-secondary">{{ __('order.fields.wholesale_discount_price') }}:</span>
                            <span class="text-dark-emphasis fw-medium">
                              {{ __('dujiaoka.money_symbol') }}{{ $wholesale_discount_price }}
                            </span>
                          </li>
                        @endif
                        @if(!empty($info))
                          <li>
                            <span class="text-body-secondary">{{ __('dujiaoka.order_information') }}:</span>
                            <span class="text-dark-emphasis fw-medium">
                              {!! $info !!}
                            </span>
                          </li>
                        @endif
                      </ul>
                    </div>
                  </div>
                </td>

                <td class="h6 py-3 d-xl-table-cell">
                  {{ $goods_price }}
                </td>

                <!-- 数量 -->
                <td class="py-2 d-none d-md-table-cell">
                  {{ $buy_amount }}
                </td>

                <!-- 订单号 -->
                <td class="h6 py-3 d-none d-md-table-cell">
                  {{ $order_sn }}
                </td>
              </tr>

            </tbody>
          </table>

        </div>
      </div>

      <!-- 右侧：订单汇总 (sticky sidebar) -->
      <aside class="col-lg-4" style="margin-top: -100px">
        <div class="position-sticky top-0" style="padding-top: 100px">
          <div class="bg-body-tertiary rounded-5 p-4 mb-3">
            <div class="p-sm-2 p-lg-0 p-xl-2">
              <h5 class="border-bottom pb-4 mb-4">订单汇总</h5>

              <ul class="list-unstyled fs-sm gap-3 mb-0">
                <!-- 支付方式 -->
                <li class="d-flex justify-content-between">
                  {{ __('dujiaoka.payment_method') }}:
                  <span class="text-dark-emphasis fw-medium">
                    {{ $pay['pay_name'] ?? '--' }}
                  </span>
                </li>

                <!-- 下单邮箱 -->
                <li class="d-flex justify-content-between">
                  {{ __('order.fields.email') }}:
                  <span class="text-dark-emphasis fw-medium">
                    {{ $email }}
                  </span>
                </li>

                <!-- 发货类型(自动/人工) -->
                <li class="d-flex justify-content-between">
                  {{ __('order.fields.type') }}:
                  @if($type == \App\Models\Order::AUTOMATIC_DELIVERY)
                    <span class="badge bg-success">{{ __('goods.fields.automatic_delivery') }}</span>
                  @else
                    <span class="badge bg-warning">{{ __('goods.fields.manual_processing') }}</span>
                  @endif
                </li>
                <!-- 如果有优惠券折扣 -->
                @if(!empty($coupon))
                  <li class="d-flex justify-content-between">
                    {{ __('order.fields.coupon_id') }}:
                    <span class="text-dark-emphasis fw-medium">
                      {{ $coupon['coupon'] }}
                    </span>
                  </li>
                  <li class="d-flex justify-content-between">
                    {{ __('order.fields.coupon_discount_price') }}:
                    <span class="text-dark-emphasis fw-medium">
                      {{ __('dujiaoka.money_symbol') }}{{ $coupon_discount_price }}
                    </span>
                  </li>
                @endif
              </ul>

              <div class="border-top pt-4 mt-4">
                <!-- 商品总价 (实际支付) -->
                <div class="d-flex justify-content-between mb-3">
                  <span class="fs-sm">{{ __('order.fields.actual_price') }}:</span>
                  <span class="h5 mb-0">
                    {{ __('dujiaoka.money_symbol') }}{{ $actual_price }}
                  </span>
                </div>

                <!-- 支付按钮 (PC端) -->
                <a class="btn btn-lg btn-dark w-100 d-none d-lg-flex"
                   href="{{ url('pay-gateway', [
                     'handle' => urlencode($pay['pay_handleroute']),
                     'payway' => $pay['pay_check'],
                     'orderSN' => $order_sn
                   ]) }}">
                  {{ __('dujiaoka.pay_immediately') }} {{ $actual_price }} {{ __('dujiaoka.money_symbol') }}
                </a>

                <!-- 订单创建时间 / 其他提示 -->
                <div class="nav justify-content-center fs-sm mt-3">
                  <span class="nav-link p-0 me-1 text-decoration-underline">
                    {{ __('order.fields.order_created') }}: {{ $created_at }}
                  </span>
                  &nbsp;
                  <span class="text-dark-emphasis fw-medium ms-1">
                    <!-- 你可以写“订单有效期30分钟”之类 -->
                    {{ __('dujiaoka.confirm_order') }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </section>
</main>

<!-- 底部固定“支付”按钮 (移动端) -->
<div class="fixed-bottom z-sticky w-100 py-2 px-3 bg-body border-top shadow d-lg-none">
  <a class="btn btn-lg btn-dark w-100"
     href="{{ url('pay-gateway', [
       'handle' => urlencode($pay['pay_handleroute']),
       'payway' => $pay['pay_check'],
       'orderSN' => $order_sn
     ]) }}">
    {{ __('dujiaoka.pay_immediately') }} {{ $actual_price }} {{ __('dujiaoka.money_symbol') }}
  </a>
</div>
@stop
@section('js')
@stop
