<!DOCTYPE html>
<html lang="{{ str_replace('_','-',strtolower(app()->getLocale())) }}">
<body>
@include('neon.layouts._header')
@include('neon.layouts._nav')
@yield('content')
@include('neon.layouts._footer')
</body>
@include('neon.layouts._script')
@section('js')
@show
</html>

