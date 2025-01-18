    <!-- Topbar -->
    <div class="alert alert-dismissible rounded-0 py-3 px-0 m-0 fade show">
      <div class="container position-relative d-flex min-w-0">
        <div class="d-flex flex-nowrap align-items-center g-2 w-100 min-w-0 mx-auto mt-n1" style="max-width: 458px">
          <div class="nav me-2">
            <button type="button" class="nav-link fs-lg p-0" id="topbarPrev" aria-label="Prev">
              <i class="ci-chevron-left"></i>
            </button>
          </div>
          <div
            class="swiper fs-sm"
            data-swiper='{
            "spaceBetween": 24,
            "loop": true,
            "autoplay": {
              "delay": 5000,
              "disableOnInteraction": false
            },
            "navigation": {
              "prevEl": "#topbarPrev",
              "nextEl": "#topbarNext"
            }
          }'
          >
            <div class="swiper-wrapper min-w-0">
              <div class="swiper-slide text-truncate text-center">本站商品仅用于测试,禁止用于任何非法行为</div>
              <div class="swiper-slide text-truncate text-center">所有商品均为空白身份注册,不包含公民隐私信息</div>
              <div class="swiper-slide text-truncate text-center">虚拟商品具有可复制可传播性,有问题请联系客服</div>
            </div>
          </div>
          <div class="nav ms-2">
            <button type="button" class="nav-link fs-lg p-0" id="topbarNext" aria-label="Next">
              <i class="ci-chevron-right"></i>
            </button>
          </div>
        </div>
        <button type="button" class="btn-close position-static flex-shrink-0 p-1 ms-3 ms-md-n4" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    </div>

    <!-- Navigation bar (Page header) -->
    <header class="navbar-sticky sticky-top container z-fixed px-2" data-sticky-element>
      <div class="navbar navbar-expand-lg flex-nowrap bg-body rounded-pill shadow ps-0 mx-1">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark rounded-pill z-0 d-none d-block-dark"></div>

        <!-- Mobile offcanvas menu toggler (Hamburger) -->
        <button type="button" class="navbar-toggler ms-3" data-bs-toggle="offcanvas" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar brand (Logo) -->
        <a class="navbar-brand position-relative z-1 ms-4 ms-sm-5 ms-lg-4 me-2 me-sm-0 me-lg-3" href="/">
          <img
            src="{{ picture_ulr(dujiaoka_config_get('img_logo')) }}"
            class="d-flex d-none d-md-inline-flex justify-content-center align-items-center flex-shrink-0 bg-body-tertiary rounded-circle me-1"
            style="width: 2.5rem; height: 2.5rem"
          />
          {{ dujiaoka_config_get('text_logo') }}
        </a>

        <!-- Main navigation that turns into offcanvas on screens < 992px wide (lg breakpoint) -->
        <nav class="offcanvas offcanvas-start" id="navbarNav" tabindex="-1" aria-labelledby="navbarNavLabel">
          <div class="offcanvas-header py-3">
            <h5 class="offcanvas-title" id="navbarNavLabel">导航菜单</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body pt-3 pb-4 py-lg-0 mx-lg-auto">
            <ul class="navbar-nav position-relative">
              <li class="nav-item me-lg-n1 me-xl-0">
                <a class="nav-link fs-sm @if(\Illuminate\Support\Facades\Request::url() == url('/')) active @endif" href="/">主页</a>
              </li>
              <li class="nav-item dropdown me-lg-n1 me-xl-0">
                <a class="nav-link dropdown-toggle fs-sm" role="button" data-bs-toggle="dropdown" data-bs-trigger="hover" data-bs-auto-close="outside" aria-expanded="false">联系客服</a>
                <ul class="dropdown-menu" style="--cz-dropdown-spacer: 1rem">
                  <li><a class="dropdown-item" href="https://t.me/riniba" target="_blank">站点客服</a></li>
                  <li><a class="dropdown-item" href="https://t.me/riniba" target="_blank">Telegram客服</a></li>
                </ul>
              </li>
              <li class="nav-item me-lg-n2 me-xl-0">
                <a class="nav-link fs-sm" href="#modalId" style="font-size: 0.625rem; letter-spacing: 0.05rem" data-bs-toggle="modal" data-bs-target="#modalId">站点公告</a>
              </li>
              <li class="nav-item me-lg-n2 me-xl-0">
                <a class="nav-link fs-sm @if(\Illuminate\Support\Facades\Request::url() == url('order-search')) active @endif" href="/order-search">订单查询</a>
              </li>
            </ul>
          </div>
        </nav>

        <!-- Button group -->
        <div class="d-flex gap-sm-1 position-relative z-1">
          <!-- Theme switcher (light/dark/auto) -->
          <div class="dropdown">
            <button
              type="button"
              class="theme-switcher btn btn-icon btn-outline-secondary fs-lg border-0 rounded-circle animate-scale"
              data-bs-toggle="dropdown"
              data-bs-display="dynamic"
              aria-expanded="false"
              aria-label="Toggle theme (light)"
            >
              <span class="theme-icon-active d-flex animate-target">
                <i class="ci-sun"></i>
              </span>
            </button>
            <ul class="dropdown-menu start-50 translate-middle-x" style="--cz-dropdown-min-width: 9rem; --cz-dropdown-spacer: 1rem">
              <li>
                <button type="button" class="dropdown-item active" data-bs-theme-value="light" aria-pressed="true">
                  <span class="theme-icon d-flex fs-base me-2">
                    <i class="ci-sun"></i>
                  </span>
                  <span class="theme-label">亮色</span>
                  <i class="item-active-indicator ci-check ms-auto"></i>
                </button>
              </li>
              <li>
                <button type="button" class="dropdown-item" data-bs-theme-value="dark" aria-pressed="false">
                  <span class="theme-icon d-flex fs-base me-2">
                    <i class="ci-moon"></i>
                  </span>
                  <span class="theme-label">暗色</span>
                  <i class="item-active-indicator ci-check ms-auto"></i>
                </button>
              </li>
              <li>
                <button type="button" class="dropdown-item" data-bs-theme-value="auto" aria-pressed="false">
                  <span class="theme-icon d-flex fs-base me-2">
                    <i class="ci-auto"></i>
                  </span>
                  <span class="theme-label">自动</span>
                  <i class="item-active-indicator ci-check ms-auto"></i>
                </button>
              </li>
            </ul>
          </div>

          <!-- Search -->
          <a class="btn btn-icon fs-lg btn-outline-secondary border-0 rounded-circle animate-scale me-2" href="/order-search">
            <i class="ci-search animate-target"></i>
          </a>
        </div>
      </div>
    </header>

        <!-- 置顶按钮 -->
        <div class="floating-buttons position-fixed top-50 end-0 z-sticky me-3 me-xl-4 pb-4">
      <a class="btn-scroll-top btn btn-sm bg-body border-0 rounded-pill shadow animate-slide-end" href="#top">
        <span style="display: inline-block; transform: rotate(90deg)">顶</span>
        <span style="display: inline-block; transform: rotate(90deg)">置</span>
        <i class="ci-arrow-right fs-base ms-1 me-n1 animate-target"></i>
        <span class="position-absolute top-0 start-0 w-100 h-100 border rounded-pill z-0"></span>
        <svg class="position-absolute top-0 start-0 w-100 h-100 z-1" viewBox="0 0 62 32" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x=".75" y=".75" width="60.5" height="30.5" rx="15.25" stroke="currentColor" stroke-width="1.5" stroke-miterlimit="10" style="stroke-dasharray: 155.201; stroke-dashoffset: 0"></rect>
        </svg>
      </a>
    </div>
    <!-- 公告 -->
    <div class="modal fade" id="modalId" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">公告</h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body bg-body-tertiary fs-sm">
            <div class="py-2 px-2 px-md-2">
            <h4 class="">{{ __('dujiaoka.site_announcement') }}：</h4>
                                    
            <p class="lead">{!! dujiaoka_config_get('notice') !!}</p>
            </div>
          </div>
          <div class="modal-footer flex-column flex-sm-row align-items-stretch">
            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">关闭</button>
            <button class="btn btn-dark" type="button" data-bs-dismiss="modal">确定</button>
          </div>
        </div>
      </div>
    </div>
