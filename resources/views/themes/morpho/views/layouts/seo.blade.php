<!DOCTYPE html>
<html lang="{{ str_replace('_','-',strtolower(app()->getLocale())) }}">
<head>
    @include('morpho::layouts._header')
    @if(isset($gd_keywords))
        <meta name="keywords" content="{{ $gd_keywords }}">
        <meta name="description" content="{{ $gd_description }}">
        <meta property="og:type" content="article">
        <meta property="og:image" content="{{ $picture }}">
        <meta property="og:title" content="{{ isset($page_title) ? $page_title : '' }}">
        <meta property="og:description" content="{{ $gd_description }}">    
        <meta property="og:release_date" content="{{ $updated_at }}">
    @endif
</head>
<body>
    @include('morpho::layouts._nav')
    @yield('content')
    @include('morpho::layouts._footer')
    
    @include('morpho::layouts._script')
    @section('js')
    @show
</body>
</html>
