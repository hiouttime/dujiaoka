@extends('morpho::layouts.seo')
@section('content')
<div class="content">
  <div class="container">
    <main class="content-wrapper">
      
      <nav class="container pt-2 pt-xxl-3 my-3 my-md-4" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">主页</a></li>
          <li class="breadcrumb-item active" aria-current="page">购物车</li>
        </ol>
      </nav>

      <section class="container py-5">
        <h1 class="h3 mb-4">购物车</h1>
        
        <div id="emptyCart" class="text-center py-5" style="display: none;">
          <i class="ci-shopping-cart display-1 text-muted mb-3"></i>
          <h4>购物车是空的</h4>
          <p class="text-muted">快去选购您喜欢的商品吧</p>
          <a href="{{ url('/') }}" class="btn btn-primary">去购物</a>
        </div>

        <div id="cartContent">
          <div class="row">
            <div class="col-lg-8">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">商品清单</h5>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="clearCart">
                      <i class="ci-trash me-1"></i>清空购物车
                    </button>
                  </div>
                  <div id="cartItems">
                    <!-- 购物车商品将在这里动态渲染 -->
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-4">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">订单摘要</h5>
                  
                  <div class="d-flex justify-content-between mb-2">
                    <span>商品总计:</span>
                    <span id="itemsCount">0 件</span>
                  </div>
                  
                  <div class="d-flex justify-content-between mb-3">
                    <span>小计:</span>
                    <span id="subtotal">{{ currency_symbol() }}0.00</span>
                  </div>
                  
                  <hr>
                  
                  <div class="d-flex justify-content-between mb-3">
                    <strong>总计:</strong>
                    <strong id="total">{{ currency_symbol() }}0.00</strong>
                  </div>

                  <form id="checkoutForm" class="mb-3">
                    @php
                      $contactRequired = cfg('contact_required', 'email');
                      $user = Auth::guard('web')->user();
                    @endphp
                    
                    @if($contactRequired === 'email')
                      @guest('web')
                      <div class="mb-3">
                        <label for="email" class="form-label">邮箱地址 <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                      </div>
                      @else
                      <div class="mb-3">
                        <label class="form-label">邮箱地址</label>
                        <div class="form-control-plaintext">{{ $user->email }}</div>
                        <input type="hidden" id="email" name="email" value="{{ $user->email }}">
                      </div>
                      @endguest
                    @elseif($contactRequired === 'any')
                      @guest('web')
                      <div class="mb-3">
                        <label for="email" class="form-label">联系方式 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="email" name="email" 
                               placeholder="请输入至少6位字符" minlength="6" required>
                        <small class="form-text text-muted">可以是邮箱、QQ号、微信号等任意联系方式</small>
                      </div>
                      @else
                      <div class="mb-3">
                        <label class="form-label">联系方式</label>
                        <div class="form-control-plaintext">{{ $user->email }}</div>
                        <input type="hidden" id="email" name="email" value="{{ $user->email }}">
                      </div>
                      @endguest
                    @endif

                    @if(cfg('is_open_search_pwd', \App\Models\BaseModel::STATUS_CLOSE) == \App\Models\BaseModel::STATUS_OPEN)
                    <div class="mb-3">
                      <label for="search_pwd" class="form-label">查询密码</label>
                      <input type="text" class="form-control" id="search_pwd" name="search_pwd">
                    </div>
                    @endif

                    <div class="mb-3">
                      <label class="form-label">
                        支付方式：<span id="selectedPaymentName">请选择</span>
                        <span class="text-danger">*</span>
                      </label>
                      <div class="d-flex flex-wrap gap-2" id="paymentMethods">
                        <!-- 支付方式将在这里动态加载 -->
                      </div>
                    </div>
                  </form>

                  <button type="button" class="btn btn-dark w-100" id="proceedCheckout">
                    立即结算
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

    </main>
  </div>
</div>
@stop


@section('js')
<script src="{{ asset('assets/morpho/js/payment-icons.js') }}"></script>
<script>
const CURRENCY_SYMBOL = '{{ currency_symbol() }}';
let paymentMethods = [];

document.addEventListener('DOMContentLoaded', function() {
  renderCart();
});


function loadPaymentMethods(items) {
  if (items.length === 0) {
    paymentMethods = [];
    renderPaymentMethods();
    return;
  }

  // 计算支付方式交集
  let common = null;
  for (const item of items) {
    if (!item.payways || !Array.isArray(item.payways)) continue;
    
    if (common === null) {
      common = [...item.payways];
    } else {
      // 通过id字段比较取交集
      common = common.filter(method => 
        item.payways.some(itemMethod => itemMethod.id === method.id)
      );
    }
  }

  if (!common || common.length === 0) {
    paymentMethods = [];
    cart.notify('所选商品没有共同的支付方式，请重新选择商品', 'error');
  } else {
    paymentMethods = common;
  }
  
  renderPaymentMethods();
}

function renderPaymentMethods() {
  const container = document.getElementById('paymentMethods');
  container.innerHTML = '';
  
  if (paymentMethods.length === 0) {
    container.innerHTML = '<div class="alert alert-warning">没有可用的支付方式</div>';
    return;
  }
  
  paymentMethods.forEach((method, index) => {
    
    const option = document.createElement('label');
    option.className = 'payment-method-option';
    option.innerHTML = `
      <input type="radio" class="btn-check" name="payway" value="${method.id}" 
             id="pay-${method.id}" ${index === 0 ? 'checked' : ''}>
      <span class="btn btn-outline-dark">
        <div class="paymentsvg" data-type="${method.pay_check || method.name.toLowerCase()}"></div>
      </span>
    `;
    
    option.addEventListener('change', (e) => {
      if (e.target.checked) {
        document.getElementById('selectedPaymentName').textContent = method.pay_name || method.name;
      }
    });
    
    // 设置默认选中的支付方式名称
    if (index === 0) {
      document.getElementById('selectedPaymentName').textContent = method.pay_name || method.name;
    }
    
    container.appendChild(option);
  });
  
  // 初始化支付图标
  if (window.PaymentIcons) {
    new PaymentIcons();
  }
}

function renderCart() {
  const items = cart.getItems();
  const cartContent = document.getElementById('cartContent');
  const emptyCart = document.getElementById('emptyCart');
  
  if (items.length === 0) {
    cartContent.style.display = 'none';
    emptyCart.style.display = 'block';
    return;
  }
  
  cartContent.style.display = 'block';
  emptyCart.style.display = 'none';
  
  const cartItemsContainer = document.getElementById('cartItems');
  cartItemsContainer.innerHTML = '';
  
  items.forEach(item => {
    const itemElement = document.createElement('div');
    itemElement.className = 'cart-item';
    
    // 生成自定义字段摘要
    const fieldsHtml = item.custom_fields && Object.keys(item.custom_fields).length 
      ? `<div class="mt-2">${Object.entries(item.custom_fields).map(([key, value]) => 
          `<small class="text-muted d-block">${key}: ${['0', '1', 0, 1].includes(value) ? (value == 1 ? '是' : '否') : value}</small>`
        ).join('')}</div>`
      : '';
    
    itemElement.innerHTML = `
      <div class="row align-items-center">
        <div class="col-auto">
          <img src="${item.image || '/assets/common/images/default.jpg'}" class="cart-item-image" alt="${item.name}">
        </div>
        <div class="col">
          <h6 class="mb-1">${item.name}</h6>
          <small class="text-muted">单价: ${CURRENCY_SYMBOL}${item.price.toFixed(2)}</small>
          ${fieldsHtml}
        </div>
        <div class="col-auto">
          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="updateQuantity(${item.goods_id}, ${item.sub_id}, ${item.quantity - 1})">
              <i class="ci-minus"></i>
            </button>
            <input type="number" class="form-control form-control-sm text-center" min="1" max="${item.stock}" 
                   value="${item.quantity}" onchange="updateQuantity(${item.goods_id}, ${item.sub_id}, this.value)" style="width: 60px;">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="updateQuantity(${item.goods_id}, ${item.sub_id}, ${item.quantity + 1})">
              <i class="ci-plus"></i>
            </button>
          </div>
        </div>
        <div class="col-auto">
          <strong>${CURRENCY_SYMBOL}${(item.price * item.quantity).toFixed(2)}</strong>
        </div>
        <div class="col-auto">
          <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeItem(${item.goods_id}, ${item.sub_id})">
            <i class="ci-trash"></i>
          </button>
        </div>
      </div>
    `;
    cartItemsContainer.appendChild(itemElement);
  });
  
  updateSummary();
  loadPaymentMethods(items);
}

function updateQuantity(goodsId, subId, quantity) {
  quantity = parseInt(quantity);
  if (quantity <= 0) {
    removeItem(goodsId, subId);
  } else {
    cart.updateQuantity(goodsId, subId, quantity);
    renderCart();
  }
}

function removeItem(goodsId, subId) {
  cart.remove(goodsId, subId);
  renderCart();
}

function updateSummary() {
  const totalQuantity = cart.getTotalQuantity();
  const totalPrice = cart.getTotalPrice();
  
  document.getElementById('itemsCount').textContent = `${totalQuantity} 件`;
  document.getElementById('subtotal').textContent = `${CURRENCY_SYMBOL}${totalPrice.toFixed(2)}`;
  document.getElementById('total').textContent = `${CURRENCY_SYMBOL}${totalPrice.toFixed(2)}`;
}


document.getElementById('clearCart').addEventListener('click', function() {
  if (confirm('确定要清空购物车吗？')) {
    cart.clear();
    renderCart();
  }
});

document.getElementById('proceedCheckout').addEventListener('click', async function() {
  const items = cart.getItems();
  if (items.length === 0) {
    cart.notify('购物车为空', 'error');
    return;
  }
  
  const form = document.getElementById('checkoutForm');
  const formData = new FormData(form);
  
  // 检查联系信息字段
  const emailInput = document.getElementById('email');
  
  if (emailInput && emailInput.type === 'email' && !formData.get('email')) {
    cart.notify('请输入邮箱地址', 'error');
    return;
  }
  
  if (emailInput && emailInput.type === 'text' && emailInput.required && !formData.get('email')) {
    cart.notify('请输入联系方式', 'error');
    return;
  }
  
  if (emailInput && emailInput.type === 'text' && formData.get('email') && formData.get('email').length < 6) {
    cart.notify('联系方式至少需要6位字符', 'error');
    return;
  }
  
  if (!formData.get('payway')) {
    cart.notify('请选择支付方式', 'error');
    return;
  }
  
  const orderData = {
    email: formData.get('email') || formData.get('contact') || '',
    search_pwd: formData.get('search_pwd'),
    payway: formData.get('payway'),
    cart_items: items.map(item => ({
      goods_id: item.goods_id,
      sub_id: item.sub_id,
      quantity: item.quantity,
      custom_fields: item.custom_fields || {}
    }))
  };
  
  try {
    this.disabled = true;
    this.textContent = '处理中...';
    
    const response = await fetch('/order/create', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: JSON.stringify(orderData)
    });
    
    const result = await response.json();
    
    if (result.success) {
      cart.clear();
      window.location.href = result.redirect;
    } else {
      cart.notify(result.message, 'error');
    }
  } catch (error) {
    cart.notify('订单创建失败，请重试', 'error');
  } finally {
    this.disabled = false;
    this.textContent = '立即结算';
  }
});
</script>
@stop