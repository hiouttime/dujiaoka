<div class="col product-item">
    <div class="product-card animate-underline hover-effect-opacity bg-body rounded">
        <div class="position-relative mb-3">

            {{-- 1) 判断自动还是人工 --}}
            @if ($goods['type'] == \App\Models\Goods::AUTOMATIC_DELIVERY)
                <span class="badge text-bg-success position-absolute top-0 start-0 z-2 mt-2 mt-sm-3 ms-2 ms-sm-3">自动</span>
            @else
                <span class="badge text-bg-info position-absolute top-0 start-0 z-2 mt-2 mt-sm-3 ms-2 ms-sm-3">人工</span>
            @endif

            @php
                $stock = $goods['type'] == 1 
                    ? collect($goods['goods_sub'])->sum(fn($sub) => \App\Models\Carmis::where('sub_id', $sub['id'])->where('status', 1)->count())
                    : collect($goods['goods_sub'])->sum('stock');
            @endphp

            @if($stock <= 0)
                <span class="badge text-bg-danger position-absolute top-0 start-0 z-2 mt-2 mt-sm-3 ms-2 ms-sm-3" style="margin-left: 56px;">缺货</span>
            @endif

            <a class="d-flex bg-body-tertiary rounded p-3"
               href="{{ $stock > 0 ? url("/buy/{$goods['id']}") : 'javascript:void(0);' }}"
               @if($stock <= 0) onclick="showToast('商品库存不足, 请联系客服补货.');" @endif>
                <div class="ratio" style="--cz-aspect-ratio: calc(308 / 274 * 100%)">
                    <img src="{{ pictureUrl($goods['picture']) }}" alt="{{ $goods['gd_name'] }}">
                </div>
            </a>
        </div>

        <div class="nav mb-1">
            <a class="nav-link animate-target min-w-0 text-dark-emphasis p-0"
               href="{{ $stock > 0 ? url("/buy/{$goods['id']}") : 'javascript:void(0);' }}"
               @if($stock <= 0) onclick="showToast('商品库存不足, 请联系客服补货.');" @endif>
                <span class="text-truncate">{{ $goods['gd_name'] }}</span>
            </a>
        </div>

        <div class="h6 mb-1">${{ number_format(collect($goods['goods_sub'])->min('price'), 2) }}</div>

        @php
            $maxStock = 50;
            $percent = $stock > 0 ? min(100, round(($stock / $maxStock) * 100, 2)) : 0;
        @endphp
        <div class="progress mb-2" role="progressbar" aria-label="Available in stock"
             aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100" style="height: 4px">
            <div class="progress-bar bg-dark rounded-pill d-none-dark" style="width: {{ $percent }}%"></div>
            <div class="progress-bar bg-light rounded-pill d-none d-block-dark" style="width: {{ $percent }}%"></div>
        </div>
    </div>
</div>
