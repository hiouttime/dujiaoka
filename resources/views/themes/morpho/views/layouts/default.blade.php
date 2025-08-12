<!DOCTYPE html>
<html lang="{{ str_replace('_','-',strtolower(app()->getLocale())) }}">
<head>
    @include('morpho::layouts._header')
</head>
<body>
    @include('morpho::layouts._nav')
    @yield('content')
    @include('morpho::layouts._footer')
    
    @include('morpho::layouts._script')
    @include('morpho::layouts._cart_dropdown')
    @section('js')
    @show
</body>
</html>

