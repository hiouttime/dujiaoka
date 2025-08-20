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
                        <a href="{{ route('user.balance') }}" class="nav-link">
                            <i class="ci-credit-card me-2"></i>余额管理
                        </a>
                        <a href="{{ route('user.recharge') }}" class="nav-link active">
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
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">账户充值</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6><i class="ci-info-circle me-2"></i>充值说明</h6>
                                <ul class="mb-0">
                                    <li>充值后的余额可用于购买商品</li>
                                    <li>充值金额将实时到账</li>
                                    <li>最低充值金额：¥1.00</li>
                                    <li>最高单次充值：¥10,000.00</li>
                                </ul>
                            </div>

                            <div class="mb-4">
                                <h6>当前余额</h6>
                                <div class="h3 text-primary">¥{{ number_format(auth('web')->user()->balance, 2) }}</div>
                            </div>

                            <form method="POST" action="{{ route('user.recharge') }}" id="rechargeForm">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="amount" class="form-label">充值金额</label>
                                    <div class="input-group">
                                        <span class="input-group-text">¥</span>
                                        <input type="number" 
                                               class="form-control @error('amount') is-invalid @enderror" 
                                               id="amount" 
                                               name="amount" 
                                               value="{{ old('amount') }}"
                                               min="1" 
                                               max="10000"
                                               step="0.01"
                                               required>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- 快捷金额选择 -->
                                <div class="mb-3">
                                    <label class="form-label">快捷选择</label>
                                    <div class="d-flex gap-2 flex-wrap">
                                        @foreach([10, 50, 100, 200, 500, 1000] as $quickAmount)
                                            <button type="button" 
                                                    class="btn btn-outline-primary btn-sm quick-amount" 
                                                    data-amount="{{ $quickAmount }}">
                                                ¥{{ $quickAmount }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="pay_id" class="form-label">支付方式</label>
                                    <select class="form-select @error('pay_id') is-invalid @enderror" 
                                            id="pay_id" 
                                            name="pay_id" 
                                            required>
                                        <option value="">请选择支付方式</option>
                                        @php
                                            $payMethods = \App\Models\Pay::where('status', 1)->get();
                                        @endphp
                                        @foreach($payMethods as $pay)
                                            <option value="{{ $pay->id }}" {{ old('pay_id') == $pay->id ? 'selected' : '' }}>
                                                {{ $pay->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('pay_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="alert alert-warning">
                                        <h6>充值后金额</h6>
                                        <div class="h5 mb-0">
                                            ¥{{ number_format(auth('web')->user()->balance, 2) }} + 
                                            <span id="addAmount">¥0.00</span> = 
                                            <span id="totalAmount" class="text-success">¥{{ number_format(auth('web')->user()->balance, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="ci-credit-card me-2"></i>立即充值
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-6">
                            <div class="text-center">
                                <img src="/assets/common/images/recharge-illustration.svg" 
                                     alt="充值插图" 
                                     class="img-fluid" 
                                     style="max-height: 300px;"
                                     onerror="this.style.display='none'">
                                
                                <div class="mt-4">
                                    <h6>为什么选择余额充值？</h6>
                                    <div class="row g-3 mt-2">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center">
                                                <i class="ci-clock text-success me-3 fs-4"></i>
                                                <div class="text-start">
                                                    <h6 class="mb-1">快速结算</h6>
                                                    <small class="text-muted">使用余额购买无需跳转支付页面</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-center">
                                                <i class="ci-shield-check text-success me-3 fs-4"></i>
                                                <div class="text-start">
                                                    <h6 class="mb-1">安全可靠</h6>
                                                    <small class="text-muted">余额资金安全保障，支持退款</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex align-items-center">
                                                <i class="ci-star text-success me-3 fs-4"></i>
                                                <div class="text-start">
                                                    <h6 class="mb-1">等级优惠</h6>
                                                    <small class="text-muted">VIP用户享受额外折扣优惠</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
</main>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    const addAmountSpan = document.getElementById('addAmount');
    const totalAmountSpan = document.getElementById('totalAmount');
    const currentBalance = {{ auth('web')->user()->balance }};
    const quickAmountButtons = document.querySelectorAll('.quick-amount');

    // 快捷金额选择
    quickAmountButtons.forEach(button => {
        button.addEventListener('click', function() {
            const amount = this.getAttribute('data-amount');
            amountInput.value = amount;
            updateAmount();
        });
    });

    // 监听金额输入
    amountInput.addEventListener('input', updateAmount);

    function updateAmount() {
        const amount = parseFloat(amountInput.value) || 0;
        const total = currentBalance + amount;
        
        addAmountSpan.textContent = '¥' + amount.toFixed(2);
        totalAmountSpan.textContent = '¥' + total.toFixed(2);
    }

    // 表单验证
    document.getElementById('rechargeForm').addEventListener('submit', function(e) {
        const amount = parseFloat(amountInput.value) || 0;
        const payId = document.getElementById('pay_id').value;

        if (amount < 1) {
            e.preventDefault();
            alert('充值金额不能少于1元');
            return;
        }

        if (amount > 10000) {
            e.preventDefault();
            alert('单次充值金额不能超过10,000元');
            return;
        }

        if (!payId) {
            e.preventDefault();
            alert('请选择支付方式');
            return;
        }

        // 确认充值
        if (!confirm(`确认充值 ¥${amount.toFixed(2)} 吗？`)) {
            e.preventDefault();
        }
    });
});
</script>
@endsection