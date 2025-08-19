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
                        <a href="{{ route('user.orders') }}" class="nav-link">
                            <i class="ci-package me-2"></i>我的订单
                        </a>
                        <a href="{{ route('user.balance') }}" class="nav-link active">
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
            <!-- 余额统计 -->
            <div class="row mb-4">
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="ci-wallet text-primary mb-2" style="font-size: 2rem;"></i>
                            <h4 class="text-primary">¥{{ number_format($stats['current_balance'], 2) }}</h4>
                            <p class="text-muted mb-0">当前余额</p>
                            <a href="{{ route('user.recharge') }}" class="btn btn-sm btn-outline-primary mt-2">立即充值</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="ci-plus-circle text-success mb-2" style="font-size: 2rem;"></i>
                            <h4 class="text-success">¥{{ number_format($stats['total_recharge'], 2) }}</h4>
                            <p class="text-muted mb-0">累计充值</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="ci-minus-circle text-danger mb-2" style="font-size: 2rem;"></i>
                            <h4 class="text-danger">¥{{ number_format($stats['total_consume'], 2) }}</h4>
                            <p class="text-muted mb-0">累计消费</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3 mb-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="ci-refresh-cw text-info mb-2" style="font-size: 2rem;"></i>
                            <h4 class="text-info">¥{{ number_format($stats['total_refund'], 2) }}</h4>
                            <p class="text-muted mb-0">累计退款</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 余额记录 -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">余额明细</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ request()->fullUrlWithQuery(['type' => '']) }}" 
                           class="btn {{ !request('type') ? 'btn-primary' : 'btn-outline-primary' }}">全部</a>
                        <a href="{{ request()->fullUrlWithQuery(['type' => 'recharge']) }}" 
                           class="btn {{ request('type') == 'recharge' ? 'btn-primary' : 'btn-outline-primary' }}">充值</a>
                        <a href="{{ request()->fullUrlWithQuery(['type' => 'consume']) }}" 
                           class="btn {{ request('type') == 'consume' ? 'btn-primary' : 'btn-outline-primary' }}">消费</a>
                        <a href="{{ request()->fullUrlWithQuery(['type' => 'refund']) }}" 
                           class="btn {{ request('type') == 'refund' ? 'btn-primary' : 'btn-outline-primary' }}">退款</a>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($records as $record)
                    <div class="d-flex justify-content-between align-items-center py-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <i class="{{ $record->icon }} {{ $record->type_class }} fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-medium">{{ $record->type_text }}</div>
                                <small class="text-muted">{{ $record->description }}</small>
                                @if($record->related_order_sn)
                                    <div>
                                        <small class="text-muted">订单号：</small>
                                        <a href="{{ route('user.order.detail', $record->related_order_sn) }}" 
                                           class="text-decoration-none small">{{ $record->related_order_sn }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-medium {{ $record->amount >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $record->signed_amount }}
                            </div>
                            <small class="text-muted">{{ $record->created_at->format('Y-m-d H:i:s') }}</small>
                            <div>
                                <small class="text-muted">余额：¥{{ number_format($record->balance_after, 2) }}</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="ci-file-text fs-1 mb-3 d-block"></i>
                        <h6>暂无余额记录</h6>
                        <p>您还没有任何余额变动记录</p>
                        <a href="{{ route('user.recharge') }}" class="btn btn-primary">立即充值</a>
                    </div>
                    @endforelse
                </div>
                
                @if($records->hasPages())
                <div class="card-footer">
                    {{ $records->links() }}
                </div>
                @endif
            </div>
        </div>
        </div>
    </section>
</main>
@endsection