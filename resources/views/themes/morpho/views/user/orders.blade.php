@extends('morpho::layouts.default')

@section('content')
<style>
.order-item {
    margin: 4px;
    padding: 12px;
    border-radius: 8px;
}
.order-item:hover {
    background-color: #f8f9fa;
}
</style>
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
                            <i class="ci-credit-card me-2"></i>余额管理
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
                    <a href="{{ route('user.order.detail', $order->order_sn) }}" 
                       class="d-flex justify-content-between align-items-center text-decoration-none text-dark order-item">
                        <div>
                            <div class="fw-medium text-primary">{{ $order->order_sn }}</div>
                            <small class="text-muted">{{ $order->goods_summary ?? $order->orderItems->pluck('goods_name')->implode('、') }}</small>
                            <div class="mt-1">
                                <small class="text-muted">{{ $order->created_at->format('Y-m-d H:i:s') }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-medium">¥{{ number_format($order->actual_price, 2) }}</div>
                            @php
                                $statusClass = match($order->status) {
                                    1 => 'warning',
                                    2, 3 => 'info', 
                                    4 => 'success',
                                    5, 6, -1 => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <small class="badge text-bg-{{ $statusClass }}">
                                {{ \App\Models\Order::getStatusMap()[$order->status] ?? '未知状态' }}
                            </small>
                        </div>
                    </a>
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