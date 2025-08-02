<?php

namespace App\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class HourlyOrdersWidget extends ChartWidget
{
    protected static ?string $heading = '24小时订单分布';
    
    protected static ?int $sort = 6;
    
    protected int | string | array $columnSpan = 'full';
    
    public ?string $filter = 'today';
    
    protected function getFilters(): ?array
    {
        return [
            'today' => '今日',
            'yesterday' => '昨日',
            '7days' => '最近7天平均',
        ];
    }

    protected function getData(): array
    {
        switch ($this->filter) {
            case 'today':
                return $this->getTodayData();
            case 'yesterday':
                return $this->getYesterdayData();
            case '7days':
                return $this->getWeekAverageData();
            default:
                return $this->getTodayData();
        }
    }
    
    private function getTodayData(): array
    {
        $orders = Order::whereDate('created_at', Carbon::today())
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(actual_price) as revenue')
            ->groupBy('hour')
            ->get();
        
        return $this->formatHourlyData($orders);
    }
    
    private function getYesterdayData(): array
    {
        $orders = Order::whereDate('created_at', Carbon::yesterday())
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(actual_price) as revenue')
            ->groupBy('hour')
            ->get();
        
        return $this->formatHourlyData($orders);
    }
    
    private function getWeekAverageData(): array
    {
        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) / 7 as count, SUM(actual_price) / 7 as revenue')
            ->groupBy('hour')
            ->get();
        
        return $this->formatHourlyData($orders, true);
    }
    
    private function formatHourlyData($orders, $isAverage = false): array
    {
        $labels = [];
        $orderData = [];
        $revenueData = [];
        
        for ($hour = 0; $hour < 24; $hour++) {
            $hourData = $orders->where('hour', $hour)->first();
            
            $labels[] = sprintf('%02d:00', $hour);
            $orderCount = $hourData ? ($isAverage ? round($hourData->count, 1) : $hourData->count) : 0;
            $revenue = $hourData ? ($isAverage ? round($hourData->revenue, 2) : $hourData->revenue) : 0;
            
            $orderData[] = $orderCount;
            $revenueData[] = (float) $revenue;
        }
        
        return [
            'datasets' => [
                [
                    'label' => $isAverage ? '平均订单数' : '订单数',
                    'data' => $orderData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'yAxisID' => 'y',
                    'type' => 'line',
                ],
                [
                    'label' => $isAverage ? '平均销售额 (¥)' : '销售额 (¥)',
                    'data' => $revenueData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.6)',
                    'borderColor' => 'rgb(16, 185, 129)',
                    'borderWidth' => 1,
                    'yAxisID' => 'y1',
                    'type' => 'bar',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // 混合图表的基础类型
    }
    
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'scales' => [
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => '时间 (小时)',
                    ],
                ],
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => '订单数',
                    ],
                    'beginAtZero' => true,
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => '销售额 (¥)',
                    ],
                    'beginAtZero' => true,
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => '订单时间分布分析',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
        ];
    }
}