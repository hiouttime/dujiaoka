@extends('morpho::layouts.default')
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
                <th scope="col" class="text-body fs-sm fw-normal py-3 d-none d-sm-table-cell"><span class="text-body">单价</span></th>
                <th scope="col" class="text-body fs-sm fw-normal py-3 d-none d-md-table-cell"><span class="text-body">数量</span></th>
                <th scope="col" class="text-body fs-sm fw-normal py-3 d-none d-lg-table-cell"><span class="text-body">类型</span></th>
                <th scope="col" class="text-body fs-sm fw-normal py-3 d-none d-sm-table-cell"><span class="text-body">小计</span></th>
              </tr>
            </thead>
            <tbody class="align-middle">
              @forelse($orderItems as $item)
              <tr>
                <td class="py-3 ps-0">
                  <div class="d-flex align-items-start">
                    <div class="w-100 min-w-0 ps-2 ps-xl-3">
                      <h6 class="mb-1">
                        {{ $item->goods_name }}
                      </h6>
                      
                      @if(!empty($item->info))
                        <div class="mt-2 p-2 bg-light rounded">
                          <small class="text-body-secondary d-block mb-1">订单信息:</small>
                          <small class="text-dark-emphasis">
                            {!! $item->info !!}
                          </small>
                        </div>
                      @endif
                    </div>
                  </div>
                </td>

                <!-- 单价 -->
                <td class="h6 py-3 d-none d-sm-table-cell">
                  {{ __('dujiaoka.money_symbol') }}{{ $item->unit_price }}
                </td>

                <!-- 数量 -->
                <td class="py-3 d-none d-md-table-cell">
                  <span class="badge bg-secondary">{{ $item->quantity }}</span>
                </td>

                <!-- 商品类型 -->
                <td class="py-3 d-none d-lg-table-cell">
                  @if($item->type == \App\Models\Order::AUTOMATIC_DELIVERY)
                    <span class="badge bg-success">{{ __('goods.fields.automatic_delivery') }}</span>
                  @else
                    <span class="badge bg-warning">{{ __('goods.fields.manual_processing') }}</span>
                  @endif
                </td>

                <!-- 小计 -->
                <td class="h6 py-3 d-none d-sm-table-cell text-end">
                  {{ __('dujiaoka.money_symbol') }}{{ $item->subtotal }}
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center py-4">
                  <span class="text-muted">没有订单商品</span>
                </td>
              </tr>
              @endforelse

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

              <div class="text-center mb-4 pb-3 border-bottom">
                <div class="fs-6 text-muted mb-1">订单号</div>
                <div class="fs-4 fw-bold font-monospace text-dark">
                  {{ $order_sn }}
                </div>
              </div>

              <ul class="list-unstyled fs-sm gap-3 mb-0">
                <li class="d-flex justify-content-between">
                  <span>{{ __('order.fields.email') }}:</span>
                  <span class="text-dark-emphasis fw-medium">
                    {{ $email }}
                  </span>
                </li>

                <!-- 支付方式 -->
                <li class="d-flex justify-content-between">
                  <span>{{ __('dujiaoka.payment_method') }}:</span>
                  <span class="text-dark-emphasis fw-medium">
                    {{ $pay['pay_name'] ?? '--' }}
                  </span>
                </li>

                <!-- 创建时间 -->
                <li class="d-flex justify-content-between">
                  <span>{{ __('order.fields.order_created') }}:</span>
                  <span class="text-dark-emphasis fw-medium">
                    {{ $created_at }}
                  </span>
                </li>

                <!-- 如果有优惠券折扣 -->
                @if(!empty($coupon))
                  <li class="d-flex justify-content-between">
                    <span>{{ __('order.fields.coupon_id') }}:</span>
                    <span class="text-dark-emphasis fw-medium">
                      {{ $coupon['coupon'] }}
                    </span>
                  </li>
                  <li class="d-flex justify-content-between">
                    <span>{{ __('order.fields.coupon_discount_price') }}:</span>
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
