@extends('morpho::layouts.default')
@section('content')
<main class="content-wrapper">
    <section class="container pt-4">
        <div class="row">
            <div class="w-100">
                @php
                    $banners = theme_cfg('banners', []);
                    $notice = shop_cfg('notice', '');
                    
                    $slides = [];
                    $slides[] = [
                        'type' => 'notice',
                        'title' => '站点公告',
                        'content' => $notice ?: '暂无公告内容'
                    ];
                    foreach($banners as $banner) {
                        $slides[] = [
                            'type' => 'banner',
                            'title' => $banner['title'] ?? '',
                            'subtitle' => $banner['subtitle'] ?? '',
                            'image' => $banner['image'] ?? '',
                            'button_text' => $banner['button_text'] ?? '',
                            'button_url' => $banner['button_url'] ?? '',
                            'target_blank' => $banner['target_blank'] ?? false
                        ];
                    }
                @endphp

                <div class="position-relative">
                    <span class="position-absolute top-0 start-0 w-100 h-100 rounded-5 d-none-dark rtl-flip"
                        style="background: linear-gradient(90deg, #accbee 0%, #e7f0fd 100%)"></span>
                    <span class="position-absolute top-0 start-0 w-100 h-100 rounded-5 d-none d-block-dark rtl-flip"
                        style="background: linear-gradient(90deg, #1b273a 0%, #1f2632 100%)"></span>
                    
                    <!-- 导航箭头 -->
                    <i class="ci-chevron-left position-absolute text-white" id="heroPrev" 
                       style="left: 2rem; top: 50%; transform: translateY(-50%); font-size: 3rem; cursor: pointer; z-index: 100; opacity: 0.7;"></i>
                    <i class="ci-chevron-right position-absolute text-white" id="heroNext" 
                       style="right: 2rem; top: 50%; transform: translateY(-50%); font-size: 3rem; cursor: pointer; z-index: 100; opacity: 0.7;"></i>
                    
                    <!-- 轮播内容 -->
                    <div class="swiper position-relative z-2" id="heroSlider">
                        <div class="swiper-wrapper">
                            @foreach($slides as $slide)
                            <div class="swiper-slide">
                                <div class="row justify-content-center" style="min-height: 45vh;">
                                    <div class="col-xxl-10">
                                        <div class="row align-items-center h-100">
                                            @if($slide['type'] === 'notice')
                                                <div class="col-12 text-start">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <i class="ci-bell fs-4 text-muted me-2"></i>
                                                        <span class="text-muted fw-medium">公告</span>
                                                    </div>
                                                    <div class="notice-content text-body fs-6 lh-lg pe-2" 
                                                         style="height: 30vh; overflow-y: auto;">
                                                        {!! $slide['content'] !!}
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-lg-6">
                                                    <div class="text-center text-lg-start">
                                                        @if($slide['subtitle'])
                                                            <p class="text-muted mb-2">{{ $slide['subtitle'] }}</p>
                                                        @endif
                                                        @if($slide['title'])
                                                            <h2 class="display-5 fw-bold mb-4">{{ $slide['title'] }}</h2>
                                                        @endif
                                                        @if($slide['button_text'] && $slide['button_url'])
                                                            <a href="{{ $slide['button_url'] }}" 
                                                               class="btn btn-dark btn-lg"
                                                               {{ $slide['target_blank'] ? 'target="_blank"' : '' }}>
                                                                {{ $slide['button_text'] }}
                                                                <i class="ci-arrow-up-right ms-2"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="text-center">
                                                        @if($slide['image'])
                                                            <img src="{{ asset('storage/' . $slide['image']) }}" 
                                                                 alt="{{ $slide['title'] }}" 
                                                                 class="img-fluid rounded-4"
                                                                 style="max-width: 400px;">
                                                        @else
                                                            <div class="bg-light rounded-4 p-5 border" style="max-width: 400px; margin: 0 auto;">
                                                                <i class="ci-image display-1 text-muted mb-3"></i>
                                                                <p class="text-muted">暂无图片</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // 初始化轮播
                    new Swiper('#heroSlider', {
                        loop: true,
                        autoplay: {
                            delay: 5000,
                            disableOnInteraction: false,
                            pauseOnMouseEnter: true
                        },
                        navigation: {
                            nextEl: '#heroNext',
                            prevEl: '#heroPrev'
                        },
                        speed: 600
                    });
                    
                    // 为溢出的公告内容添加虚线提示
                    document.querySelectorAll('.notice-content').forEach(el => {
                        if (el.scrollHeight > el.clientHeight) {
                            el.style.borderBottom = '2px dashed #999';
                        }
                    });
                });
                </script>
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
            <div class="tab-pane fade show active" id="group-all" role="tabpanel" aria-labelledby="group-all-tab">
                <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-4 row-cols-xl-4 row-cols-xxl-5 g-3 pt-4">
                    @foreach($data as $group)
                        @foreach($group['goods'] as $goods)
                        @include('morpho::layouts._goods', ['goods' => $goods])
                        @endforeach
                    @endforeach
                </div>
            </div>
        
            @foreach($data as $group)
                <div class="tab-pane fade" id="group-{{ $group['id'] }}" role="tabpanel" aria-labelledby="group-{{ $group['id'] }}-tab">
                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-4 row-cols-xl-4 row-cols-xxl-5 g-3 pt-4">
                        @foreach($group['goods'] as $goods)
                        @include('morpho::layouts._goods', ['goods' => $goods])                        
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