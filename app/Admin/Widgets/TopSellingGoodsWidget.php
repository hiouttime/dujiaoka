<?php

namespace App\Admin\Widgets;

use App\Models\Goods;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopSellingGoodsWidget extends ChartWidget
{
    protected static ?string $heading = '热销商品排行';
    
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];
    
    public ?string $filter = '30days';
    
    protected function getFilters(): ?array
    {
        return [
            '7days' => '最近7天',
            '30days' => '最近30天',
            '3months' => '最近3个月',
        ];
    }

    protected function getData(): array
    {
        $days = match($this->filter) {
            '7days' => 7,
            '30days' => 30,
            '3months' => 90,
            default => 30,
        };
        
        // 获取热销商品数据
        $topGoods = Order::where('orders.status', Order::STATUS_COMPLETED)
            ->where('orders.created_at', '>=', now()->subDays($days))
            ->join('goods', 'orders.goods_id', '=', 'goods.id')
            ->select('goods.gd_name', DB::raw('COUNT(*) as sales_count'), DB::raw('SUM(orders.actual_price) as revenue'))
            ->groupBy('goods.id', 'goods.gd_name')
            ->orderByDesc('sales_count')
            ->limit(10)
            ->get();

        // 如果没有销售数据，显示可用商品
        if ($topGoods->isEmpty()) {
            $topGoods = \App\Models\Goods::where('is_open', true)
                ->select('gd_name')
                ->selectRaw('0 as sales_count, 0 as revenue')
                ->limit(5)
                ->get();
        }

        $labels = [];
        $salesData = [];
        $revenueData = [];
        
        foreach ($topGoods as $goods) {
            $labels[] = mb_strlen($goods->gd_name) > 10 ? 
                mb_substr($goods->gd_name, 0, 10) . '...' : 
                $goods->gd_name;
            $salesData[] = $goods->sales_count;
            $revenueData[] = (float) $goods->revenue;
        }

        // 如果没有任何商品数据，显示占位信息
        if (empty($labels)) {
            $labels = ['暂无商品数据'];
            $salesData = [0];
        }

        return [
            'datasets' => [
                [
                    'label' => '销量',
                    'data' => $salesData,
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(6, 182, 212, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(132, 204, 22, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(115, 115, 115, 0.8)',
                    ],
                    'borderColor' => [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(6, 182, 212, 1)',
                        'rgba(249, 115, 22, 1)',
                        'rgba(132, 204, 22, 1)',
                        'rgba(236, 72, 153, 1)',
                        'rgba(115, 115, 115, 1)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'indexAxis' => 'y', // 水平柱状图
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'title' => 'function(context) {
                            return context[0].label;
                        }',
                        'label' => 'function(context) {
                            return "销量: " + context.parsed.x + " 件";
                        }',
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => '销量 (件)',
                    ],
                ],
                'y' => [
                    'title' => [
                        'display' => true,
                        'text' => '商品名称',
                    ],
                ],
            ],
        ];
    }
}