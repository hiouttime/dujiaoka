<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>{{ isset($page_title) ? $page_title : '' }} | {{ theme_config('site_name', shop_cfg('title')) }}</title>
    <style>
      html,
      body {
        touch-action: manipulation;
      }
    </style>

    <meta http-equiv="Cache-Control" content="max-age=3600" />
    <meta http-equiv="Expires" content="Sun, 17 Mar 2024 12:00:00 GMT" />
    <meta name="Keywords" content="{{ shop_cfg('keywords') }}">
    <meta name="Description" content="{{ shop_cfg('description')  }}">
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    
    {{-- 主题配置的图标 --}}
    @if(theme_config('favicon'))
        <link rel="icon" type="image/png" href="{{ theme_config('favicon') }}" />
    @else
        <link rel="icon" type="image/png" href="{{ theme_asset('app-icons/icon-32x32.png') }}" sizes="32x32" />
    @endif
    
    @if(\request()->getScheme() == "https")
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endif
    
    <link rel="apple-touch-icon" href="{{ theme_asset('app-icons/icon-180x180.png') }}" />
    <link rel="manifest" href="manifest.json" />
    
    {{-- 主题自定义Meta标签 --}}
    @if(theme_config('custom_meta'))
        {!! theme_config('custom_meta') !!}
    @endif
    
    {{-- Google Analytics --}}
    @if(theme_config('google_analytics'))
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ theme_config('google_analytics') }}"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', '{{ theme_config('google_analytics') }}');
        </script>
    @endif
    
    {{-- 字体和图标预加载 --}}
    <link rel="preload" href="{{ theme_asset('fonts/inter-variable-latin.woff2') }}" as="font" type="font/woff2" crossorigin />
    <link rel="preload" href="{{ theme_asset('icons/cartzilla-icons.woff2') }}" as="font" type="font/woff2" crossorigin />
    
    {{-- 样式表 --}}
    <link rel="stylesheet" href="{{ theme_asset('icons/cartzilla-icons.min.css') }}" />
    <link rel="stylesheet" href="{{ theme_asset('css/swiper-bundle.min.css') }}" />
    <link rel="stylesheet" href="{{ theme_asset('css/simplebar.min.css') }}" />
    <link rel="stylesheet" href="{{ theme_asset('css/style.css') }}" />
    <link rel="preload" href="{{ theme_asset('css/theme.min.css') }}" as="style" />
    <link rel="stylesheet" href="{{ theme_asset('css/theme.min.css') }}" id="theme-styles" />
</head>

