<!DOCTYPE html>
<html lang="{{ str_replace('_','-',strtolower(app()->getLocale())) }}">
<head>
    @include('layouts._header')
    @themeStyle
</head>
<body class="{{ theme_config('header_style', 'default') }}-header {{ theme_config('animation_enabled', true) ? 'animate-enabled' : '' }}">
    @include('layouts._nav')
    @yield('content')
    @include('layouts._footer')
    
    @include('layouts._script')
    @themeScript
    @section('js')
    @show
</body>
</html>

