<script src="{{ theme_asset('js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ theme_asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ theme_asset('js/swiper-bundle.min.js') }}"></script>
<script src="{{ theme_asset('js/morpho.js') }}"></script>
<script src="{{ theme_asset('js/cart.js') }}"></script>
<script src="{{ theme_asset('js/scroll-to-top.js') }}"></script>

{{-- 页脚自定义代码 --}}
@if(!empty(app(\App\Settings\ShopSettings::class)->footer))
    {!! app(\App\Settings\ShopSettings::class)->footer !!}
@endif
