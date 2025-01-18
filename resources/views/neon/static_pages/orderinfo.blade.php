@extends('riniba_03.layouts.default')

@section('content')
<main class="content-wrapper">
  <nav class="container pt-2 pt-xxl-3 my-3 my-md-4" aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">主页</a></li>
      <li class="breadcrumb-item"><a href="/">购物</a></li>
      <li class="breadcrumb-item active" aria-current="page">订单详情</li>
    </ol>
  </nav>

  <div class="container">
    <!-- row-cols-1 row-cols-lg-2 表示：在小屏 1 列，大屏 2 列，可按需修改 -->
    <div class="row row-cols-1 row-cols-lg-2 g-0 mx-auto" style="max-width: 1920px">
      @foreach($orders as $index => $order)
        <div class="col d-flex flex-column justify-content-center py-2">
          <div class="w-100 pt-sm-2 pt-md-3 pt-lg-4 pb-lg-4 pb-xl-5 px-3 px-sm-4 pe-lg-0 ps-lg-4 mx-auto ms-lg-auto me-lg-4"
               style="max-width: 740px">
            <!-- 顶部标题区，显示订单号和状态 -->
            <div class="d-flex align-items-sm-center border-bottom">
              <div class="d-flex align-items-center justify-content-center bg-success text-white rounded-circle flex-shrink-0"
                   style="width: 3rem; height: 3rem; margin-top: -.125rem">
                <i class="ci-check fs-4"></i>
              </div>
              <div class="w-100 ps-3">
                <div class="fs-sm mb-1">
                  订单号：{{ $order['order_sn'] }}
                </div>
                <div class="d-sm-flex align-items-center">
                  <div class="h5 mb-0 me-3">
                    订单状态：
                    <div class="nav mt-2 mt-sm-0 ms-auto d-inline">
                      <span class="fs-lg mb-0 d-inline">
                        {{-- 根据 $order['status'] 输出中文或翻译 --}}
                        @switch($order['status'])
                          @case(\App\Models\Order::STATUS_EXPIRED)
                            已过期
                            @break
                          @case(\App\Models\Order::STATUS_WAIT_PAY)
                            待支付
                            @break
                          @case(\App\Models\Order::STATUS_PENDING)
                            待处理
                            @break
                          @case(\App\Models\Order::STATUS_PROCESSING)
                            已处理
                            @break
                          @case(\App\Models\Order::STATUS_COMPLETED)
                            已完成
                            @break
                          @case(\App\Models\Order::STATUS_FAILURE)
                            已失败
                            @break
                          @case(\App\Models\Order::STATUS_ABNORMAL)
                            状态异常
                            @break
                          @default
                            未知状态
                        @endswitch
                      </span>
                      <!-- 如果是待支付，可以显示“重新支付”按钮 -->
                      @if($order['status'] == \App\Models\Order::STATUS_WAIT_PAY)
                        <button class="btn btn-dark btn-sm ms-3"
                          onclick="window.location.href='{{ url('/bill/'.$order['order_sn']) }}'">
                          重新支付
                        </button>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- 中部：显示订单的其他信息 (名称、时间、邮箱、类型、总价、支付方式等) -->
            <div class="d-flex flex-column gap-2 pt-3">
              <div>
                <h3 class="h6 mb-2 d-inline">订单名称：</h3>
                <span class="fs-sm mb-0 d-inline">
                  {{ $order['title'] }} x {{ $order['buy_amount'] }}
                </span>
              </div>
              <div>
                <h3 class="h6 mb-2 d-inline">下单时间：</h3>
                <span class="fs-sm mb-0 d-inline">
                  {{ $order['created_at'] }}
                </span>
              </div>
              <div>
                <h3 class="h6 mb-2 d-inline">下单邮箱：</h3>
                <span class="fs-sm mb-0 d-inline">
                  {{ $order['email'] }}
                </span>
              </div>
              <div>
                <h3 class="h6 mb-2 d-inline">订单类型：</h3>
                <span class="fs-sm mb-0 d-inline">
                  @if($order['type'] == \App\Models\Order::AUTOMATIC_DELIVERY)
                    自动发货
                  @else
                    人工发货
                  @endif
                </span>
              </div>
              <div>
                <h3 class="h6 mb-2 d-inline">订单总价：</h3>
                <span class="fs-sm mb-0 d-inline">
                  {{ $order['actual_price'] }}
                </span>
              </div>
              <div>
                <h3 class="h6 mb-2 d-inline">支付方式：</h3>
                <span class="fs-sm mb-0 d-inline">
                  {{ $order['pay']['pay_name'] ?? '' }}
                </span>
              </div>
            </div>

            <!-- 卡密信息区 -->
            <div class="bg-success rounded px-2 py-2" style="--cz-bg-opacity: .2">
              <div class="py-3">
                <h2 class="h6 text-center pb-2 mb-1">🎉 您的卡密信息 🎉</h2>
                <!-- 注意：需要给每个订单的 textarea 独立 ID，否则复制功能只会对第一个有效 -->
                @php
                  $textareaID = "kami-textarea-{$index}";
                  $btnID = "kami-btn-{$index}";
                @endphp
                <textarea class="form-control mb-4" id="{{ $textareaID }}" rows="5">{{ $order['info'] }}</textarea>

                <div class="d-flex gap-3 w-100">
                  <!-- 复制按钮 data-copy-text-from 用来绑定到 textareaID -->
                  <button type="button" class="btn btn-dark w-50 kami-btn"
                          data-copy-text-from="#{{ $textareaID }}">
                    复制卡密信息
                  </button>
                  <button type="button" class="btn btn-dark w-50 kami-btn"
                          data-copy-text-from="#{{ $textareaID }}">
                    再次复制卡密信息
                  </button>
                </div>
              </div>
            </div>

          </div>
        </div>
      @endforeach
    </div>
  </div>
</main>
@stop

@section('js')
<script>
document.querySelectorAll('.kami-btn').forEach(function(btn) {
  let targetSelector = btn.getAttribute('data-copy-text-from');
  let clipboard = new ClipboardJS(btn, {
    target: () => document.querySelector(targetSelector)
  });
  clipboard.on('success', function(e) {
    alert("{{ __('dujiaoka.prompt.copy_text_success') }}");
    e.clearSelection();
  });
  clipboard.on('error', function(e) {
    alert("{{ __('dujiaoka.prompt.copy_text_failed') }}");
  });
});
</script>
@stop
