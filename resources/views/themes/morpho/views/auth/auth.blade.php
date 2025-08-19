@extends('morpho::layouts.default')

@section('content')
<div class="auth-container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-sm-8 col-md-6 col-lg-4 col-xl-3">
                <div class="auth-card">
                    <div class="auth-header">
                        <div class="auth-logo mb-4">
                            <h1 class="brand-name">{{ config('app.name', 'Dujiaoka') }}</h1>
                            <p class="brand-tagline">欢迎来到我们的平台</p>
                        </div>
                        
                        <!-- Tab 切换按钮 -->
                        <div class="auth-tabs">
                            <button class="auth-tab active" data-tab="login">
                                <i class="ci-log-in"></i>
                                登录
                            </button>
                            <button class="auth-tab" data-tab="register">
                                <i class="ci-user-plus"></i>
                                注册
                            </button>
                        </div>
                    </div>

                    <div class="auth-body">
                        <!-- 错误提示 -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                @foreach ($errors->all() as $error)
                                    <div><i class="ci-alert-circle me-2"></i>{{ $error }}</div>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="ci-check-circle me-2"></i>{{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- 登录表单 -->
                        <div class="auth-form active" id="login-form">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-icon">
                                            <i class="ci-mail"></i>
                                        </span>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               name="email" 
                                               value="{{ old('email') }}" 
                                               placeholder="请输入邮箱地址"
                                               required 
                                               autofocus>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-icon">
                                            <i class="ci-lock"></i>
                                        </span>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               name="password" 
                                               placeholder="请输入密码"
                                               required>
                                        <span class="password-toggle" onclick="togglePassword('login')">
                                            <i class="ci-eye"></i>
                                        </span>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-options">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">记住我</label>
                                    </div>
                                    <a href="{{ route('password.request') }}" class="forgot-password">忘记密码？</a>
                                </div>

                                <button type="submit" class="btn-auth">
                                    <span class="btn-text">登录账户</span>
                                    <i class="ci-arrow-right"></i>
                                </button>
                            </form>
                        </div>

                        <!-- 注册表单 -->
                        <div class="auth-form" id="register-form">
                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                
                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-icon">
                                            <i class="ci-mail"></i>
                                        </span>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               name="email" 
                                               value="{{ old('email') }}" 
                                               placeholder="请输入邮箱地址"
                                               required>
                                    </div>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-icon">
                                            <i class="ci-user"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control @error('nickname') is-invalid @enderror" 
                                               name="nickname" 
                                               value="{{ old('nickname') }}" 
                                               placeholder="昵称（可选）"
                                               maxlength="50">
                                    </div>
                                    @error('nickname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-hint">留空将使用邮箱前缀作为昵称</div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-icon">
                                            <i class="ci-lock"></i>
                                        </span>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               name="password" 
                                               placeholder="请输入密码"
                                               id="register-password"
                                               required>
                                        <span class="password-toggle" onclick="togglePassword('register')">
                                            <i class="ci-eye"></i>
                                        </span>
                                    </div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="password-strength">
                                        <div class="strength-bar">
                                            <div class="strength-fill"></div>
                                        </div>
                                        <span class="strength-text">密码至少8位，包含字母和数字</span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="input-group">
                                        <span class="input-icon">
                                            <i class="ci-lock"></i>
                                        </span>
                                        <input type="password" 
                                               class="form-control" 
                                               name="password_confirmation" 
                                               placeholder="确认密码"
                                               id="password-confirmation"
                                               required>
                                        <span class="password-toggle" onclick="togglePassword('confirm')">
                                            <i class="ci-eye"></i>
                                        </span>
                                    </div>
                                    <div class="password-match"></div>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               class="form-check-input @error('agree_terms') is-invalid @enderror" 
                                               id="agree_terms" 
                                               name="agree_terms" 
                                               value="1" 
                                               required>
                                        <label class="form-check-label" for="agree_terms">
                                            我已阅读并同意 <a href="#" class="terms-link">用户协议</a> 和 <a href="#" class="terms-link">隐私政策</a>
                                        </label>
                                        @error('agree_terms')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <button type="submit" class="btn-auth">
                                    <span class="btn-text">创建账户</span>
                                    <i class="ci-arrow-right"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<link rel="stylesheet" href="{{ asset('assets/morpho/css/auth.css') }}">
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab 切换功能
    const tabs = document.querySelectorAll('.auth-tab');
    const forms = document.querySelectorAll('.auth-form');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // 更新 tab 状态
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // 更新表单显示
            forms.forEach(form => {
                form.classList.remove('active');
                if (form.id === targetTab + '-form') {
                    form.classList.add('active');
                }
            });
        });
    });
    
    // 密码显示/隐藏切换
    window.togglePassword = function(type) {
        let input, icon;
        
        if (type === 'login') {
            input = document.querySelector('#login-form input[name="password"]');
            icon = document.querySelector('#login-form .password-toggle i');
        } else if (type === 'register') {
            input = document.querySelector('#register-password');
            icon = document.querySelector('#register-form .password-toggle i');
        } else if (type === 'confirm') {
            input = document.querySelector('#password-confirmation');
            icon = document.querySelector('#password-confirmation').nextElementSibling.querySelector('i');
        }
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('ci-eye');
            icon.classList.add('ci-eye-off');
        } else {
            input.type = 'password';
            icon.classList.remove('ci-eye-off');
            icon.classList.add('ci-eye');
        }
    };
    
    // 密码强度检查
    const passwordInput = document.getElementById('register-password');
    const confirmInput = document.getElementById('password-confirmation');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = checkPasswordStrength(password);
            updatePasswordStrength(strength);
        });
    }
    
    if (confirmInput) {
        confirmInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirm = this.value;
            const matchDiv = this.parentNode.nextElementSibling;
            
            if (password && confirm) {
                if (password === confirm) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    matchDiv.innerHTML = '<i class="ci-check text-success"></i> 密码匹配';
                    matchDiv.className = 'password-match text-success';
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                    matchDiv.innerHTML = '<i class="ci-close text-danger"></i> 密码不匹配';
                    matchDiv.className = 'password-match text-danger';
                }
            } else {
                this.classList.remove('is-valid', 'is-invalid');
                matchDiv.innerHTML = '';
                matchDiv.className = 'password-match';
            }
        });
    }
    
    function checkPasswordStrength(password) {
        let score = 0;
        if (password.length >= 8) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        return Math.min(score, 4);
    }
    
    function updatePasswordStrength(strength) {
        const strengthFill = document.querySelector('.strength-fill');
        const strengthText = document.querySelector('.strength-text');
        
        if (!strengthFill || !strengthText) return;
        
        const colors = ['#ff6b6b', '#ffa726', '#ffcc02', '#66bb6a'];
        const texts = ['弱', '一般', '良好', '强'];
        
        if (strength === 0) {
            strengthFill.style.width = '0%';
            strengthText.textContent = '密码至少8位，包含字母和数字';
            strengthText.style.color = '#6c757d';
        } else {
            strengthFill.style.width = (strength * 25) + '%';
            strengthFill.style.backgroundColor = colors[strength - 1];
            strengthText.textContent = '密码强度：' + texts[strength - 1];
            strengthText.style.color = colors[strength - 1];
        }
    }
    
    // 自动关闭提示框
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
    
    // 根据URL参数或错误自动选择正确的 tab
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    
    if (tabParam === 'register') {
        document.querySelector('[data-tab="register"]').click();
    } else if (tabParam === 'login') {
        document.querySelector('[data-tab="login"]').click();
    } else {
        @if($errors->any())
            // 如果有注册相关错误，切换到注册 tab
            const hasRegisterErrors = @json($errors->has('nickname') || $errors->has('password_confirmation') || $errors->has('agree_terms'));
            if (hasRegisterErrors) {
                document.querySelector('[data-tab="register"]').click();
            }
        @endif
    }
});
</script>
@endsection