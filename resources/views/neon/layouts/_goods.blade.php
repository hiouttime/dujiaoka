<div class="col product-item">
    <div class="product-card animate-underline hover-effect-opacity bg-body rounded">
        <div class="position-relative mb-3">

            {{-- 1) 判断自动还是人工 --}}
            @if ($goods['type'] == \App\Models\Goods::AUTOMATIC_DELIVERY)
                <span class="badge text-bg-success position-absolute top-0 start-0 z-2 mt-2 mt-sm-3 ms-2 ms-sm-3">自动</span>
            @else
                <span class="badge text-bg-info position-absolute top-0 start-0 z-2 mt-2 mt-sm-3 ms-2 ms-sm-3">人工</span>
            @endif

            {{-- 如果缺货 (in_stock <= 0)，再显示一个“缺货” --}}
            @if($goods['in_stock'] <= 0)
                <span class="badge text-bg-danger position-absolute top-0 start-0 z-2 mt-2 mt-sm-3 ms-2 ms-sm-3" style="margin-left: 56px;">
                    缺货
                </span>
            @endif

            {{-- 2) 商品图片/封面链接 --}}
            <a class="d-flex bg-body-tertiary rounded p-3"
               href="@if($goods['in_stock'] > 0) {{ url("/buy/{$goods['id']}") }} @else javascript:void(0); @endif"
               @if($goods['in_stock'] <= 0)
                  onclick="showToast('商品库存不足, 请联系客服补货.');"
               @endif
            >
                <div class="ratio" style="--cz-aspect-ratio: calc(308 / 274 * 100%)">
                    <img src="{{ picture_ulr($goods['picture']) }}" alt="{{ $goods['gd_name'] }}">
                </div>
            </a>
        </div>

        {{-- 3) 商品标题 --}}
        <div class="nav mb-1">
            <a class="nav-link animate-target min-w-0 text-dark-emphasis p-0"
               href="@if($goods['in_stock'] > 0) {{ url("/buy/{$goods['id']}") }} @else javascript:void(0); @endif"
               @if($goods['in_stock'] <= 0)
                  onclick="showToast('商品库存不足, 请联系客服补货.');"
               @endif
            >
                <span class="text-truncate">
                    {{ $goods['gd_name'] }}
                </span>
            </a>
        </div>

        {{-- 4) 售价 --}}
        <div class="h6 mb-1">
            ${{ number_format($goods['sell_price'], 2) }}
        </div>

        {{-- 5) 进度条（根据库存 in_stock）示例: 50 视为满库存 --}}
        @php
            $maxStock = 50;
            $inStock = (int) $goods['in_stock'];
            $percent = 0;
            if($inStock > 0){
                $percent = ($inStock >= $maxStock) ? 100 : round(($inStock / $maxStock) * 100, 2);
            }
        @endphp
        <div class="progress mb-2" role="progressbar" aria-label="Available in stock"
             aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100" style="height: 4px">
            <div class="progress-bar bg-dark rounded-pill d-none-dark" style="width: {{ $percent }}%"></div>
            <div class="progress-bar bg-light rounded-pill d-none d-block-dark" style="width: {{ $percent }}%"></div>
        </div>
    </div>
</div>
