@extends('morpho::layouts.seo')
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
  
        <section class="container">
          <div class="row align-items-start">
            <div class="col-md-5 pb-4 pb-md-0 mb-2 mb-sm-3 mb-md-0 pe-md-4">
              <div class="position-relative">
                <div class="product-image-container bg-body-tertiary rounded mb-3 mb-sm-4 mb-md-3 mb-lg-4">
                  <img src="{{ pictureUrl($picture) }}" alt="{{ $gd_name }}" class="product-image">
                </div>
              </div>
            </div>
  
            <div class="col-md-7">
              <div class="ps-md-4 ps-xl-5">
                <form class="needs-validation" novalidate id="buy-form" action="{{ url('order/create') }}" method="post">
                  @csrf
  
                  <h1 class="h3 mb-3">{{ $gd_name }}</h1>
  
                  <div class="d-flex flex-wrap gap-3 gap-xxl-4 fs-sm text-dark-emphasis mb-2">
  
                    <div class="d-flex align-items-center me-3">
                      <i class="ci-delivery fs-xl text-body-emphasis me-2"></i>
                      @if($type == \App\Models\Goods::AUTOMATIC_DELIVERY)
                        自动发货
                      @else
                        人工发货
                      @endif
                    </div>
  
                    <div class="d-flex align-items-center me-3">
                      <i class="ci-broccoli fs-xl text-body-emphasis me-2"></i>
                      库存：<span id="currentStock">
                        @if(count($goods_sub) > 1)
                          {{ $type == 1 ? \App\Models\Carmis::where('sub_id', $goods_sub[0]['id'])->where('status', 1)->count() : $goods_sub[0]['stock'] }}
                        @else
                          {{ $type == 1 ? collect($goods_sub)->sum(fn($sub) => \App\Models\Carmis::where('sub_id', $sub['id'])->where('status', 1)->count()) : collect($goods_sub)->sum('stock') }}
                        @endif
                      </span>
                    </div>
  
                    @if(!empty($wholesale_price_cnf) && is_array($wholesale_price_cnf))
                      <div class="d-flex align-items-center me-3">
                        <i class="ci-leaf fs-xl text-body-emphasis me-2"></i>
                        有批发价
                      </div>
                    @endif
                  </div>
  
                  @if(count($goods_sub) > 1)
                    <div class="mb-4">
                      <label class="form-label fw-semibold pb-1 mb-2">
                        选择规格
                        <span class="text-danger">*</span>:
                      </label>
                      <div class="d-flex flex-wrap gap-2" id="specGroup">
                        @foreach($goods_sub as $index => $sub)
                          @php
                            $subStock = $type == 1 ? 
                              \App\Models\Carmis::where('sub_id', $sub['id'])->where('status', 1)->count() : 
                              $sub['stock'];
                          @endphp
                          <label class="spec-option" data-price="{{ $sub['price'] }}" data-stock="{{ $subStock }}" data-sub-id="{{ $sub['id'] }}">
                            <input type="radio" class="btn-check" name="sub_id" value="{{ $sub['id'] }}" 
                                   id="spec-{{ $sub['id'] }}" @if($index == 0) checked @endif
                                   @if($subStock <= 0) disabled @endif>
                            <span class="btn btn-outline-dark @if($subStock <= 0) disabled @endif">
                              {{ $sub['name'] }} 
                              <small class="text-muted">${{ number_format($sub['price'], 2) }}</small>
                              @if($subStock <= 0)
                                <small class="text-danger">(缺货)</small>
                              @endif
                            </span>
                          </label>
                        @endforeach
                      </div>
                    </div>
                  @else
                    <input type="hidden" name="sub_id" value="{{ $goods_sub[0]['id'] }}">
                  @endif

                  <div class="h4 d-flex align-items-center my-3" id="priceDisplay">
                    @if(count($goods_sub) > 1)
                      $<span id="currentPrice">{{ number_format($goods_sub[0]['price'], 2) }}</span>
                    @else
                      ${{ number_format(collect($goods_sub)->min('price'), 2) }}
                    @endif
                  </div>
  
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
  
                  @if($buy_limit_num > 0)
                    <h6 class="mb-3">
                      <small class="badge bg-danger">
                        {{ __('dujiaoka.purchase_limit') }} ({{ $buy_limit_num }})
                      </small>
                    </h6>
                  @endif
  
                  <input type="hidden" name="gid" value="{{ $id }}">
  
  
                  {{-- 自定义表单字段 --}}
                  @if(isset($customer_form_fields) && is_array($customer_form_fields) && count($customer_form_fields) > 0)
                    @foreach($customer_form_fields as $field)
                      <div class="mb-3">
                        <label for="{{ $field['field_key'] }}" class="form-label">
                          {{ $field['field_description'] }}:
                          @if($field['field_type'] !== 'switch')
                            <span class="text-danger">*</span>
                          @endif
                        </label>
                        @if($field['field_type'] === 'input')
                          <input type="text"
                                 class="form-control"
                                 id="{{ $field['field_key'] }}"
                                 name="custom_fields[{{ $field['field_key'] }}]"
                                 required
                                 placeholder="请输入{{ $field['field_description'] }}">
                        @elseif($field['field_type'] === 'textarea')
                          <textarea class="form-control"
                                    id="{{ $field['field_key'] }}"
                                    name="custom_fields[{{ $field['field_key'] }}]"
                                    rows="3"
                                    required
                                    placeholder="请输入{{ $field['field_description'] }}"></textarea>
                        @elseif($field['field_type'] === 'select')
                          <select class="form-control"
                                  id="{{ $field['field_key'] }}"
                                  name="custom_fields[{{ $field['field_key'] }}]"
                                  required>
                            <option value="">请选择{{ $field['field_description'] }}</option>
                            @if(isset($field['field_options']) && is_array($field['field_options']))
                              @foreach($field['field_options'] as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                              @endforeach
                            @endif
                          </select>
                        @elseif($field['field_type'] === 'switch')
                          <div class="form-check form-switch">
                            <input type="hidden" name="custom_fields[{{ $field['field_key'] }}]" value="0">
                            <input type="checkbox"
                                   class="form-check-input"
                                   id="{{ $field['field_key'] }}"
                                   name="custom_fields[{{ $field['field_key'] }}]"
                                   value="1">
                            <label class="form-check-label" for="{{ $field['field_key'] }}">
                              {{ $field['field_description'] }}
                            </label>
                          </div>
                        @endif
                        @if($field['field_type'] !== 'switch')
                          <div class="invalid-feedback">请填写 {{ $field['field_description'] }}！</div>
                        @endif
                      </div>
                    @endforeach
                  @endif

                  {{-- 兼容旧版本的其他输入项配置 --}}
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
  
                  @if(isset($open_coupon))
                    <div class="mb-3">
                      <label for="coupon" class="form-label">
                        {{ __('dujiaoka.coupon_code') }}:
                      </label>
                      <input type="text" class="form-control" id="coupon" name="coupon_code" placeholder="">
                    </div>
                  @endif
  
                  @if(cfg('is_open_img_code') == \App\Models\Goods::STATUS_OPEN)
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
  
  
                  <div class="d-flex gap-3 pb-3 pb-lg-4 mb-3">
                    <div class="count-input flex-shrink-0 w-50 d-flex justify-content-center align-items-center">
                      <button type="button" class="btn btn-icon btn-lg" data-decrement aria-label="Decrement quantity">
                        <i class="ci-minus"></i>
                      </button>
                      @php
                        $initialStock = count($goods_sub) > 1 ? 
                          ($type == 1 ? \App\Models\Carmis::where('sub_id', $goods_sub[0]['id'])->where('status', 1)->count() : $goods_sub[0]['stock']) :
                          ($type == 1 ? collect($goods_sub)->sum(fn($sub) => \App\Models\Carmis::where('sub_id', $sub['id'])->where('status', 1)->count()) : collect($goods_sub)->sum('stock'));
                      @endphp
                      <input type="number" class="form-control form-control-xl w-50" name="by_amount" min="1"
                             max="{{ $initialStock }}" value="1">
                      <button type="button" class="btn btn-icon btn-lg" data-increment aria-label="Increment quantity">
                        <i class="ci-plus"></i>
                      </button>
                    </div>
                    <input type="hidden" name="aff" value="">
  
                    <div class="d-flex gap-2 w-100">
                      <button type="button" id="addToCart" class="btn btn-lg btn-outline-dark">
                        <i class="ci-shopping-cart me-2"></i>加入购物车
                      </button>
                      <button type="button" id="buyNow" class="btn btn-lg btn-dark">
                        立即购买
                      </button>
                    </div>
                  </div>
  
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
  
  
        <section class="container pt-5 mt-2 mt-sm-3 mt-lg-4 mt-xl-5">
          {{-- 关联文章横排列表 --}}
          @if(isset($relatedArticles) && $relatedArticles->count() > 0)
            <div class="related-articles mb-5">
              <h4 class="mb-3 d-flex align-items-center">
                <i class="ci-book me-2"></i>相关文章
              </h4>
              <div class="row g-3">
                @foreach($relatedArticles as $article)
                  <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card article-card h-100">
                      <div class="card-body d-flex flex-column">
                        <h6 class="card-title mb-2">
                          <a href="{{ route('article.show', $article->link) }}" class="text-decoration-none stretched-link article-title-link">
                            {{ $article->title }}
                          </a>
                        </h6>
                        <p class="card-text small flex-grow-1 article-desc-text">
                          {{ Str::limit(strip_tags($article->content), 80) }}
                        </p>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
            <hr class="my-4">
          @endif

          <div class="mb-4">
            <h4 class="mb-3 d-flex align-items-center">
              <i class="ci-file-text me-2"></i>{{ __('goods.fields.description') }}
            </h4>
          </div>

          <div class="row">
            {!! $description !!}
          </div>
        </section>
        
        <!-- 底部浮动栏的最终停靠位置 -->
        <div class="sticky-banner-container">
          <div class="sticky-product-banner" id="stickyBanner">
            <div class="container px-2">
              <div class="navbar navbar-expand-lg flex-nowrap bg-body rounded-pill shadow ps-0 mx-1">
                <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark rounded-pill z-0 d-none d-block-dark"></div>
                
                <div class="d-flex align-items-center position-relative z-1 ms-3 me-3">
                  <img src="{{ pictureUrl($picture) }}" alt="{{ $gd_name }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                  <div class="ms-3 min-w-0 flex-grow-1">
                    <div class="fw-medium text-truncate">{{ $gd_name }}</div>
                    @if(count($goods_sub) > 1)
                      <div class="text-muted small d-flex align-items-center">
                        <span class="sticky-spec-name me-1">{{ $goods_sub[0]['name'] }}</span>
                        <span class="text-body-secondary">·</span>
                        <span class="fw-medium text-success ms-1">$<span class="sticky-price">{{ number_format($goods_sub[0]['price'], 2) }}</span></span>
                      </div>
                    @else
                      <div class="text-muted small">
                        <span class="fw-medium text-success">${{ number_format(collect($goods_sub)->min('price'), 2) }}</span>
                      </div>
                    @endif
                  </div>
                </div>
                
                <div class="d-flex gap-2 position-relative z-1 ms-auto me-3">
                  <button type="button" id="stickyAddToCart" class="btn btn-outline-dark btn-sm d-none d-md-inline-flex">
                    <i class="ci-shopping-cart me-1"></i>加入购物车
                  </button>
                  <button type="button" id="stickyBuyNow" class="btn btn-dark btn-sm">
                    立即购买
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
  
      </main>
    </div>
  </div>

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
@stop
@section('css')
<style>
.spec-option .btn-check:checked + .btn {
    background-color: #212529;
    border-color: #212529;
    color: #fff;
}

.spec-option .btn {
    transition: all 0.2s ease-in-out;
}

.spec-option .btn:hover:not(.disabled) {
    background-color: #f8f9fa;
    border-color: #212529;
}

.product-image-container {
    position: relative;
    width: 100%;
    aspect-ratio: 1 / 1;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center;
    transition: transform 0.3s ease;
}

.product-image-container:hover .product-image {
    transform: scale(1.05);
}

@media (max-width: 767.98px) {
    .product-image-container {
        max-width: 12.5rem;
        margin: 0 auto;
    }
    
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .h3 {
        font-size: 1.5rem;
    }
}

@media (min-width: 768px) {
    .product-image-container {
        max-width: 16rem;
    }
    
    .col-md-5 {
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }
}

.related-articles .article-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    border: 1px solid #e9ecef;
    position: relative;
}

.related-articles .article-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.related-articles .article-card .stretched-link::after {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1;
    content: "";
}

/* 深色模式适配 */
.article-title-link {
    color: var(--bs-body-color) !important;
    transition: color 0.2s ease-in-out;
}

.article-title-link:hover {
    color: var(--bs-primary) !important;
}

.article-desc-text {
    color: var(--bs-secondary-color) !important;
}

/* 浮动购买栏容器 */
.sticky-banner-container {
    position: relative;
    padding: 1rem 0 2rem 0;
}

/* 底部浮动购买栏 */
.sticky-product-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 1rem 0;
    transform: translateY(100%);
    transition: transform 0.3s ease;
    z-index: 1020;
}

.sticky-product-banner.show {
    transform: translateY(0);
}

/* 当浮动栏到达容器位置时，改为绝对定位但保持全宽 */
.sticky-product-banner.docked {
    position: absolute;
    bottom: auto;
    top: 0;
    left: 0;
    right: 0;
    transform: translateY(0);
    width: 100vw;
    margin-left: calc(-50vw + 50%);
}
</style>
@stop
@section('js')
<script src="{{ asset('assets/morpho/js/payment-icons.js') }}"></script>
<script>
            @if(!empty($buy_prompt))
            var myModal = new bootstrap.Modal(document.getElementById('staticBackdrop'))
            $(function(){
                myModal.show()
            });
            @endif
            const amountInput = $('input[name="by_amount"]');
            const addToCartBtn = $('#addToCart');
            const buyNowBtn = $('#buyNow');
            
            function getSelectedSpec() {
                @if(count($goods_sub) > 1)
                    const selectedSpec = $('.spec-option input:checked').closest('.spec-option');
                    return {
                        goods_id: {{ $id }},
                        sub_id: +selectedSpec.data('sub-id'),
                        stock: +selectedSpec.data('stock')
                    };
                @else
                    return {
                        goods_id: {{ $id }},
                        sub_id: {{ $goods_sub[0]['id'] }},
                        stock: {{ $type == 1 ? collect($goods_sub)->sum(fn($sub) => \App\Models\Carmis::where('sub_id', $sub['id'])->where('status', 1)->count()) : collect($goods_sub)->sum('stock') }}
                    };
                @endif
            }
            
            $(document).on('click', 'button[data-increment]', function() {
                const max = +amountInput.attr('max') || 999;
                const val = +amountInput.val() || 1;
                if (val < max) amountInput.val(val + 1);
            });
            
            $(document).on('click', 'button[data-decrement]', function() {
                const min = +amountInput.attr('min') || 1;
                const val = +amountInput.val() || 1;
                if (val > min) amountInput.val(val - 1);
            });
            
            @if(count($goods_sub) > 1)
                const initStock = {{ $type == 1 ? \App\Models\Carmis::where('sub_id', $goods_sub[0]['id'])->where('status', 1)->count() : $goods_sub[0]['stock'] }};
                if (initStock <= 0) {
                    addToCartBtn.prop('disabled', true);
                    buyNowBtn.prop('disabled', true).text('缺货');
                    $('button[data-increment], button[data-decrement]').prop('disabled', true);
                }
            @endif
            
            $('input[name="sub_id"]').change(function() {
                const opt = $(this).closest('.spec-option');
                const price = +opt.data('price');
                const stock = +opt.data('stock');
                const specName = opt.find('.btn').text().trim().split(' ')[0]; // 获取规格名称，去除价格部分
                
                $('#currentPrice').text(price.toFixed(2));
                $('.sticky-price').text(price.toFixed(2));
                $('.sticky-spec-name').text(specName);
                $('#currentStock').text(stock);
                
                const currentVal = +amountInput.val();
                amountInput.attr('max', stock);
                
                if (currentVal > stock) {
                    amountInput.val(Math.min(stock, 1));
                }
                
                $('button[data-increment], button[data-decrement]').prop('disabled', false);
                
                if (stock <= 0) {
                    addToCartBtn.prop('disabled', true);
                    buyNowBtn.prop('disabled', true).text('缺货');
                    $('button[data-increment], button[data-decrement]').prop('disabled', true);
                } else {
                    addToCartBtn.prop('disabled', false);
                    buyNowBtn.prop('disabled', false).text('立即购买');
                }
            });
            
            addToCartBtn.click(async function() {
                const spec = getSelectedSpec();
                const quantity = +amountInput.val();
                
                if (quantity > spec.stock) {
                    cart.notify('库存不足', 'error');
                    return;
                }
                
                @if($buy_limit_num > 0)
                if (quantity > {{ $buy_limit_num }}) {
                    cart.notify('超出限购数量', 'error');
                    return;
                }
                @endif
                
                // 收集自定义字段数据
                const customFieldsData = {};
                document.querySelectorAll('[name^="custom_fields["]').forEach(field => {
                    const fieldName = field.name.match(/custom_fields\[([^\]]+)\]/)?.[1];
                    if (fieldName) {
                        if (field.type === 'checkbox') {
                            customFieldsData[fieldName] = field.checked ? field.value : '0';
                        } else {
                            customFieldsData[fieldName] = field.value;
                        }
                    }
                });
                
                await cart.validateAndAdd(spec.goods_id, spec.sub_id, quantity, customFieldsData);
            });
            
            buyNowBtn.click(async function() {
                const spec = getSelectedSpec();
                const quantity = +amountInput.val();
                
                if (quantity > spec.stock) {
                    cart.notify('库存不足', 'error');
                    return;
                }
                
                @if($buy_limit_num > 0)
                if (quantity > {{ $buy_limit_num }}) {
                    cart.notify('超出限购数量', 'error');
                    return;
                }
                @endif
                
                // 收集自定义字段数据
                const customFieldsData = {};
                document.querySelectorAll('[name^="custom_fields["]').forEach(field => {
                    const fieldName = field.name.match(/custom_fields\[([^\]]+)\]/)?.[1];
                    if (fieldName) {
                        if (field.type === 'checkbox') {
                            customFieldsData[fieldName] = field.checked ? field.value : '0';
                        } else {
                            customFieldsData[fieldName] = field.value;
                        }
                    }
                });
                
                // 立即购买：创建单品订单数据并直接跳转到购物车页面
                const orderData = {
                    goods_id: spec.goods_id,
                    sub_id: spec.sub_id,
                    quantity: quantity,
                    name: spec.name,
                    price: spec.price,
                    image: '{{ pictureUrl($picture) }}',
                    stock: spec.stock,
                    custom_fields: customFieldsData,
                    buy_now: true
                };
                
                // 存储单品购买数据到sessionStorage
                sessionStorage.setItem('buyNowItem', JSON.stringify(orderData));
                
                // 直接跳转到购物车
                window.location.href = '/cart?buy_now=1';
            });
            
            // 底部浮动购买栏逻辑
            const stickyBanner = document.getElementById('stickyBanner');
            const originalBuyButtons = document.querySelector('.d-flex.gap-2.w-100');
            const stickyAddToCartBtn = document.getElementById('stickyAddToCart');
            const stickyBuyNowBtns = document.querySelectorAll('#stickyBuyNow');
            const bannerContainer = document.querySelector('.sticky-banner-container');
            
            // 监听滚动事件
            let ticking = false;
            function updateStickyBanner() {
                if (!originalBuyButtons || !bannerContainer) return;
                
                const originalRect = originalBuyButtons.getBoundingClientRect();
                const containerRect = bannerContainer.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                
                const isOriginalVisible = originalRect.bottom > 0;
                const containerReachBottom = containerRect.bottom <= windowHeight;
                
                if (!isOriginalVisible && !containerReachBottom) {
                    // 原始按钮不可见，且容器还没到底部 - 显示固定浮动栏
                    stickyBanner.classList.add('show');
                    stickyBanner.classList.remove('docked');
                } else if (!isOriginalVisible && containerReachBottom) {
                    // 原始按钮不可见，但容器已到底部 - 停靠在容器中
                    stickyBanner.classList.add('show', 'docked');
                } else {
                    // 原始按钮可见 - 隐藏浮动栏
                    stickyBanner.classList.remove('show', 'docked');
                }
                
                ticking = false;
            }
            
            function requestTick() {
                if (!ticking) {
                    requestAnimationFrame(updateStickyBanner);
                    ticking = true;
                }
            }
            
            window.addEventListener('scroll', requestTick, { passive: true });
            window.addEventListener('resize', requestTick, { passive: true });
            
            // 浮动栏按钮事件绑定
            if (stickyAddToCartBtn) {
                stickyAddToCartBtn.addEventListener('click', () => {
                    addToCartBtn.click();
                });
            }
            
            stickyBuyNowBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    buyNowBtn.click();
                });
            });
            
            // 同步按钮状态
            function syncStickyButtonsState() {
                const isDisabled = buyNowBtn.prop('disabled');
                const buttonText = buyNowBtn.text();
                
                if (stickyAddToCartBtn) {
                    stickyAddToCartBtn.disabled = addToCartBtn.prop('disabled');
                }
                
                stickyBuyNowBtns.forEach(btn => {
                    btn.disabled = isDisabled;
                    if (btn.classList.contains('d-none', 'd-md-inline-flex')) {
                        btn.textContent = buttonText;
                    }
                });
            }
            
            // 监听规格变化，同步按钮状态
            $('input[name="sub_id"]').on('change', syncStickyButtonsState);
            syncStickyButtonsState();
</script>

@stop
