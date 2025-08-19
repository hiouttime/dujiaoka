@extends('morpho::layouts.default')

@section('content')
<main class="content-wrapper">
    <section class="container pt-4 pb-5">
        <div class="row">
        <!-- 侧边栏 -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-placeholder bg-primary rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="ci-user text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h6 class="mb-1">{{ auth('web')->user()->nickname ?: auth('web')->user()->email }}</h6>
                        <span class="badge text-bg-secondary">{{ auth('web')->user()->level_name }}</span>
                    </div>
                    
                    <nav class="nav nav-pills flex-column">
                        <a href="{{ route('user.center') }}" class="nav-link">
                            <i class="ci-home me-2"></i>概览
                        </a>
                        <a href="{{ route('user.orders') }}" class="nav-link active">
                            <i class="ci-package me-2"></i>我的订单
                        </a>
                        <a href="{{ route('user.balance') }}" class="nav-link">
                            <i class="ci-wallet me-2"></i>余额管理
                        </a>
                        <a href="{{ route('user.recharge') }}" class="nav-link">
                            <i class="ci-credit-card me-2"></i>余额充值
                        </a>
                        <a href="{{ route('user.level') }}" class="nav-link">
                            <i class="ci-star me-2"></i>等级信息
                        </a>
                        <a href="{{ route('user.profile') }}" class="nav-link">
                            <i class="ci-user me-2"></i>个人资料
                        </a>
                        <a href="{{ route('user.change-password') }}" class="nav-link">
                            <i class="ci-lock me-2"></i>修改密码
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- 主内容 -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">我的订单</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ request()->fullUrlWithQuery(['status' => '']) }}" 
                           class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">全部</a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => '1']) }}" 
                           class="btn {{ request('status') == '1' ? 'btn-primary' : 'btn-outline-primary' }}">待支付</a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => '2']) }}" 
                           class="btn {{ request('status') == '2' ? 'btn-primary' : 'btn-outline-primary' }}">处理中</a>
                        <a href="{{ request()->fullUrlWithQuery(['status' => '4']) }}" 
                           class="btn {{ request('status') == '4' ? 'btn-primary' : 'btn-outline-primary' }}">已完成</a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($orders as $order)
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0 me-3">订单号：{{ $order->order_sn }}</h6>
                                @php
                                    $statusClass = match($order->status) {
                                        1 => 'warning',
                                        2, 3 => 'info', 
                                        4 => 'success',
                                        5, 6, -1 => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge text-bg-{{ $statusClass }}">
                                    {{ \App\Models\Order::getStatusMap()[$order->status] ?? '未知状态' }}
                                </span>
                            </div>
                            <small class="text-muted">{{ $order->created_at->format('Y-m-d H:i:s') }}</small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6>商品信息</h6>
                                    @foreach($order->orderItems as $item)
                                    <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div>
                                            <div class="fw-medium">{{ $item->goods_name }}</div>
                                            <small class="text-muted">单价：¥{{ number_format($item->goods_price, 2) }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="text-muted">x{{ $item->quantity }}</span>
                                            <div class="fw-medium">¥{{ number_format($item->total_price, 2) }}</div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="col-md-4">
                                    <div class="border-start ps-3">
                                        <h6>订单金额</h6>
                                        <div class="d-flex justify-content-between">
                                            <span>商品总价：</span>
                                            <span>¥{{ number_format($order->total_price, 2) }}</span>
                                        </div>
                                        @if($order->coupon_discount_price > 0)
                                        <div class="d-flex justify-content-between text-success">
                                            <span>优惠券折扣：</span>
                                            <span>-¥{{ number_format($order->coupon_discount_price, 2) }}</span>
                                        </div>
                                        @endif
                                        @if($order->user_discount_amount > 0)
                                        <div class="d-flex justify-content-between text-success">
                                            <span>等级折扣：</span>
                                            <span>-¥{{ number_format($order->user_discount_amount, 2) }}</span>
                                        </div>
                                        @endif
                                        @if($order->balance_used > 0)
                                        <div class="d-flex justify-content-between text-info">
                                            <span>余额支付：</span>
                                            <span>-¥{{ number_format($order->balance_used, 2) }}</span>
                                        </div>
                                        @endif
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>实付金额：</span>
                                            <span class="text-primary">¥{{ number_format($order->actual_price, 2) }}</span>
                                        </div>
                                        
                                        @if($order->pay)
                                        <div class="mt-2">
                                            <small class="text-muted">支付方式：{{ $order->pay->name }}</small>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('user.order.detail', $order->order_sn) }}" 
                                   class="btn btn-sm btn-outline-primary">查看详情</a>
                                
                                @if($order->status == 1)
                                    <a href="{{ route('pay.checkout', $order->order_sn) }}" 
                                       class="btn btn-sm btn-primary">立即支付</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="ci-package fs-1 mb-3 d-block"></i>
                        <h6>暂无订单</h6>
                        <p>您还没有创建任何订单</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">去购物</a>
                    </div>
                    @endforelse
                </div>
                
                @if($orders->hasPages())
                <div class="card-footer">
                    {{ $orders->links() }}
                </div>
                @endif
            </div>
        </div>
        </div>
    </section>
</main>
@endsection