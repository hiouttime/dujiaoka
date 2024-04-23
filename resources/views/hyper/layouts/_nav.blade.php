<div class="header-navbar">
    <div class="container header-flex">
        <!-- LOGO -->
        <a href="/" class="topnav-logo" style="float: none;">
            <img src="{{ picture_ulr(dujiaoka_config_get('img_logo')) }}" height="36">
            <div class="logo-title">{{ dujiaoka_config_get('text_logo') }}</div>
        </a>
        <div>
            <a class="btn" href="{{ url('article') }}">
                <i class="noti-icon uil-file-search-alt dripicons-article"></i>
                相关文章
            </a>
            <a class="btn btn-outline-primary" href="{{ url('order-search') }}">
                <i class="noti-icon uil-file-search-alt search-icon"></i>
                查询订单
            </a>
        </div>
    </div>
</div>