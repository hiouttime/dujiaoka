@extends('morpho::layouts.default')

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
    <!-- 宽屏幕双列布局：左侧订单信息，右侧商品详细 -->
    <div class="row g-4 mx-auto" style="max-width: 1920px">
      @foreach($orders as $index => $order)
        <!-- 左侧：订单基本信息 -->
        <div class="col-12 col-lg-5">
          <div class="w-100 pt-sm-2 pt-md-3 pt-lg-4 pb-lg-4 pb-xl-5 px-3 px-sm-4">
            <!-- 订单号和状态 -->
            <div class="border-bottom mb-4 pb-3">
              <div class="mb-3">
                <h2 class="h4 fw-bold mb-1">订单号：{{ $order->order_sn }}</h2>
                <div class="h6 mb-0">
                  订单状态：
                  @switch($order->status)
                    @case(\App\Models\Order::STATUS_EXPIRED)
                      <span class="text-muted">已过期</span>
                      @break
                    @case(\App\Models\Order::STATUS_WAIT_PAY)
                      <span class="text-warning">待支付</span>
                      @break
                    @case(\App\Models\Order::STATUS_PENDING)
                      <span class="text-info">待处理</span>
                      @break
                    @case(\App\Models\Order::STATUS_PROCESSING)
                      <span class="text-primary">处理中</span>
                      @break
                    @case(\App\Models\Order::STATUS_COMPLETED)
                      <span class="text-success">已完成</span>
                      @break
                    @case(\App\Models\Order::STATUS_FAILURE)
                      <span class="text-danger">已失败</span>
                      @break
                    @case(\App\Models\Order::STATUS_ABNORMAL)
                      <span class="text-danger">状态异常</span>
                      @break
                    @default
                      <span class="text-muted">未知状态</span>
                  @endswitch
                </div>
              </div>
              
              <!-- 如果是待支付，显示重新支付按钮 -->
              @if($order->status == \App\Models\Order::STATUS_WAIT_PAY)
                <button class="btn btn-primary btn-sm"
                  onclick="window.location.href='{{ url('/bill/'.$order->order_sn) }}'">
                  重新支付
                </button>
              @endif
            </div>

            <!-- 订单基本信息 -->
            <div class="mb-4">
              <h3 class="h6 mb-3">订单信息</h3>
              <div class="d-flex flex-column gap-3">
                <div class="d-flex justify-content-between">
                  <span class="text-muted">下单时间：</span>
                  <span>{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="text-muted">下单邮箱：</span>
                  <span>{{ $order->email }}</span>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="text-muted">订单总价：</span>
                  <span class="fw-bold text-success">¥{{ $order->actual_price }}</span>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="text-muted">支付方式：</span>
                  <span>{{ $order->pay->pay_name ?? '' }}</span>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="text-muted">商品数量：</span>
                  <span>{{ $order->orderItems->count() }} 种商品</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 右侧：商品详细信息 -->
        <div class="col-12 col-lg-7">
          <div class="w-100 pt-sm-2 pt-md-3 pt-lg-4 pb-lg-4 pb-xl-5 px-3 px-sm-4">
            <h3 class="h6 mb-3">商品列表</h3>
            
            @foreach($order->orderItems as $itemIndex => $item)
              <div class="border rounded p-3 mb-4">
                <!-- 商品基本信息 -->
                <div class="mb-3">
                  <h4 class="h6 mb-2">{{ $item->goods_name }}</h4>
                  <div class="small text-muted mb-2">
                    单价：¥{{ $item->unit_price }} × {{ $item->quantity }} = ¥{{ $item->subtotal }}
                  </div>
                  <div class="small">
                    <span class="badge bg-{{ $item->type == 1 ? 'success' : 'warning' }}">
                      {{ $item->type == 1 ? '自动发货' : '人工发货' }}
                    </span>
                  </div>
                </div>
                
                @if($item->goods->usage_instructions)
                  <!-- 使用说明 -->
                  <div class="bg-light rounded p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                      <h5 class="h6 mb-0">📋 使用说明</h5>
                      <button class="btn btn-sm btn-outline-secondary" 
                              type="button" 
                              data-bs-toggle="collapse" 
                              data-bs-target="#usage-{{ $index }}-{{ $itemIndex }}" 
                              aria-expanded="false">
                        <i class="ci-chevron-down" style="transition: transform 0.2s ease;"></i>
                      </button>
                    </div>
                    <div id="usage-{{ $index }}-{{ $itemIndex }}" class="collapse">
                      <div class="small text-muted mt-3">
                        {!! nl2br(e($item->goods->usage_instructions)) !!}
                      </div>
                    </div>
                  </div>
                @endif

                @if($item->info)
                  <!-- 商品详情/卡密信息 -->
                  <div class="border rounded p-3">
                    <div class="d-flex justify-content-between align-items-center">
                      <h5 class="h6 mb-0">商品信息</h5>
                      <button class="btn btn-sm btn-outline-secondary" 
                              type="button" 
                              data-bs-toggle="collapse" 
                              data-bs-target="#info-{{ $index }}-{{ $itemIndex }}" 
                              aria-expanded="false">
                        <i class="ci-chevron-down" style="transition: transform 0.2s ease;"></i>
                      </button>
                    </div>
                    <div id="info-{{ $index }}-{{ $itemIndex }}" class="collapse">
                      @php
                        $textareaID = "kami-textarea-{$index}-{$itemIndex}";
                      @endphp
                      <div class="mt-3">
                        <textarea class="form-control mb-3" id="{{ $textareaID }}" rows="6" readonly>{{ $item->info }}</textarea>
                        <div class="d-flex gap-2">
                          <button type="button" class="btn btn-dark btn-sm kami-btn flex-grow-1"
                                  data-copy-text-from="#{{ $textareaID }}">
                            复制信息
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                @else
                  <!-- 没有卡密信息时的提示 -->
                  <div class="text-center text-muted py-3 bg-light rounded">
                    <small>商品未发货或暂无卡密信息</small>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>
  </div>
</main>
@stop

@section('js')
<script>
// 复制功能 - 使用ClipboardJS
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

// Bootstrap折叠动画完成后旋转图标
document.addEventListener('DOMContentLoaded', function() {
  const collapseElements = document.querySelectorAll('.collapse');
  collapseElements.forEach(function(collapse) {
    collapse.addEventListener('shown.bs.collapse', function() {
      const button = document.querySelector('[data-bs-target="#' + collapse.id + '"]');
      if (button) {
        const icon = button.querySelector('i');
        if (icon) icon.style.transform = 'rotate(180deg)';
      }
    });
    
    collapse.addEventListener('hidden.bs.collapse', function() {
      const button = document.querySelector('[data-bs-target="#' + collapse.id + '"]');
      if (button) {
        const icon = button.querySelector('i');
        if (icon) icon.style.transform = 'rotate(0deg)';
      }
    });
  });
});
</script>
@stop
