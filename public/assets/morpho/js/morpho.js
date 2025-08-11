const utils = {
  // 防抖节流
  debounce(fn, delay = 300) {
    let timer;
    return (...args) => {
      clearTimeout(timer);
      timer = setTimeout(() => fn(...args), delay);
    };
  },

  throttle(fn, delay = 300) {
    let timer, lastRun = 0;
    return (...args) => {
      if (Date.now() - lastRun > delay) {
        fn(...args);
        lastRun = Date.now();
      } else {
        clearTimeout(timer);
        timer = setTimeout(() => {
          fn(...args);
          lastRun = Date.now();
        }, delay - (Date.now() - lastRun));
      }
    };
  },

  parseJSON(str, fallback = {}) {
    try {
      return JSON.parse(str);
    } catch {
      return fallback;
    }
  },

  emit(el, event, data = null) {
    el.dispatchEvent(new CustomEvent(event, { detail: data }));
  }
};

class StickyNav {
  constructor() {
    this.nav = document.querySelector('[data-sticky-navbar]');
    if (!this.nav) return;
    
    this.offset = utils.parseJSON(this.nav.dataset.stickyNavbar, { offset: 200 }).offset;
    this.isSticky = false;
    this.handleScroll = utils.throttle(this.onScroll.bind(this), 10);
    
    window.addEventListener('scroll', this.handleScroll);
  }

  onScroll() {
    const shouldStick = window.scrollY > this.offset;
    
    if (shouldStick && !this.isSticky) {
      this.nav.classList.add('navbar-stuck');
      this.isSticky = true;
      utils.emit(this.nav, 'navbar:stuck');
    } else if (!shouldStick && this.isSticky) {
      this.nav.classList.remove('navbar-stuck');
      this.isSticky = false;
      utils.emit(this.nav, 'navbar:unstuck');
    }
  }
}

// 粘性元素
class StickyElements {
  constructor() {
    this.elements = document.querySelectorAll('[data-sticky-element]');
    if (!this.elements.length) return;
    
    this.handleScroll = utils.throttle(this.onScroll.bind(this), 10);
    window.addEventListener('scroll', this.handleScroll);
  }

  onScroll() {
    this.elements.forEach(el => {
      const config = utils.parseJSON(el.dataset.stickyElement, { offset: 0 });
      const shouldStick = window.scrollY > config.offset;
      el.classList.toggle('is-stuck', shouldStick);
    });
  }
}

// 密码显示切换
class PasswordToggle {
  constructor() {
    this.toggles = document.querySelectorAll('[data-password-toggle]');
    this.initToggles();
  }

  initToggles() {
    this.toggles.forEach(toggle => {
      const input = document.querySelector(toggle.dataset.passwordToggle);
      if (!input) return;

      toggle.addEventListener('click', () => {
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        
        const icon = toggle.querySelector('i');
        if (icon) {
          icon.className = isPassword ? 'ci-eye-off' : 'ci-eye';
        }
      });
    });
  }
}

// 数量输入控制
class CountInput {
  constructor() {
    this.containers = document.querySelectorAll('[data-count-input]');
    this.initInputs();
  }

  initInputs() {
    this.containers.forEach(container => {
      const input = container.querySelector('input[type="number"]');
      const btnDec = container.querySelector('[data-decrement]');
      const btnInc = container.querySelector('[data-increment]');
      
      if (!input) return;

      const min = parseInt(input.min) || 1;
      const max = parseInt(input.max) || 999;
      const step = parseInt(input.step) || 1;

      btnDec?.addEventListener('click', () => {
        const current = parseInt(input.value) || min;
        input.value = Math.max(min, current - step);
        utils.emit(input, 'input');
      });

      btnInc?.addEventListener('click', () => {
        const current = parseInt(input.value) || min;
        input.value = Math.min(max, current + step);
        utils.emit(input, 'input');
      });
    });
  }
}

// 表单验证
class FormValidator {
  constructor() {
    this.forms = document.querySelectorAll('[data-form-validate]');
    this.initValidation();
  }

  initValidation() {
    this.forms.forEach(form => {
      form.addEventListener('submit', e => {
        if (!this.validateForm(form)) {
          e.preventDefault();
        }
      });

      // 实时验证
      const inputs = form.querySelectorAll('input, textarea, select');
      inputs.forEach(input => {
        input.addEventListener('blur', () => this.validateField(input));
      });
    });
  }

  validateForm(form) {
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
      if (!this.validateField(input)) {
        isValid = false;
      }
    });

    return isValid;
  }

  validateField(input) {
    const isEmpty = !input.value.trim();
    const isEmail = input.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value);
    const isInvalid = isEmpty || (input.value && isEmail);

    input.classList.toggle('is-invalid', isInvalid);
    input.classList.toggle('is-valid', !isInvalid && input.value);

    return !isInvalid;
  }
}

// 返回顶部
class ScrollToTop {
  constructor() {
    this.btn = document.querySelector('[data-scroll-top]');
    if (!this.btn) return;

    this.threshold = 300;
    this.handleScroll = utils.throttle(this.onScroll.bind(this), 100);
    
    window.addEventListener('scroll', this.handleScroll);
    this.btn.addEventListener('click', this.scrollToTop);
  }

  onScroll() {
    this.btn.classList.toggle('show', window.scrollY > this.threshold);
  }

  scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
}

// 复制处理
class CopyHandler {
  constructor() {
    this.buttons = document.querySelectorAll('[data-copy]');
    this.initButtons();
  }

  initButtons() {
    this.buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        const target = btn.dataset.copy;
        const text = target.startsWith('#') 
          ? document.querySelector(target)?.textContent 
          : target;
        
        this.copyText(text, btn);
      });
    });
  }

  async copyText(text, btn) {
    try {
      await navigator.clipboard.writeText(text);
      this.showSuccess(btn);
    } catch {
      this.fallbackCopy(text, btn);
    }
  }

  fallbackCopy(text, btn) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
      document.execCommand('copy');
      this.showSuccess(btn);
    } catch {
      console.error('复制失败');
    } finally {
      document.body.removeChild(textarea);
    }
  }

  showSuccess(btn) {
    const original = btn.textContent;
    btn.textContent = '已复制!';
    btn.disabled = true;
    
    setTimeout(() => {
      btn.textContent = original;
      btn.disabled = false;
    }, 2000);
  }
}

// Bootstrap组件初始化
class BootstrapInit {
  constructor() {
    if (!window.bootstrap) return;
    
    this.initTooltips();
    this.initPopovers();
    this.initHoverDropdowns();
  }

  initTooltips() {
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
      .forEach(el => new window.bootstrap.Tooltip(el));
  }

  initPopovers() {
    document.querySelectorAll('[data-bs-toggle="popover"]')
      .forEach(el => new window.bootstrap.Popover(el));
  }

  initHoverDropdowns() {
    if ('ontouchstart' in window) return; // 跳过触摸设备

    document.querySelectorAll('[data-bs-toggle="dropdown"][data-bs-trigger="hover"]')
      .forEach(dropdown => {
        const instance = new window.bootstrap.Dropdown(dropdown);
        
        dropdown.addEventListener('mouseover', () => instance.show());
        dropdown.parentNode.addEventListener('mouseleave', () => instance.hide());
      });
  }
}


// Swiper初始化
class SwiperInit {
  constructor() {
    if (typeof Swiper === 'undefined') return;
    
    this.swipers = new Map();
    this.initAll();
  }

  initAll() {
    document.querySelectorAll('[data-swiper]').forEach((el, i) => {
      const config = utils.parseJSON(el.dataset.swiper, {});
      const defaultConfig = {
        loop: false,
        speed: 400,
        spaceBetween: 20,
        slidesPerView: 'auto',
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev'
        },
        pagination: {
          el: '.swiper-pagination',
          clickable: true
        },
        scrollbar: {
          el: '.swiper-scrollbar',
          draggable: true
        }
      };

      const swiper = new Swiper(el, { ...defaultConfig, ...config });
      this.swipers.set(el.id || `swiper-${i}`, swiper);
    });
  }
}

// 主应用类
class MorphoApp {
  constructor() {
    this.components = [];
    this.init();
  }

  init() {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => this.start());
    } else {
      this.start();
    }
  }

  async start() {
    // 等待Bootstrap加载
    if (window.bootstrap || await this.waitForBootstrap()) {
      this.initComponents();
      this.onReady();
    }
  }

  waitForBootstrap() {
    return new Promise(resolve => {
      if (window.bootstrap) return resolve(true);
      
      setTimeout(() => {
        resolve(!!window.bootstrap);
      }, 100);
    });
  }

  initComponents() {
    try {
      // 按优先级初始化组件
      this.components = [
        new StickyNav(),
        new StickyElements(), 
        new PasswordToggle(),
        new CountInput(),
        new FormValidator(),
        new ScrollToTop(),
        new CopyHandler(),
        new BootstrapInit(),
        new SwiperInit(),
        new ThemeSwitcher()
      ].filter(Boolean);
    } catch (error) {
      console.error('初始化失败:', error);
    }
  }

  onReady() {
    utils.emit(document, 'morpho:ready', { components: this.components });
  }
}

// 主题切换器
class ThemeSwitcher {
  constructor() {
    this.init();
  }

  init() {
    this.setTheme(this.getPreferredTheme());
    this.showActiveTheme(this.getPreferredTheme());
    this.bindEvents();
  }

  getStoredTheme() {
    return localStorage.getItem('theme');
  }

  setStoredTheme(theme) {
    localStorage.setItem('theme', theme);
  }

  getPreferredTheme() {
    const stored = this.getStoredTheme();
    return stored || 'auto';
  }

  setTheme(theme) {
    const actualTheme = theme === 'auto' 
      ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
      : theme;
    document.documentElement.setAttribute('data-bs-theme', actualTheme);
  }

  showActiveTheme(theme, focus = false) {
    const switcher = document.querySelector('.theme-switcher');
    if (!switcher) return;

    const activeIcon = document.querySelector('.theme-icon-active i');
    const activeBtn = document.querySelector(`[data-bs-theme-value="${theme}"]`);
    
    if (!activeBtn) return;

    const iconClass = activeBtn.querySelector('.theme-icon i').className;

    document.querySelectorAll('[data-bs-theme-value]').forEach(el => {
      el.classList.remove('active');
      el.setAttribute('aria-pressed', 'false');
    });

    activeBtn.classList.add('active');
    activeBtn.setAttribute('aria-pressed', 'true');
    
    if (activeIcon) activeIcon.className = iconClass;
    
    switcher.setAttribute('aria-label', `Toggle theme (${activeBtn.dataset.bsThemeValue})`);
    
    if (focus) switcher.focus();
  }

  bindEvents() {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
      const stored = this.getStoredTheme();
      if (stored !== 'light' && stored !== 'dark') {
        this.setTheme(this.getPreferredTheme());
      }
    });

    document.querySelectorAll('[data-bs-theme-value]').forEach(toggle => {
      toggle.addEventListener('click', () => {
        const theme = toggle.getAttribute('data-bs-theme-value');
        this.setStoredTheme(theme);
        this.setTheme(theme);
        this.showActiveTheme(theme, true);
      });
    });
  }
}


// Toast消息显示
class ToastManager {
  constructor() {
    this.isVisible = false;
  }

  show(message, type = 'success') {
    if (this.isVisible) return;

    let container = document.getElementById('toastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toastContainer';
      container.className = 'toast-container p-1 top-0 start-50 translate-middle-x';
      container.style.cssText = 'position: fixed; z-index: 9999;';
      document.body.appendChild(container);
    }

    const existing = document.getElementById('liveToast');
    if (existing) existing.remove();

    const toast = document.createElement('div');
    toast.id = 'liveToast';
    toast.className = `toast text-bg-${type} border-0 fade`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
      <div class="d-flex">
        <i class="ci-check-circle fs-base mt-1 me-2"></i>
        <div class="toast-body me-2">${message}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="toast"></button>
      </div>
    `;

    container.appendChild(toast);

    if (window.bootstrap) {
      const bsToast = new window.bootstrap.Toast(toast);
      bsToast.show();
      
      setTimeout(() => {
        bsToast.hide();
        this.isVisible = false;
      }, 5000);
    }
    
    this.isVisible = true;
  }
}

// 启动应用并导出
new MorphoApp();

// 全局导出
window.MorphoApp = MorphoApp;
window.MorphoUtils = utils;

// 全局Toast函数
const toastManager = new ToastManager();
window.showToast = (message, type) => toastManager.show(message, type);