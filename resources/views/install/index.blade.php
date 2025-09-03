<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>安装独角数卡</title>
    <style>
      html, body {
        touch-action: manipulation;
      }
    </style>
    @if(\request()->isSecure())
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endif
    
    <link rel="stylesheet" href="{{ asset('assets/morpho/icons/cartzilla-icons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/morpho/css/swiper-bundle.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/morpho/css/simplebar.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/morpho/css/theme.min.css') }}" />
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="text-center mb-5">
                    <h1 class="h2 mb-3">独角数卡</h1>
                    <p class="text-muted">安装向导</p>
                </div>

                <!-- 进度卡片 -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="progress mb-3" style="height: 6px;">
                            <div class="progress-bar" id="install-progress" style="width: 33%"></div>
                        </div>
                        <div class="row text-center">
                            <div class="col-3">
                                <small class="text-primary fw-bold">
                                    <i class="ci-file-text me-1"></i>使用协议
                                </small>
                            </div>
                            <div class="col-3">
                                <small class="text-muted" id="step2-text">
                                    <i class="ci-check-circle me-1"></i>环境检查
                                </small>
                            </div>
                            <div class="col-3">
                                <small class="text-muted" id="step3-text">
                                    <i class="ci-settings me-1"></i>配置信息
                                </small>
                            </div>
                            <div class="col-3">
                                <small class="text-muted" id="step4-text">
                                    <i class="ci-check me-1"></i>安装完成
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 内容卡片 -->
                <div class="card shadow-sm">
                    <div class="card-body">
                            <!-- 步骤1: 使用协议 -->
                            <div id="step-1" class="step-content">
                                <h4 class="mb-4">软件使用许可及风险告知协议</h4>
                                
                                <div class="border rounded p-3 mb-4" style="height: 400px; overflow-y: auto; background-color: #f8f9fa;">
                                    @include('install.license')
                                </div>
                                
                                <div class="form-check mb-4">
                                    <input class="form-check-input" type="checkbox" id="agree-license" required>
                                    <label class="form-check-label" for="agree-license">
                                        我已仔细阅读并同意以上使用许可协议
                                    </label>
                                </div>

                                <div class="text-end">
                                    <button class="btn btn-primary" id="agree-btn" onclick="nextStep()" disabled>
                                        同意并继续 <i class="ci-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- 步骤2: 环境检查 -->
                            <div id="step-2" class="step-content d-none">
                                <h4 class="mb-4">系统环境检查</h4>
                                
                                <div class="list-group list-group-flush mb-4">
                                    @php
                                        $hasError = false;
                                        foreach($environmentChecks as $check) {
                                            if (!isset($check['required']) || $check['required'] !== false) {
                                                if (!$check['status']) {
                                                    $hasError = true;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    
                                    @foreach($environmentChecks as $key => $check)
                                    <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                        <span>{{ $check['label'] }}</span>
                                        <div>
                                            @if(isset($check['required']) && !$check['required'])
                                                <span class="badge bg-{{ $check['status'] ? 'success' : 'warning' }}">
                                                    {{ $check['message'] }}
                                                </span>
                                            @else
                                                <span class="badge bg-{{ $check['status'] ? 'success' : 'danger' }}">
                                                    {{ $check['message'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                @if($hasError)
                                <div class="alert alert-danger mb-3">
                                    <i class="ci-alert-circle me-2"></i>
                                    <strong>错误：</strong> 存在必需的环境要求未满足，请先解决上述红色标记的问题后再继续安装。
                                </div>
                                @endif

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-primary" onclick="prevStep()">
                                        <i class="ci-arrow-left me-1"></i> 上一步
                                    </button>
                                    <button class="btn btn-primary" onclick="nextStep()" {{ $hasError ? 'disabled' : '' }}>
                                        下一步 <i class="ci-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- 步骤3: 配置信息 -->
                            <div id="step-3" class="step-content d-none">
                                <h4 class="mb-4">配置信息</h4>
                                
                                <form id="install-form">
                                    @csrf
                                    
                                    <!-- 数据库配置 -->
                                    <div class="mb-4">
                                        <h6 class="mb-3">数据库配置</h6>
                                        <div class="row">
                                            <div class="col-sm-6 mb-3">
                                                <label class="form-label">数据库地址</label>
                                                <input type="text" name="db_host" value="127.0.0.1" class="form-control" required>
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <label class="form-label">数据库端口</label>
                                                <input type="number" name="db_port" value="3306" class="form-control" required>
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <label class="form-label">数据库名</label>
                                                <input type="text" name="db_database" value="dujiaoka" class="form-control" required>
                                            </div>
                                            <div class="col-sm-6 mb-3">
                                                <label class="form-label">用户名</label>
                                                <input type="text" name="db_username" value="root" class="form-control" required>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">密码</label>
                                                <input type="password" name="db_password" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Redis配置 -->
                                    <div class="mb-4">
                                        <h6 class="mb-3">Redis配置</h6>
                                        <div class="row">
                                            <div class="col-sm-4 mb-3">
                                                <label class="form-label">Redis地址</label>
                                                <input type="text" name="redis_host" value="127.0.0.1" class="form-control" required>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label class="form-label">Redis端口</label>
                                                <input type="number" name="redis_port" value="6379" class="form-control" required>
                                            </div>
                                            <div class="col-sm-4 mb-3">
                                                <label class="form-label">Redis密码</label>
                                                <input type="password" name="redis_password" class="form-control" placeholder="默认为空">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 网站配置 -->
                                    <div class="mb-4">
                                        <h6 class="mb-3">网站配置</h6>
                                        <div class="mb-3">
                                            <label class="form-label">网站名称</label>
                                            <input type="text" name="title" value="独角数卡" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">网站URL</label>
                                            <input type="url" name="app_url" id="app_url" class="form-control" required placeholder="https://your-domain.com">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">后台路径</label>
                                            <input type="text" name="admin_path" value="/admin" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-primary" onclick="prevStep()">
                                            <i class="ci-arrow-left me-1"></i> 上一步
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            开始安装 <i class="ci-check ms-1"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- 步骤4: 安装完成 -->
                            <div id="step-4" class="step-content d-none">
                                <div class="text-center">
                                    <i class="ci-check-circle text-success mb-4" style="font-size: 4rem;"></i>
                                    <h4 class="text-success mb-3">安装成功！</h4>
                                    <p class="text-muted mb-4">
                                        独角数卡已成功安装，默认管理员账号密码均为：<strong>admin</strong><br>
                                        请及时登录修改密码！
                                    </p>
                                    
                                    <div class="d-flex justify-content-center gap-3">
                                        <a href="/" class="btn btn-primary">
                                            <i class="ci-home me-1"></i> 访问首页
                                        </a>
                                        <a href="/admin" class="btn btn-success">
                                            <i class="ci-settings me-1"></i> 访问后台
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Loading状态 -->
                            <div id="loading" class="d-none text-center py-5">
                                <div class="spinner-border text-primary mb-3" role="status"></div>
                                <p class="text-muted">正在安装中，请稍候...</p>
                            </div>
                            
                            <!-- 错误提示 -->
                            <div id="error-alert" class="alert alert-danger mt-3 d-none" role="alert">
                                <strong>错误：</strong>
                                <span id="error-text"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('assets/morpho/js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('assets/morpho/js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        let currentStep = 1;

        // 自动填充网站URL和协议滚动检测
        document.addEventListener('DOMContentLoaded', function() {
            const appUrlInput = document.getElementById('app_url');
            if (appUrlInput && !appUrlInput.value) {
                const currentUrl = window.location.origin;
                appUrlInput.value = currentUrl;
            }
            
            // 监听协议滚动和复选框
            const licenseBox = document.querySelector('.step-content .border');
            const agreeCheckbox = document.getElementById('agree-license');
            const agreeBtn = document.getElementById('agree-btn');
            let scrolledToBottom = false;
            
            if (licenseBox) {
                licenseBox.addEventListener('scroll', function() {
                    if (licenseBox.scrollTop + licenseBox.clientHeight >= licenseBox.scrollHeight - 5) {
                        scrolledToBottom = true;
                        updateAgreeButton();
                    }
                });
            }
            
            if (agreeCheckbox) {
                agreeCheckbox.addEventListener('change', updateAgreeButton);
            }
            
            function updateAgreeButton() {
                if (agreeBtn) {
                    agreeBtn.disabled = !(scrolledToBottom && agreeCheckbox?.checked);
                }
            }
        });

        function updateProgress() {
            const progress = currentStep * 25;
            $('#install-progress').css('width', progress + '%');
            
            // 更新步骤文本
            $('small').removeClass('text-primary fw-bold').addClass('text-muted');
            if (currentStep === 1) $('small:first').removeClass('text-muted').addClass('text-primary fw-bold');
            if (currentStep === 2) $('#step2-text').removeClass('text-muted').addClass('text-primary fw-bold');
            if (currentStep === 3) $('#step3-text').removeClass('text-muted').addClass('text-primary fw-bold');
            if (currentStep === 4) $('#step4-text').removeClass('text-muted').addClass('text-primary fw-bold');
        }

        function showStep(step) {
            $('.step-content').addClass('d-none');
            $('#step-' + step).removeClass('d-none');
            currentStep = step;
            updateProgress();
        }

        function nextStep() {
            if (currentStep < 4) {
                showStep(currentStep + 1);
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                showStep(currentStep - 1);
            }
        }

        function showError(message) {
            $('#error-text').text(message);
            $('#error-alert').removeClass('d-none');
            setTimeout(() => $('#error-alert').addClass('d-none'), 10000);
        }

        $('#install-form').on('submit', function(e) {
            e.preventDefault();
            
            $('#step-3').addClass('d-none');
            $('#loading').removeClass('d-none');
            
            $.ajax({
                url: '{{ route("install.do") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(result) {
                    $('#loading').addClass('d-none');
                    if (result.success) {
                        showStep(4);
                    } else {
                        $('#step-3').removeClass('d-none');
                        showError(result.error || '安装失败');
                    }
                },
                error: function(xhr) {
                    $('#loading').addClass('d-none');
                    $('#step-3').removeClass('d-none');
                    showError(xhr.responseJSON?.error || '网络错误，请重试');
                }
            });
        });
    </script>
</body>
</html>