<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>{{ isset($page_title) ? $page_title : '' }} | {{ dujiaoka_config_get('title') }}</title>
    <style>
      html,
      body {
        touch-action: manipulation;
      }
    </style>

    <meta http-equiv="Cache-Control" content="max-age=3600" />
    <meta http-equiv="Expires" content="Sun, 17 Mar 2024 12:00:00 GMT" />
    <meta name="Keywords" content="{{ dujiaoka_config_get('keywords') }}">
    <meta name="Description" content="{{ dujiaoka_config_get('description')  }}">
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <link rel="manifest" href="manifest.json" />
    <link rel="icon" type="image/png" href="/assets/riniba_03/app-icons/icon-32x32.png" sizes="32x32" />
    @if(\request()->getScheme() == "https")
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endif
    <link rel="apple-touch-icon" href="/assets/riniba_03/app-icons/icon-180x180.png" />
    <script src="/assets/riniba_03/js/theme-switcher.js"></script>
    <link rel="preload" href="/assets/riniba_03/fonts/inter-variable-latin.woff2" as="font" type="font/woff2" crossorigin />
    <link rel="preload" href="/assets/riniba_03/icons/cartzilla-icons.woff2" as="font" type="font/woff2" crossorigin />
    <link rel="stylesheet" href="/assets/riniba_03/icons/cartzilla-icons.min.css" />
    <link rel="stylesheet" href="/assets/riniba_03/css/swiper-bundle.min.css" />
    <link rel="stylesheet" href="/assets/riniba_03/css/glightbox.min.css" />
    <link rel="stylesheet" href="/assets/riniba_03/css/simplebar.min.css" />
    <link rel="preload" href="/assets/riniba_03/css/theme.min.css" as="style" />
    <link rel="stylesheet" href="/assets/riniba_03/css/theme.min.css" id="theme-styles" />
</head>

