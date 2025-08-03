// morpho主题JavaScript
class morphoTheme {
    constructor() {
        this.init();
    }

    init() {
        this.applyThemeConfig();
        this.initAnimations();
        this.initThemeToggle();
    }

    // 应用主题配置
    applyThemeConfig() {
        const themeConfig = window.themeConfig || {};
        
        // 应用颜色配置
        if (themeConfig.primary_color) {
            document.documentElement.style.setProperty('--theme-primary', themeConfig.primary_color);
        }
        if (themeConfig.secondary_color) {
            document.documentElement.style.setProperty('--theme-secondary', themeConfig.secondary_color);
        }
        if (themeConfig.accent_color) {
            document.documentElement.style.setProperty('--theme-accent', themeConfig.accent_color);
        }
        if (themeConfig.background_gradient) {
            document.documentElement.style.setProperty('--theme-bg-gradient', themeConfig.background_gradient);
        }

        // 应用动画设置
        if (themeConfig.animation_enabled) {
            document.body.classList.add('animate-enabled');
        }
    }

    // 初始化动画
    initAnimations() {
        // 滚动动画
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // 观察需要动画的元素
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    }

    // 初始化主题切换
    initThemeToggle() {
        const toggle = document.querySelector('.theme-toggle');
        if (toggle) {
            toggle.addEventListener('click', () => {
                document.body.classList.toggle('dark-mode');
                localStorage.setItem('theme-mode', 
                    document.body.classList.contains('dark-mode') ? 'dark' : 'light'
                );
            });
        }

        // 恢复保存的主题
        const savedTheme = localStorage.getItem('theme-mode');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-mode');
        }
    }

    // 霓虹效果
    static addmorphoEffect(element, color) {
        element.style.boxShadow = `
            0 0 5px ${color},
            0 0 10px ${color},
            0 0 15px ${color},
            0 0 20px ${color}
        `;
    }

    // 移除霓虹效果
    static removemorphoEffect(element) {
        element.style.boxShadow = '';
    }
}

// 工具函数
const ThemeUtils = {
    // 获取主题配置
    getConfig(key, defaultValue = null) {
        return window.themeConfig?.[key] || defaultValue;
    },

    // 设置CSS变量
    setCSSVariable(name, value) {
        document.documentElement.style.setProperty(name, value);
    },

    // 添加类名
    addClass(element, className) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        element?.classList.add(className);
    },

    // 移除类名
    removeClass(element, className) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        element?.classList.remove(className);
    }
};

// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', () => {
    new morphoTheme();
});

// 导出到全局
window.morphoTheme = morphoTheme;
window.ThemeUtils = ThemeUtils;