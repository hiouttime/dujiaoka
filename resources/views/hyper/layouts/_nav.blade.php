<div class="header-navbar">
  <div class="container header-flex">
    <!-- LOGO -->
    <a href="/" class="topnav-logo" style="float: none;">
      <img src="{{ picture_ulr(dujiaoka_config_get('img_logo')) }}" height="36">
      <div class="logo-title">{{ dujiaoka_config_get('text_logo') }}</div></a>
    <div class="nav-link">
      <ul class="nav-list">
        <li class="nav-item">
          <a class="tab-link @if(\Illuminate\Support\Facades\Request::path() == '/') active @endif " href="/">
              {{__('dujiaoka.home_page')}}
        </a>
        <li class="nav-item">
          <a class="tab-link @if(\Illuminate\Support\Facades\Request::path() == 'article') active @endif" href="/article">{{__('dujiaoka.article_page')}}</a></li>
      </ul>
    </div>
    <a class="btn btn-outline-primary" href="{{ url('order-search') }}">
      <i class="noti-icon uil-file-search-alt search-icon"></i>查询订单</a>
  </div>
</div>