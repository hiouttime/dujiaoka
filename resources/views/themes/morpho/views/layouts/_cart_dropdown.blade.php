
<script>
document.addEventListener('DOMContentLoaded', () => {
    const cart = document.querySelector('.cart-icon')?.closest('.dropdown');
    const menu = cart?.querySelector('.dropdown-menu');
    let timer;

    if (cart && menu) {
        // 悬浮显示/隐藏
        cart.addEventListener('mouseenter', () => {
            clearTimeout(timer);
            menu.classList.add('show');
        });

        cart.addEventListener('mouseleave', () => {
            timer = setTimeout(() => menu.classList.remove('show'), 200);
        });

        // 点击跳转
        cart.querySelector('.cart-icon')?.addEventListener('click', e => {
            e.preventDefault();
            location.href = '/cart';
        });
    }
});
</script>