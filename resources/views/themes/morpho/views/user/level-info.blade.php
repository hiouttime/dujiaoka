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
                        <h6 class="mb-1">{{ $user->nickname ?: $user->email }}</h6>
                        <span class="badge text-bg-secondary">{{ $user->level_name }}</span>
                    </div>
                    
                    <nav class="nav nav-pills flex-column">
                        <a href="{{ route('user.center') }}" class="nav-link">
                            <i class="ci-home me-2"></i>概览
                        </a>
                        <a href="{{ route('user.orders') }}" class="nav-link">
                            <i class="ci-package me-2"></i>我的订单
                        </a>
                        <a href="{{ route('user.balance') }}" class="nav-link">
                            <i class="ci-credit-card me-2"></i>余额管理
                        </a>
                        <a href="{{ route('user.recharge') }}" class="nav-link">
                            <i class="ci-credit-card me-2"></i>余额充值
                        </a>
                        <a href="{{ route('user.level') }}" class="nav-link active">
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
            <!-- 当前等级 -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">我的等级</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            <div class="level-badge mx-auto mb-3" 
                                 style="width: 120px; height: 120px; background: {{ $user->level->color ?? '#6b7280' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="ci-star text-white" style="font-size: 3rem;"></i>
                            </div>
                            <h4 class="mb-1">{{ $user->level_name }}</h4>
                            <p class="text-muted">当前等级</p>
                        </div>
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="text-primary">¥{{ number_format($user->total_spent, 2) }}</h5>
                                            <p class="mb-0">累计消费</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="text-success">{{ round((1 - $user->discount_rate) * 100, 1) }}%</h5>
                                            <p class="mb-0">专享折扣</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($user->level->description)
                            <div class="mt-3">
                                <h6>等级说明</h6>
                                <p class="text-muted">{{ $user->level->description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 升级进度 -->
            @php
                $nextLevel = $user->level->getNextLevel();
            @endphp
            @if($nextLevel)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">升级进度</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-center">
                            <div class="level-mini-badge mx-auto mb-2" 
                                 style="width: 60px; height: 60px; background: {{ $user->level->color ?? '#6b7280' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="ci-star text-white"></i>
                            </div>
                            <small>{{ $user->level_name }}</small>
                        </div>
                        
                        <div class="flex-grow-1 mx-4">
                            @php
                                $currentLevelSpent = $user->level->min_spent;
                                $nextLevelSpent = $nextLevel->min_spent;
                                $userSpent = $user->total_spent;
                                $progress = 0;
                                if ($nextLevelSpent > $currentLevelSpent) {
                                    $progress = min(100, (($userSpent - $currentLevelSpent) / ($nextLevelSpent - $currentLevelSpent)) * 100);
                                }
                            @endphp
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $progress }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">¥{{ number_format($currentLevelSpent, 0) }}</small>
                                <small class="text-muted">¥{{ number_format($nextLevelSpent, 0) }}</small>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <div class="level-mini-badge mx-auto mb-2" 
                                 style="width: 60px; height: 60px; background: {{ $nextLevel->color }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="ci-star text-white"></i>
                            </div>
                            <small>{{ $nextLevel->name }}</small>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <p class="mb-2">还需消费 <strong class="text-primary">¥{{ number_format($nextLevelSpent - $userSpent, 2) }}</strong> 即可升级到 <strong>{{ $nextLevel->name }}</strong></p>
                        <p class="text-muted small">升级后可享受 {{ round((1 - $nextLevel->discount_rate) * 100, 1) }}% 折扣</p>
                    </div>
                </div>
            </div>
            @else
            <div class="card mb-4">
                <div class="card-body text-center">
                    <i class="ci-trophy text-warning mb-3" style="font-size: 3rem;"></i>
                    <h5>恭喜您已达到最高等级！</h5>
                    <p class="text-muted">您已经是我们最尊贵的用户，感谢您的支持！</p>
                </div>
            </div>
            @endif

            <!-- 等级说明 -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">等级体系说明</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($allLevels as $level)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 {{ $user->level_id == $level->id ? 'border-primary' : '' }}">
                                <div class="card-body text-center">
                                    <div class="level-mini-badge mx-auto mb-3" 
                                         style="width: 80px; height: 80px; background: {{ $level->color }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="ci-star text-white" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <h6>{{ $level->name }}</h6>
                                    @if($user->level_id == $level->id)
                                        <span class="badge text-bg-primary mb-2">当前等级</span>
                                    @endif
                                    <p class="text-muted small mb-2">消费满 ¥{{ number_format($level->min_spent, 0) }} 升级</p>
                                    <p class="text-success small mb-2">享受 {{ round((1 - $level->discount_rate) * 100, 1) }}% 折扣</p>
                                    @if($level->description)
                                        <p class="text-muted small">{{ $level->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4">
                        <h6>升级规则</h6>
                        <ul class="text-muted small">
                            <li>等级根据累计消费金额自动升级</li>
                            <li>只有已完成的订单才计入累计消费</li>
                            <li>等级升级后立即享受对应折扣</li>
                            <li>等级不会降级，一旦达到永久保持</li>
                            <li>折扣可与优惠券叠加使用</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
</main>
@endsection