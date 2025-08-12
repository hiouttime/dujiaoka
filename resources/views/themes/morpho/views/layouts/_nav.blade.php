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
            @php
              $themeNotices = theme_cfg('notices', '');
              $notices = !empty($themeNotices) ? explode("\n", $themeNotices) : explode("\n", shop_cfg('notice', ''));
              $notices = array_filter($notices);
            @endphp

            @if(!empty($notices))
              @foreach($notices as $notice)
                @if(trim($notice))
                  <div class="swiper-slide text-truncate text-center">{{ trim($notice) }}</div>
                @endif
              @endforeach
            @endif
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
            src="{{ pictureUrl(shop_cfg('img_logo')) }}"
            class="d-flex d-none d-md-inline-flex justify-content-center align-items-center flex-shrink-0 me-1 {{ theme_cfg('invert_logo', false)?'invert_logo':'' }}"
            style="width: 2.5rem; height: 2.5rem"
          />
          {{ shop_cfg('text_logo') }}
        </a>

        <!-- Main navigation that turns into offcanvas on screens < 992px wide (lg breakpoint) -->
        <nav class="offcanvas offcanvas-start" id="navbarNav" tabindex="-1" aria-labelledby="navbarNavLabel">
          <div class="offcanvas-header py-3">
            <h5 class="offcanvas-title" id="navbarNavLabel">导航菜单</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
          </div>
          <div class="offcanvas-body pt-3 pb-4 py-lg-0 mx-lg-auto">
            <ul class="navbar-nav position-relative">
              @php
                $navItems = shop_cfg('nav_items', []);
              @endphp
              
              @if(!empty($navItems))
                @foreach($navItems as $item)
                  @if(!empty($item['children']))
                    <li class="nav-item dropdown me-lg-n1 me-xl-0">
                      <a class="nav-link dropdown-toggle fs-sm" role="button" data-bs-toggle="dropdown" data-bs-trigger="hover" data-bs-auto-close="outside" aria-expanded="false">{{ $item['name'] }}</a>
                      <ul class="dropdown-menu" style="--cz-dropdown-spacer: 1rem">
                        @foreach($item['children'] as $child)
                          <li>
                            <a class="dropdown-item" href="{{ $child['url'] }}" {{ isset($child['target_blank']) && $child['target_blank'] ? 'target="_blank"' : '' }}>{{ $child['name'] }}</a>
                          </li>
                        @endforeach
                      </ul>
                    </li>
                  @else
                    <li class="nav-item me-lg-n2 me-xl-0">
                        <a class="nav-link fs-sm @if(\Illuminate\Support\Facades\Request::url() == url($item['url'])) active @endif" 
                           href="{{ $item['url'] }}" 
                           {{ isset($item['target_blank']) && $item['target_blank'] ? 'target="_blank"' : '' }}>
                          {{ $item['name'] }}
                        </a>
                    </li>
                  @endif
                @endforeach
              @endif
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

          <!-- Cart -->
          <div class="dropdown position-relative">
            <a class="btn btn-icon fs-lg btn-outline-secondary border-0 rounded-circle animate-scale cart-icon position-relative" 
               href="/cart">
              <i class="ci-shopping-cart animate-target"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end cart-dropdown" style="--cz-dropdown-min-width: 320px; --cz-dropdown-spacer: 1rem">
              <div class="p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6 class="mb-0">购物车</h6>
                  <span class="text-muted small" id="cartDropdownCount">0 件商品</span>
                </div>
                
                <div id="cartDropdownItems">
                  <div class="text-center text-muted py-4" id="cartDropdownEmpty">
                    <i class="ci-shopping-cart fs-2 mb-2 d-block"></i>
                    <small>购物车是空的</small>
                  </div>
                </div>
                
                <div id="cartDropdownFooter" style="display: none;">
                  <hr class="my-3">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-medium">总计:</span>
                    <span class="fw-bold" id="cartDropdownTotal">$0.00</span>
                  </div>
                  <div class="d-grid gap-2">
                    <a href="/cart" class="btn btn-dark btn-sm">查看购物车</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
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
