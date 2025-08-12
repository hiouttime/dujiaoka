<style>
.cart-dropdown {
    max-height: 400px;
    overflow-y: auto;
}

.cart-dropdown .dropdown-menu {
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    border: none;
}

.cart-dropdown-item:hover {
    background-color: #f8f9fa;
}

/* 让dropdown在hover时显示 */
.dropdown:hover .dropdown-menu {
    display: block;
}

.dropdown .dropdown-toggle::after {
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cartDropdown = document.querySelector('.cart-icon').closest('.dropdown');
    const dropdownMenu = cartDropdown?.querySelector('.dropdown-menu');
    let hoverTimer;

    if (cartDropdown && dropdownMenu) {
        // 鼠标进入时显示dropdown
        cartDropdown.addEventListener('mouseenter', function() {
            clearTimeout(hoverTimer);
            dropdownMenu.classList.add('show');
        });

        // 鼠标离开时延迟隐藏dropdown
        cartDropdown.addEventListener('mouseleave', function() {
            hoverTimer = setTimeout(() => {
                dropdownMenu.classList.remove('show');
            }, 200);
        });

        // 点击购物车图标时跳转到购物车页面
        const cartIcon = cartDropdown.querySelector('.cart-icon');
        cartIcon?.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/cart';
        });
    }
});
</script>