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
                        <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim($user->email))) }}?s=80&d=identicon" 
                             alt="{{ $user->nickname ?: $user->email }}"
                             class="rounded-circle mx-auto mb-2 d-block" 
                             style="width: 80px; height: 80px;">
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
                        <a href="{{ route('user.level') }}" class="nav-link">
                            <i class="ci-star me-2"></i>等级信息
                        </a>
                        <a href="{{ route('user.profile') }}" class="nav-link active">
                            <i class="ci-user me-2"></i>个人资料
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- 主内容 -->
        <div class="col-md-9">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ci-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach($errors->all() as $error)
                        <div><i class="ci-alert-circle me-2"></i>{{ $error }}</div>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- 个人资料 -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ci-user me-2"></i>个人资料
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.profile') }}" id="profileForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">邮箱地址</label>
                                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nickname" class="form-label">昵称</label>
                                <input type="text" 
                                       class="form-control @error('nickname') is-invalid @enderror" 
                                       name="nickname" 
                                       value="{{ old('nickname', $user->nickname) }}"
                                       maxlength="50"
                                       placeholder="留空将使用邮箱前缀">
                                @error('nickname')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">手机号码</label>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" 
                                       value="{{ old('phone', $user->phone) }}"
                                       maxlength="20"
                                       placeholder="选填">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">注册时间</label>
                                <input type="text" class="form-control" value="{{ $user->created_at->format('Y-m-d H:i') }}" disabled>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="ci-check me-2"></i>保存资料
                        </button>
                    </form>
                </div>
            </div>

            <!-- 修改密码 -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ci-lock me-2"></i>修改密码
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.change-password') }}" id="passwordForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="current_password" class="form-label">当前密码</label>
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       name="current_password" 
                                       required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="password" class="form-label">新密码</label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="password_confirmation" class="form-label">确认新密码</label>
                                <input type="password" 
                                       class="form-control" 
                                       name="password_confirmation" 
                                       required>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <small>
                                <i class="ci-info-circle me-2"></i>
                                密码至少8位，包含字母和数字。修改密码后将自动退出当前登录状态。
                            </small>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="ci-lock me-2"></i>修改密码
                        </button>
                    </form>
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
    // 个人资料表单验证
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        const nickname = this.nickname.value.trim();
        const phone = this.phone.value.trim();

        if (nickname && nickname.length > 50) {
            e.preventDefault();
            alert('昵称不能超过50个字符');
            return;
        }

        if (phone && !/^[\d\s\-\+\(\)]+$/.test(phone)) {
            e.preventDefault();
            alert('请输入有效的手机号码');
            return;
        }
    });

    // 修改密码表单验证
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        const currentPassword = this.current_password.value;
        const newPassword = this.password.value;
        const confirmPassword = this.password_confirmation.value;

        if (newPassword.length < 8) {
            e.preventDefault();
            alert('新密码至少需要8位');
            return;
        }

        if (newPassword !== confirmPassword) {
            e.preventDefault();
            alert('两次输入的密码不一致');
            return;
        }

        if (currentPassword === newPassword) {
            e.preventDefault();
            alert('新密码不能与当前密码相同');
            return;
        }

        if (!confirm('确认修改密码吗？修改后将自动退出当前登录状态。')) {
            e.preventDefault();
        }
    });

    // 自动关闭提示框
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endsection