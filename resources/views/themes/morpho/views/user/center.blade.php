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
                        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim($user->email))) }}?s=80&d=identicon" 
                             alt="{{ $user->nickname ?: $user->email }}"
                             class="rounded-circle mx-auto mb-2 d-block" 
                             style="width: 80px; height: 80px;">
                        <h6 class="mb-1">{{ $user->nickname ?: $user->email }}</h6>
                        <span class="badge text-bg-{{ $user->level->color ?? 'secondary' }}">{{ $user->level_name }}</span>
                    </div>
                    
                    <nav class="nav nav-pills flex-column">
                        <a href="{{ route('user.center') }}" class="nav-link active">
                            <i class="ci-home me-2"></i>概览
                        </a>
                        <a href="{{ route('user.orders') }}" class="nav-link">
                            <i class="ci-package me-2"></i>我的订单
                        </a>
                        <a href="{{ route('user.balance') }}" class="nav-link">
                            <i class="ci-credit-card me-2"></i>余额管理
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
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- 统计卡片 -->
            <div class="row mb-4">
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="ci-package text-primary mb-2" style="font-size: 2rem;"></i>
                            <h4 class="mb-1">{{ $stats['total_orders'] }}</h4>
                            <p class="text-muted mb-0">总订单数</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="ci-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                            <h4 class="mb-1">{{ $stats['completed_orders'] }}</h4>
                            <p class="text-muted mb-0">已完成订单</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="ci-dollar-sign text-warning mb-2" style="font-size: 2rem;"></i>
                            <h4 class="mb-1">¥{{ number_format($stats['total_spent'], 2) }}</h4>
                            <p class="text-muted mb-0">累计消费</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="ci-credit-card text-info mb-2" style="font-size: 2rem;"></i>
                            <h4 class="mb-1">¥{{ number_format($stats['current_balance'], 2) }}</h4>
                            <p class="text-muted mb-0">账户余额</p>
                            <a href="{{ route('user.recharge') }}" class="btn btn-sm btn-outline-primary mt-2">充值</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 等级进度 -->
            @if($nextLevel)
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="card-title">等级进度</h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>{{ $user->level_name }}</span>
                        <span>{{ $nextLevel->name }}</span>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar" role="progressbar" style="width: {{ $upgradeProgress }}%"></div>
                    </div>
                    <small class="text-muted">
                        还需消费 ¥{{ number_format($nextLevel->min_spent - $user->total_spent, 2) }} 即可升级到 {{ $nextLevel->name }}
                    </small>
                </div>
            </div>
            @endif

            <!-- 最近订单 -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">最近订单</h6>
                    <a href="{{ route('user.orders') }}" class="btn btn-sm btn-outline-primary">查看全部</a>
                </div>
                <div class="card-body">
                    @forelse($recentOrders as $order)
                    <a href="{{ route('user.order.detail', $order->order_sn) }}" 
                       class="d-flex justify-content-between align-items-center text-decoration-none text-dark order-item">
                        <div>
                            <div class="fw-medium text-primary">{{ $order->order_sn }}</div>
                            <small class="text-muted">{{ $order->goods_summary ?? $order->orderItems->pluck('goods_name')->implode('、') }}</small>
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
                                {{ \App\Models\Order::getStatusMap()[$order->status] ?? '未知' }}
                            </small>
                        </div>
                    </a>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="ci-package fs-1 mb-2 d-block"></i>
                        <p>暂无订单</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- 余额记录 -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">余额记录</h6>
                    <a href="{{ route('user.balance') }}" class="btn btn-sm btn-outline-primary">查看全部</a>
                </div>
                <div class="card-body">
                    @forelse($recentBalanceRecords as $record)
                    <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div>
                            <div class="fw-medium">{{ $record->type_text }}</div>
                            <small class="text-muted">{{ $record->description }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-medium {{ $record->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $record->signed_amount }}
                            </div>
                            <small class="text-muted">{{ $record->created_at->format('m-d H:i') }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="ci-credit-card fs-1 mb-2 d-block"></i>
                        <p>暂无余额记录</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</main>
@endsection