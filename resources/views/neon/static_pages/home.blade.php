@extends('riniba_03.layouts.default')
@section('content')
<main class="content-wrapper">
    <section class="container pt-4">
        <div class="row">
            <div class="w-100">
                <div class="position-relative">
                    <span class="position-absolute top-0 start-0 w-100 h-100 rounded-5 d-none-dark rtl-flip"
                        style="background: linear-gradient(90deg, #accbee 0%, #e7f0fd 100%)"></span>
                    <span class="position-absolute top-0 start-0 w-100 h-100 rounded-5 d-none d-block-dark rtl-flip"
                        style="background: linear-gradient(90deg, #1b273a 0%, #1f2632 100%)"></span>
                    <div class="row justify-content-center position-relative z-2">
                        <div class="col-xl-5 col-xxl-5  d-flex align-items-center mt-xl-n3">

                            <!-- Text content master slider -->
                            <div class="swiper px-5 pe-xl-0 ps-xxl-0 me-xl-n5" data-swiper='{
                    "spaceBetween": 64,
                    "loop": true,
                    "speed": 400,
                    "controlSlider": "#sliderImages",
                    "autoplay": {
                      "delay": 5500,
                      "disableOnInteraction": false
                    },
                    "scrollbar": {
                      "el": ".swiper-scrollbar"
                    }
                  }'>
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide text-center text-xl-start pt-5 py-xl-5">
                                        <p class="text-body">近期热销产品</p>
                                        <h2 class="display-4 pb-2 pb-xl-4">美区礼品卡50美刀</h2>
                                        <a class="btn btn-lg btn-dark" href="buy/1">
                                            立即 购买
                                            <i class="ci-arrow-up-right fs-lg ms-2 me-n1"></i>
                                        </a>
                                    </div>
                                    <div class="swiper-slide text-center text-xl-start pt-5 py-xl-5">
                                        <p class="text-body">Telegram社区数万人社区</p>
                                        <h2 class="display-4 pb-2 pb-xl-4">专业售后服务</h2>
                                        <a class="btn btn-lg btn-dark" href="hhttps://t.me/RinibaGroup">
                                            加入 社区
                                            <i class="ci-arrow-up-right fs-lg ms-2 me-n1"></i>
                                        </a>
                                    </div>
                                    <div class="swiper-slide text-center text-xl-start pt-5 py-xl-5">
                                        <p class="text-body">发货时间少于两分钟</p>
                                        <h2 class="display-4 pb-2 pb-xl-4">及时交货</h2>
                                        <a class="btn btn-lg btn-dark rounded-pill"
                                            href="https://youtu.be/me_Dc5PJrXk?si=QSILRHdbjUIJ7SmV" data-glightbox
                                            data-gallery="video">
                                            <i class="ci-play fs-lg ms-n1 me-2"></i>
                                            播放 视频
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-9 col-sm-7 col-md-6 col-lg-5 col-xl-5">
                            <!-- Binded images (controlled slider) -->
                            <div class="swiper user-select-none" id="sliderImages" data-swiper='{
                    "allowTouchMove": false,
                    "loop": true,
                    "effect": "fade",
                    "fadeEffect": {
                      "crossFade": true
                    }
                  }'>
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide d-flex justify-content-end">
                                        <div class="ratio rtl-flip"
                                            style="max-width: 400px; --cz-aspect-ratio: calc(537 / 495 * 100%)">
                                            <img src="/assets/riniba_03/banner/1.webp" alt="Image">
                                        </div>
                                    </div>
                                    <div class="swiper-slide d-flex justify-content-end">
                                        <div class="ratio rtl-flip"
                                            style="max-width: 400px; --cz-aspect-ratio: calc(537 / 495 * 100%)">
                                            <img src="/assets/riniba_03/banner/2.webp" alt="Image">
                                        </div>
                                    </div>
                                    <div class="swiper-slide d-flex justify-content-end">
                                        <div class="ratio rtl-flip"
                                            style="max-width: 400px; --cz-aspect-ratio: calc(537 / 495 * 100%)">
                                            <img src="/assets/riniba_03/banner/3.webp" alt="Image">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Scrollbar -->
                    <div class="row justify-content-center" data-bs-theme="dark">
                        <div class="col-xxl-10">
                            <div class="position-relative mx-5 mx-xxl-0">
                                <div class="swiper-scrollbar mb-4"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="container pt-3 mt-2 mt-sm-3 mt-lg-4 mt-xl-2">
        <div class="row g-0 overflow-x-auto pb-2 pb-sm-3 mb-3">
            <div class="col-auto pb-1 pb-sm-0 mx-auto">
                <ul class="nav nav-pills  justify-content-center">
                    <li class="nav-item">
                        <a href="#group-all" data-bs-toggle="tab" class="btn btn-outline-secondary active">{{
                            __('dujiaoka.group_all') }}</a>
                    </li>
                    @foreach($data as $index => $group)
                    <li class="nav-item">
                        <a href="#group-{{ $group['id'] }}" data-bs-toggle="tab" class="btn btn-outline-secondary">{{
                            $group['gp_name'] }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <!-- 搜索 -->        
        <div class="d-flex justify-content-center mb-4">
            <div class="position-relative col-12 col-md-6">
                <i class="ci-search position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                <input type="search" class="quicksearch form-control form-icon-start" placeholder="搜索您的商品...">
                <button
                    class="btn btn-sm btn-outline-secondary w-auto border-0 p-1 position-absolute top-50 end-0 translate-middle-y me-2 clear-btn">
                    <svg class="opacity-75" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M18.619 5.381a.875.875 0 0 1 0 1.238l-12 12A.875.875 0 0 1 5.38 17.38l12-12a.875.875 0 0 1 1.238 0Z">
                        </path>
                        <path
                            d="M5.381 5.381a.875.875 0 0 1 1.238 0l12 12a.875.875 0 1 1-1.238 1.238l-12-12a.875.875 0 0 1 0-1.238Z">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="tab-content" id="goodsTabContent">

            <!-- (A) “全部” 面板：展示所有分组下的商品 -->
            <div class="tab-pane fade show active" id="group-all" role="tabpanel" aria-labelledby="group-all-tab">
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 row-cols-xxl-6 g-4 pt-4">
                    @foreach($data as $group)
                        @foreach($group['goods'] as $goods)
                        @include('riniba_03.layouts._goods', ['goods' => $goods])
                        @endforeach
                    @endforeach
                </div>
            </div>
        
            <!-- (B) 各分组面板：每个分组只展示自己商品 -->
            @foreach($data as $group)
                <div class="tab-pane fade" id="group-{{ $group['id'] }}" role="tabpanel" aria-labelledby="group-{{ $group['id'] }}-tab">
                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 row-cols-xxl-6 g-4 pt-4">
                        @foreach($group['goods'] as $goods)
                        @include('riniba_03.layouts._goods', ['goods' => $goods])                        
                        @endforeach
                    </div>
                </div>
            @endforeach
        
        </div>
    </section>
</main>


@stop

@section('js')
<script>
    $(document).ready(function() {
    
      // 1) 拿到搜索框和清除按钮
      const $searchInput = $(".quicksearch");
      const $clearBtn = $(".clear-btn");
    
      // 2) 封装一个执行搜索的函数
      function doSearch() {
        let searchContent = $.trim($searchInput.val());
        if (searchContent !== "") {
          // 不区分大小写，所以统一 toLowerCase
          searchContent = searchContent.toLowerCase();
    
          // 先隐藏所有 .col（或你的商品父容器 class）
          $(".col").hide()
            // 再 filter 出匹配项
            .filter(function() {
              // $(this).text() 把该元素内所有文本拿来做对比
              // 也可以只对指定子元素做 .text()，如 $(this).find('.card-title').text()
              return $(this).text().toLowerCase().indexOf(searchContent) !== -1;
            })
            // 显示匹配到的
            .show();
    
          // 显示清除按钮
          $clearBtn.css("opacity", 1);
        } else {
          // 如果搜索框空了，就恢复全部
          $(".col").show();
          // 隐藏清除按钮
          $clearBtn.css("opacity", 0);
        }
      }
    
      // 3) 输入框监听输入事件 => 实时搜索
      $searchInput.on("input", doSearch);
    
      // 4) 点击清除按钮 => 清空输入框并恢复
      $clearBtn.on("click", function() {
        $searchInput.val("");
        doSearch();
      });
    
    });
    </script>
    @stop