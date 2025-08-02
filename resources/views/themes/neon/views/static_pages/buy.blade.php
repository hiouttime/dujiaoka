@extends('riniba_03.layouts.seo')
@section('content')
<div class="content">
    <div class="container">
      <main class="content-wrapper">
        
        <nav class="container pt-2 pt-xxl-3 my-3 my-md-4" aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">主页</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/') }}">购物</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $gd_name }}</li>
          </ol>
        </nav>
  
        <!-- 商品主内容区域 -->
        <section class="container">
          <div class="row">
            <!-- 左侧：商品图片 / Gallery -->
            <div class="col-md-6 pb-4 pb-md-0 mb-2 mb-sm-3 mb-md-0">
              <div class="position-relative">
                <a class="hover-effect-scale hover-effect-opacity position-relative d-flex rounded overflow-hidden mb-3 mb-sm-4 mb-md-3 mb-lg-4"
                   href="{{ picture_url($picture) }}" data-glightbox="" data-gallery="product-gallery">
                  <i class="ci-zoom-in hover-effect-target fs-3 text-white position-absolute top-50 start-50 translate-middle opacity-0 z-2"></i>
                  <div class="ratio hover-effect-target bg-body-tertiary rounded"
                       style="--cz-aspect-ratio: calc(706 / 636 * 100%)">
                    <img src="{{ picture_url($picture) }}" alt="{{ $gd_name }}">
                  </div>
                </a>
              </div>
            </div>
  
            <!-- 右侧：商品详情 & 购买表单 -->
            <div class="col-md-6">
              <div class="ps-md-4 ps-xl-5">
                <!-- 购买表单 -->
                <form class="needs-validation" novalidate id="buy-form" action="{{ url('create-order') }}" method="post">
                  @csrf
  
                  <!-- 商品名称 -->
                  <h1 class="h3 mb-3">{{ $gd_name }}</h1>
  
                  <!-- 关键信息 (如：自动/人工、库存、批发价提示等) -->
                  <div class="d-flex flex-wrap gap-3 gap-xxl-4 fs-sm text-dark-emphasis mb-2">
  
                    <!-- 判断自动/人工发货 -->
                    <div class="d-flex align-items-center me-3">
                      <i class="ci-delivery fs-xl text-body-emphasis me-2"></i>
                      @if($type == \App\Models\Goods::AUTOMATIC_DELIVERY)
                        自动发货
                      @else
                        人工发货
                      @endif
                    </div>
  
                    <!-- 库存 -->
                    <div class="d-flex align-items-center me-3">
                      <i class="ci-broccoli fs-xl text-body-emphasis me-2"></i>
                      库存：{{ $in_stock }}
                    </div>
  
                    <!-- 若有批发价配置 -->
                    @if(!empty($wholesale_price_cnf) && is_array($wholesale_price_cnf))
                      <div class="d-flex align-items-center me-3">
                        <i class="ci-leaf fs-xl text-body-emphasis me-2"></i>
                        有批发价
                      </div>
                    @endif
                  </div>
  
                  <!-- 价格显示 (如果有原价，也可以加个 <del> 原价 ) -->
                  <div class="h4 d-flex align-items-center my-3">
                    {{ $actual_price }} {{ __('dujiaoka.money_symbol') }}
                    {{-- 如需显示原价:
                    @if(isset($original_price) && $original_price > $actual_price)
                      <del class="fs-sm fw-normal text-body-tertiary ms-2">
                        {{ $original_price }} {{ __('dujiaoka.money_symbol') }}
                      </del>
                    @endif
                    --}}
                  </div>
  
                  <!-- 批发 / 优惠价列表 (可自行排版) -->
                  @if(!empty($wholesale_price_cnf) && is_array($wholesale_price_cnf))
                    <div class="mb-3">
                      @foreach($wholesale_price_cnf as $ws)
                        <span class="badge bg-dark mt-1 mb-1">
                          <i class="ali-icon">&#xe77d;</i>
                          {{ __('dujiaoka.by_amount') }}{{ $ws['number'] }}{{ __('dujiaoka.or_the_above') }}，{{ __('dujiaoka.each') }}：{{ $ws['price']  }}{{ __('dujiaoka.money_symbol') }}
                        </span>
                      @endforeach
                    </div>
                  @endif
  
                  <!-- 如果有限购 -->
                  @if($buy_limit_num > 0)
                    <h6 class="mb-3">
                      <small class="badge bg-danger">
                        {{ __('dujiaoka.purchase_limit') }} ({{ $buy_limit_num }})
                      </small>
                    </h6>
                  @endif
  
                  <!-- [1] 电子邮箱 -->
                  <div class="mb-3">
                    <label for="email" class="form-label">
                      {{ __('dujiaoka.email') }}
                      <span class="text-danger">*</span>
                    </label>
                    <!-- 传递商品ID隐藏域 -->
                    <input type="hidden" name="gid" value="{{ $id }}">
                    <input type="email" class="form-control" name="email" id="email"
                           required placeholder="查询订单或发送卡密会用到">
                    <div class="invalid-feedback">请输入您的电子邮箱!</div>
                  </div>
  
                  <!-- [2] 查询密码 (如果开启) -->
                  @if(dujiaoka_config_get('is_open_search_pwd') == \App\Models\Goods::STATUS_OPEN)
                    <div class="mb-3">
                      <label for="search_pwd" class="form-label">
                        {{ __('dujiaoka.search_password') }}
                        <span class="text-danger">*</span>
                      </label>
                      <input type="text" class="form-control" id="search_pwd" name="search_pwd"
                             required placeholder="查询订单时会用到">
                      <div class="invalid-feedback">请输入您的查询密码!</div>
                    </div>
                  @endif
  
                  <!-- 如果是人工发货并且有额外表单字段 -->
                  @if($type == \App\Models\Goods::MANUAL_PROCESSING && is_array($other_ipu))
                    @foreach($other_ipu as $ipu)
                      <div class="mb-3">
                        <label for="{{ $ipu['field'] }}" class="form-label">
                          {{ $ipu['desc'] }}:
                          @if($ipu['rule'] !== false)
                            <span class="text-danger">*</span>
                          @endif
                        </label>
                        <input type="text"
                               class="form-control"
                               id="{{ $ipu['field'] }}"
                               name="{{ $ipu['field'] }}"
                               @if($ipu['rule'] !== false) required @endif
                               placeholder="{{ $ipu['desc'] }}">
                        @if($ipu['rule'] !== false)
                          <div class="invalid-feedback">请填写 {{ $ipu['desc'] }}！</div>
                        @endif
                      </div>
                    @endforeach
                  @endif
  
                  <!-- [3] 优惠券 (如果需要) -->
                  @if(isset($open_coupon))
                    <div class="mb-3">
                      <label for="coupon" class="form-label">
                        {{ __('dujiaoka.coupon_code') }}:
                      </label>
                      <input type="text" class="form-control" id="coupon" name="coupon_code" placeholder="">
                    </div>
                  @endif
  
                  <!-- [4] 图片验证码 (如果开启) -->
                  @if(dujiaoka_config_get('is_open_img_code') == \App\Models\Goods::STATUS_OPEN)
                    <div class="mb-3">
                      <label for="verifyCode" class="form-label">
                        {{ __('dujiaoka.img_verify_code') }}
                      </label>
                      <div class="input-group">
                        <input type="text" name="img_verify_code" class="form-control" id="verifyCode" required>
                        <img style="margin-left: 10px;"
                             src="{{ captcha_src('buy') . time() }}" height="33px"
                             alt="{{ __('dujiaoka.img_verify_code') }}"
                             onclick="refreshCaptcha()" id="imageCode">
                      </div>
                    </div>
                    <script>
                      function refreshCaptcha() {
                        var img = document.getElementById('imageCode');
                        img.src = '{{ captcha_src('buy') }}' + Math.random();
                      }
                    </script>
                  @endif
  
                  <!-- [5] 支付方式 (payways) -->
                  <div class="mb-4">
                    <label class="form-label fw-semibold pb-1 mb-2">
                      {{ __('dujiaoka.payment_method') }}
                      <span class="text-danger">*</span>:
                    </label>
                    <div class="d-flex flex-wrap gap-2" id="paymentGroup">
                      @foreach($payways as $index => $way)
                        <label class="payments" data-type="{{ $way['pay_check'] }}" data-id="{{ $way['id'] }}">
                          <input type="radio" class="btn-check"
                                 name="payway" value="{{ $way['id'] }}"
                                 id="payway-{{ $way['id'] }}"
                                 @if($index == 0) checked @endif>
                          <span class="btn btn-image p-0 paymentsvg">
                            {{ $way['pay_name'] }}
                          </span>
                          <span>{{ $way['pay_name'] }}</span>
                        </label>
                      @endforeach
                    </div>
                  </div>
  
                  <!-- [6] 购买数量 -->
                  <div class="d-flex gap-3 pb-3 pb-lg-4 mb-3">
                    <div class="count-input flex-shrink-0 w-50 d-flex justify-content-center align-items-center">
                      <button type="button" class="btn btn-icon btn-lg" data-decrement aria-label="Decrement quantity">
                        <i class="ci-minus"></i>
                      </button>
                      <input type="number" class="form-control form-control-xl w-50" name="by_amount" min="1"
                             value="1">
                      <button type="button" class="btn btn-icon btn-lg" data-increment aria-label="Increment quantity">
                        <i class="ci-plus"></i>
                      </button>
                    </div>
                    <!-- 如果有 aff 之类的可以隐藏 -->
                    <input type="hidden" name="aff" value="">
  
                    <button type="submit" id="submit" class="btn btn-lg btn-dark w-100">
                      {{ __('dujiaoka.order_now') }}
                    </button>
                  </div>
  
                  <!-- 一些额外提示 -->
                  <ul class="list-unstyled gap-3 pb-3 pb-lg-4 mb-3 fs-sm">
                    <li class="d-flex flex-wrap">
                      <span class="d-flex align-items-center fw-medium text-dark-emphasis me-2">
                        <i class="ci-clock fs-base me-2"></i>
                        推荐使用加密货币支付:
                      </span>
                      注意正确的付款金额
                    </li>
                    @if($type == \App\Models\Goods::MANUAL_PROCESSING)
                    <li class="d-flex flex-wrap">
                      <span class="d-flex align-items-center fw-medium text-dark-emphasis me-2">
                        <i class="ci-delivery fs-base me-2"></i>
                        手动发货商品:
                      </span>
                      请在规定的作息时间联系客服
                    </li>
                    @endif
                  </ul>
                </form>
              </div>
            </div>
          </div>
        </section>
  
        <!-- 吸顶的购买栏 (可选) -->
        <section class="sticky-product-banner sticky-top" data-sticky-element>
          <div class="sticky-product-banner-inner pt-5">
            <div class="navbar container flex-nowrap align-items-center bg-body pt-4 pt-lg-5 mt-lg-n2">
              <div class="d-flex align-items-center min-w-0 ms-lg-2 me-3">
                <div class="ratio ratio-1x1 flex-shrink-0" style="width: 50px;">
                  <img src="{{ picture_url($picture) }}" alt="{{ $gd_name }}">
                </div>
                <h4 class="h6 fw-medium d-none d-lg-block ps-3 mb-0">{{ $gd_name }}</h4>
                <div class="w-100 min-w-0 d-lg-none ps-2">
                  <h4 class="fs-sm fw-medium text-truncate mb-1">{{ $gd_name }}</h4>
                  <div class="h6 mb-0">
                    {{ $actual_price }} {{ __('dujiaoka.money_symbol') }}
                    {{-- 原价可选
                    @if(isset($original_price) && $original_price > $actual_price)
                      <del class="fs-xs fw-normal text-body-tertiary">
                        {{ $original_price }} {{ __('dujiaoka.money_symbol') }}
                      </del>
                    @endif
                    --}}
                  </div>
                </div>
              </div>
              <div class="h4 d-none d-lg-block mb-0 ms-auto me-4">
                {{ $actual_price }} {{ __('dujiaoka.money_symbol') }}
                {{-- 原价可选
                @if(isset($original_price) && $original_price > $actual_price)
                  <del class="fs-sm fw-normal text-body-tertiary">
                    {{ $original_price }} {{ __('dujiaoka.money_symbol') }}
                  </del>
                @endif
                --}}
              </div>
              <div class="d-flex gap-2">
                <!-- 点击提交同一个表单 #buy-form -->
                <button type="submit" form="buy-form" id="submit" class="btn btn-dark animate-pulse d-md-none">
                  <i class="ci-shopping-cart fs-base animate-target me-2"></i> {{ __('dujiaoka.order_now') }}
                </button>
                <button type="submit" form="buy-form" id="submit" class="btn btn-dark ms-auto d-none d-md-inline-flex px-4">
                  立即购买
                </button>
              </div>
            </div>
          </div>
        </section>
  
        <!-- 商品描述/提示/教程等Tab (可选) -->
        <section class="container pt-5 mt-2 mt-sm-3 mt-lg-4 mt-xl-5" >
          <ul class="nav nav-underline flex-nowrap border-bottom" role="tablist">
            <li class="nav-item me-md-1" role="presentation">
              <button type="button" class="nav-link active" id="description-tab" data-bs-toggle="tab"
                      data-bs-target="#description-tab-pane" role="tab"
                      aria-controls="description-tab-pane" aria-selected="true">
                {{ __('goods.fields.description') }}
              </button>
            </li>
            <li class="nav-item me-md-1" role="presentation">
              <button type="button" class="nav-link" id="washing-tab" data-bs-toggle="tab"
                      data-bs-target="#washing-tab-pane" role="tab"
                      aria-controls="washing-tab-pane" aria-selected="false">
                商品提示
              </button>
            </li>
            <li class="nav-item me-md-1" role="presentation">
              <button type="button" class="nav-link" id="delivery-tab" data-bs-toggle="tab"
                      data-bs-target="#delivery-tab-pane" role="tab"
                      aria-controls="delivery-tab-pane" aria-selected="false">
                相关教程
              </button>
            </li>
          </ul>
  
          <div class="tab-content pt-4 mt-sm-1 mt-md-3">
            <!-- Description tab -->
            <div class="tab-pane fade active show" id="description-tab-pane" role="tabpanel" aria-labelledby="description-tab">
              <div class="row">
                <!-- 直接输出商品描述 -->
                {!! $description !!}
              </div>
            </div>
  
            <!-- Washing instructions tab -->
            <div class="tab-pane fade fs-sm" id="washing-tab-pane" role="tabpanel" aria-labelledby="washing-tab">
              <p>这里放一些商品提示/使用须知...</p>
            </div>
  
            <!-- Delivery and returns tab -->
            <div class="tab-pane fade fs-sm" id="delivery-tab-pane" role="tabpanel" aria-labelledby="delivery-tab">
              <p>在这里放一些使用教程 / 文章 等...</p>
            </div>
          </div>
        </section>
  
      </main>
    </div>
  </div>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{ __('goods.fields.buy_prompt') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {!! $buy_prompt !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('dujiaoka.close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal end -->
@stop
@section('js')
<script src="/assets/riniba_03/js/bootstrap-input-spinner.js"></script>
<script>
            @if(!empty($buy_prompt))
            var myModal = new bootstrap.Modal(document.getElementById('staticBackdrop'))
            $(function(){
                myModal.show()
            });
            @endif
            $("input[type='number']").inputSpinner();
            $('#submit').click(function(){
                if($("input[name='by_amount']").val() > {{ $in_stock }}){
                    {{-- 数量不允许大于库存 --}}
                    $(".modal-body").html("{{ __('dujiaoka.prompt.inventory_shortage') }}")
                    myModal.show()
                    return false;
                }
                @if($buy_limit_num > 0)
                if($("input[name='by_amount']").val() > {{ $buy_limit_num }}){
                    {{-- 已超过限购数量 --}}
                    $(".modal-body").html("{{ __('dujiaoka.prompt.purchase_limit_exceeded') }}")
                    myModal.show()
                    return false;
                }
                @endif
            });
</script>

@stop
