class Cart {
    constructor() {
        this.items = this.load();
        this.updateCartIcon();
    }

    load() {
        const data = localStorage.getItem('cart');
        return data ? JSON.parse(data) : [];
    }

    save() {
        localStorage.setItem('cart', JSON.stringify(this.items));
        this.updateCartIcon();
    }

    add(item) {
        const existingIndex = this.items.findIndex(i => 
            i.goods_id === item.goods_id && i.sub_id === item.sub_id
        );

        if (existingIndex > -1) {
            this.items[existingIndex].quantity += item.quantity;
            // 更新自定义字段
            this.items[existingIndex].custom_fields = {
                ...this.items[existingIndex].custom_fields,
                ...item.custom_fields
            };
        } else {
            this.items.push(item);
        }

        this.save();
        return true;
    }

    remove(goodsId, subId) {
        this.items = this.items.filter(item => 
            !(item.goods_id === goodsId && item.sub_id === subId)
        );
        this.save();
    }

    updateQuantity(goodsId, subId, quantity) {
        const item = this.items.find(i => 
            i.goods_id === goodsId && i.sub_id === subId
        );
        
        if (item) {
            if (quantity <= 0) {
                this.remove(goodsId, subId);
            } else {
                item.quantity = quantity;
                this.save();
            }
        }
    }

    clear() {
        this.items = [];
        this.save();
    }

    getItems() {
        return this.items;
    }

    getTotalQuantity() {
        return this.items.reduce((total, item) => total + item.quantity, 0);
    }

    getTotalPrice() {
        return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    }

    updateCartIcon() {
        const quantity = this.getTotalQuantity();
        const totalPrice = this.getTotalPrice();
        const cartBadge = document.querySelector('.cart-badge');
        const cartIcon = document.querySelector('.cart-icon');
        
        // 更新购物车图标角标
        if (cartIcon) {
            if (quantity > 0) {
                if (!cartBadge) {
                    const badge = document.createElement('span');
                    badge.className = 'cart-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                    badge.textContent = quantity;
                    cartIcon.appendChild(badge);
                } else {
                    cartBadge.textContent = quantity;
                }
            } else {
                if (cartBadge) {
                    cartBadge.remove();
                }
            }
        }

        // 更新下拉窗口内容
        this.updateCartDropdown();
    }

    updateCartDropdown() {
        const items = this.getItems();
        const quantity = this.getTotalQuantity();
        const totalPrice = this.getTotalPrice();
        
        const countElement = document.getElementById('cartDropdownCount');
        const itemsContainer = document.getElementById('cartDropdownItems');
        const emptyElement = document.getElementById('cartDropdownEmpty');
        const footerElement = document.getElementById('cartDropdownFooter');
        const totalElement = document.getElementById('cartDropdownTotal');

        if (!countElement || !itemsContainer) return;

        countElement.textContent = `${quantity} 件商品`;
        
        if (items.length === 0) {
            if (emptyElement) emptyElement.style.display = 'block';
            if (footerElement) footerElement.style.display = 'none';
            itemsContainer.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="ci-shopping-cart fs-2 mb-2 d-block"></i>
                    <small>购物车是空的</small>
                </div>
            `;
        } else {
            if (emptyElement) emptyElement.style.display = 'none';
            if (footerElement) footerElement.style.display = 'block';
            if (totalElement) totalElement.textContent = `$${totalPrice.toFixed(2)}`;
            
            itemsContainer.innerHTML = items.slice(0, 3).map(item => `
                <div class="d-flex align-items-center mb-3">
                    <img src="${item.image || '/assets/common/images/default.jpg'}" 
                         alt="${item.name}" 
                         class="rounded me-3" 
                         style="width: 48px; height: 48px; object-fit: cover;">
                    <div class="flex-grow-1 me-2">
                        <h6 class="mb-1 fs-sm">${item.name}</h6>
                        <div class="d-flex align-items-center text-muted small">
                            <span>$${item.price.toFixed(2)}</span>
                            <span class="mx-2">×</span>
                            <span>${item.quantity}</span>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-medium small">$${(item.price * item.quantity).toFixed(2)}</div>
                    </div>
                </div>
            `).join('') + (items.length > 3 ? `
                <div class="text-center text-muted small">
                    还有 ${items.length - 3} 件商品...
                </div>
            ` : '');
        }
    }

    async validateAndAdd(goodsId, subId, quantity = 1, customFields = {}) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch('/api/cart/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({
                    goods_id: goodsId,
                    sub_id: subId,
                    quantity: quantity
                })
            });

            const result = await response.json();
            
            if (result.success) {
                this.add({
                    goods_id: goodsId,
                    sub_id: subId,
                    name: result.data.name,
                    price: parseFloat(result.data.price),
                    image: result.data.image,
                    quantity: quantity,
                    stock: result.data.stock,
                    max_quantity: result.data.max_quantity,
                    custom_fields: customFields,
                    // 只存储支付方式基本信息
                    payways: result.data.payways.map(({id, name, pay_name}) => ({id, name: name || pay_name}))
                });
                
                this.showAddToCartAnimation();
                this.showMessage('已添加到购物车', 'success');
                return true;
            } else {
                this.showMessage(result.message, 'error');
                return false;
            }
        } catch (error) {
            this.showMessage('添加失败，请重试', 'error');
            return false;
        }
    }

    showMessage(message, type = 'info') {
        const alertClass = type === 'success' ? 'alert-success' : 
                          type === 'error' ? 'alert-danger' : 'alert-info';
        
        const alertElement = document.createElement('div');
        alertElement.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        alertElement.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertElement.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertElement);
        
        setTimeout(() => {
            if (alertElement.parentNode) {
                alertElement.remove();
            }
        }, 3000);
    }

    showAddToCartAnimation() {
        const cartIcon = document.querySelector('.cart-icon');
        if (!cartIcon) return;
        
        // 创建飞入动画效果
        cartIcon.classList.add('cart-bounce');
        
        setTimeout(() => {
            cartIcon.classList.remove('cart-bounce');
        }, 600);
    }
}

window.cart = new Cart();