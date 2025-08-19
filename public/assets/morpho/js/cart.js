class Cart {
    constructor() {
        this.items = this.load();
        this.updateUI();
    }

    load() {
        const data = localStorage.getItem('cart');
        return data ? JSON.parse(data) : [];
    }

    save() {
        localStorage.setItem('cart', JSON.stringify(this.items));
        this.updateUI();
    }

    add(item) {
        const existing = this.items.find(i => 
            i.goods_id === item.goods_id && i.sub_id === item.sub_id
        );

        if (existing) {
            existing.quantity += item.quantity;
            existing.custom_fields = {...existing.custom_fields, ...item.custom_fields};
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

    updateQuantity(goodsId, subId, qty) {
        const item = this.findItem(goodsId, subId);
        if (!item) return;
        
        qty <= 0 ? this.remove(goodsId, subId) : (item.quantity = qty, this.save());
    }

    findItem(goodsId, subId) {
        return this.items.find(i => i.goods_id === goodsId && i.sub_id === subId);
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

    updateUI() {
        this.updateIcon();
        this.updateDropdown();
    }

    updateIcon() {
        const qty = this.getTotalQuantity();
        const badge = document.getElementById('cartCount');
        
        if (badge) {
            badge.textContent = qty;
            badge.style.display = qty > 0 ? 'inline' : 'none';
        }
    }

    updateDropdown() {
        const items = this.getItems();
        const qty = this.getTotalQuantity();
        const total = this.getTotalPrice();
        
        const els = {
            count: document.getElementById('cartDropdownCount'),
            container: document.getElementById('cartDropdownItems'),
            empty: document.getElementById('cartDropdownEmpty'),
            footer: document.getElementById('cartDropdownFooter'),
            total: document.getElementById('cartDropdownTotal')
        };

        if (!els.count || !els.container) return;

        els.count.textContent = `${qty} 件商品`;
        
        if (items.length === 0) {
            els.empty?.style && (els.empty.style.display = 'block');
            els.footer?.style && (els.footer.style.display = 'none');
            els.container.innerHTML = this.renderEmptyCart();
        } else {
            els.empty?.style && (els.empty.style.display = 'none');
            els.footer?.style && (els.footer.style.display = 'block');
            els.total && (els.total.textContent = `$${total.toFixed(2)}`);
            els.container.innerHTML = this.renderCartItems(items);
        }
    }

    renderEmptyCart() {
        return `<div class="text-center text-muted py-4">
            <i class="ci-shopping-cart fs-2 mb-2 d-block"></i>
            <small>购物车是空的</small>
        </div>`;
    }

    renderCartItems(items) {
        const itemsHtml = items.slice(0, 3).map(item => `
            <div class="d-flex align-items-center mb-3">
                <img src="${item.image || '/assets/common/images/default.jpg'}" 
                     alt="${item.name}" class="rounded me-3" 
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
        `).join('');
        
        const moreHtml = items.length > 3 ? 
            `<div class="text-center text-muted small">还有 ${items.length - 3} 件商品...</div>` : '';
        
        return itemsHtml + moreHtml;
    }

    async validateAndAdd(goodsId, subId, qty = 1, customFields = {}) {
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch('/api/cart/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token || ''
                },
                body: JSON.stringify({goods_id: goodsId, sub_id: subId, quantity: qty})
            });

            const {success, data, message} = await res.json();
            
            if (success) {
                this.add({
                    goods_id: goodsId,
                    sub_id: subId,
                    name: data.name,
                    price: +data.price,
                    image: data.image,
                    quantity: qty,
                    stock: data.stock,
                    max_quantity: data.max_quantity,
                    custom_fields: customFields,
                    payways: data.payways.map(({id, name, pay_name}) => ({id, name: name || pay_name}))
                });
                
                this.animate();
                this.notify('已添加到购物车', 'success');
                return true;
            }
            
            this.notify(message, 'error');
            return false;
        } catch (error) {
            this.notify('添加失败，请重试', 'error');
            return false;
        }
    }

    notify(msg, type = 'info') {
        const typeMap = {success: 'alert-success', error: 'alert-danger', info: 'alert-info'};
        
        const alert = document.createElement('div');
        alert.className = `alert ${typeMap[type]} alert-dismissible fade show position-fixed`;
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alert.innerHTML = `${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        
        document.body.appendChild(alert);
        setTimeout(() => alert.remove(), 3000);
    }

    animate() {
        const icon = document.querySelector('.cart-icon');
        if (!icon) return;
        
        icon.classList.add('cart-bounce');
        setTimeout(() => icon.classList.remove('cart-bounce'), 600);
    }
}

// 用户交互逻辑
class UserInterface {
    static init() {
        const userAvatar = document.querySelector('.user-avatar-trigger');
        if (userAvatar) {
            userAvatar.addEventListener('click', function(e) {
                if (!this.getAttribute('aria-expanded') || this.getAttribute('aria-expanded') === 'false') {
                    location.href = this.href;
                }
            });
        }
    }
}

// 初始化
document.addEventListener('DOMContentLoaded', UserInterface.init);
window.cart = new Cart();