<?php

namespace App\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrderStatusChartWidget extends ChartWidget
{
    protected static ?string $heading = '订单状态分布';
    
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];
    
    public ?string $filter = 'today';
    
    protected function getFilters(): ?array
    {
        return [
            'today' => '今日',
            'week' => '本周',
            'month' => '本月',
            'all' => '全部',
        ];
    }

    protected function getData(): array
    {
        $query = Order::query();
        
        switch ($this->filter) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                break;
            // 'all' 不需要额外条件
        }
        
        $statusStats = $query->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $statusMap = [
            Order::STATUS_WAIT_PAY => '待付款',
            Order::STATUS_PENDING => '待处理',
            Order::STATUS_PROCESSING => '处理中',
            Order::STATUS_COMPLETED => '已完成',
            Order::STATUS_FAILURE => '已失败',
            Order::STATUS_ABNORMAL => '异常订单',
            Order::STATUS_EXPIRED => '已过期',
        ];
        
        $statusColors = [
            Order::STATUS_WAIT_PAY => '#F59E0B',     // 橙色
            Order::STATUS_PENDING => '#6B7280',     // 灰色
            Order::STATUS_PROCESSING => '#3B82F6',  // 蓝色
            Order::STATUS_COMPLETED => '#10B981',   // 绿色
            Order::STATUS_FAILURE => '#EF4444',     // 红色
            Order::STATUS_ABNORMAL => '#8B5CF6',    // 紫色
            Order::STATUS_EXPIRED => '#374151',     // 深灰色
        ];

        $labels = [];
        $data = [];
        $colors = [];
        
        foreach ($statusStats as $stat) {
            if (isset($statusMap[$stat->status])) {
                $labels[] = $statusMap[$stat->status];
                $data[] = $stat->count;
                $colors[] = $statusColors[$stat->status];
            }
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
    
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 15,
                        'usePointStyle' => true,
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            var label = context.label || "";
                            var value = context.parsed;
                            var total = context.dataset.data.reduce((a, b) => a + b, 0);
                            var percentage = ((value / total) * 100).toFixed(1);
                            return label + ": " + value + " (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
        ];
    }
}