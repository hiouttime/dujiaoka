@php
    $stock = $goods['type'] == 1 
        ? collect($goods['goods_sub'])->sum(fn($sub) => \App\Models\Carmis::where('sub_id', $sub['id'])->where('status', 1)->count())
        : collect($goods['goods_sub'])->sum('stock');
    $isAvailable = $stock > 0;
    $buyUrl = $isAvailable ? url("/buy/{$goods['id']}") : 'javascript:void(0);';
    $typeColors = [1 => 'success', 2 => 'info', 3 => 'primary'];
    $typeNames = [1 => '自动发货', 2 => '人工处理', 3 => '自动处理'];
@endphp

<div class="col">
    <div class="card h-100 border rounded" style="transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;"
         onmouseover="this.style.transform='translateY(-4px)'; this.style.borderColor='#6c757d'; this.style.boxShadow='0 0.5rem 1rem rgba(0,0,0,0.15)';"
         onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#dee2e6'; this.style.boxShadow='none';">
        
        <div class="position-relative bg-light rounded-top" style="aspect-ratio: 1;">
            <a href="{{ $buyUrl }}" class="d-block h-100 p-3 d-flex align-items-center justify-content-center"
               @if(!$isAvailable) onclick="showToast('商品库存不足, 请联系客服补货.');" @endif>
                <img src="{{ pictureUrl($goods['picture']) }}" alt="{{ $goods['gd_name'] }}" 
                     class="img-fluid" style="object-fit: contain;">
            </a>
            
            <div class="position-absolute top-0 start-0 m-2 d-flex flex-wrap gap-1">
                <span class="badge bg-{{ $typeColors[$goods['type']] ?? 'secondary' }} fw-normal">
                    {{ $typeNames[$goods['type']] ?? '未知类型' }}
                </span>
                @if(!$isAvailable)
                    <span class="badge bg-danger fw-normal">缺货</span>
                @endif
            </div>
        </div>

        <div class="card-body text-center">
            <a href="{{ $buyUrl }}" class="text-decoration-none text-dark"
               @if(!$isAvailable) onclick="showToast('商品库存不足, 请联系客服补货.');" @endif>
                <h6 class="card-title mb-2 lh-sm" style="display: -webkit-box; -webkit-line-clamp: 2; 
                    -webkit-box-orient: vertical; overflow: hidden; height: 2.4em;">
                    {{ $goods['gd_name'] }}
                </h6>
            </a>
            
            <div class="text-muted small mb-2">
                <i class="fas fa-fire text-danger"></i> 销量: {{ $goods['sales_volume'] ?? 0 }}
            </div>

            <div class="fw-bold text-primary">
                ${{ number_format(collect($goods['goods_sub'])->min('price'), 2) }}
            </div>
        </div>
    </div>
</div>
