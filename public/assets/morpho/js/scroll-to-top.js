// 控制置顶按钮显示/隐藏和进度动画
document.addEventListener('DOMContentLoaded', function() {
    const scrollTopBtn = document.querySelector('.btn-scroll-top');
    if (!scrollTopBtn) return;
    
    const svgRect = scrollTopBtn.querySelector('svg rect');
    const dashArray = 155.201; // SVG圆角矩形周长
    
    // 初始隐藏
    scrollTopBtn.style.opacity = '0';
    scrollTopBtn.style.visibility = 'hidden';
    scrollTopBtn.style.transform = 'translateX(100%)';
    scrollTopBtn.style.transition = 'all 0.3s ease';
    
    if (svgRect) {
        svgRect.style.strokeDasharray = dashArray;
        svgRect.style.strokeDashoffset = dashArray;
    }
    
    function updateScrollProgress() {
        const scrollTop = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrollPercent = scrollTop / docHeight;
        
        // 显示/隐藏按钮
        if (scrollTop > 300) {
            scrollTopBtn.style.opacity = '1';
            scrollTopBtn.style.visibility = 'visible';
            scrollTopBtn.style.transform = 'translateX(0)';
        } else {
            scrollTopBtn.style.opacity = '0';
            scrollTopBtn.style.visibility = 'hidden';
            scrollTopBtn.style.transform = 'translateX(100%)';
        }
        
        // 更新进度圆环
        if (svgRect && scrollTop > 300) {
            const offset = dashArray - (scrollPercent * dashArray);
            svgRect.style.strokeDashoffset = offset;
        }
    }
    
    // 节流函数
    let ticking = false;
    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(updateScrollProgress);
            ticking = true;
            setTimeout(() => ticking = false, 16);
        }
    }
    
    window.addEventListener('scroll', requestTick);
    
    // 平滑滚动到顶部
    scrollTopBtn.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});