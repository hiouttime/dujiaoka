<?php

namespace App\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = '销售额趋势';
    
    protected static ?int $sort = 2;
    
    protected static string $color = 'info';
    
    protected int | string | array $columnSpan = 'full';
    
    public ?string $filter = '7days';
    
    protected function getFilters(): ?array
    {
        return [
            '7days' => '最近7天',
            '30days' => '最近30天',
            '3months' => '最近3个月',
            'year' => '本年度',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter;
        
        switch ($filter) {
            case '7days':
                return $this->getDailyData(7);
            case '30days':
                return $this->getDailyData(30);
            case '3months':
                return $this->getMonthlyData(3);
            case 'year':
                return $this->getMonthlyData(12);
            default:
                return $this->getDailyData(7);
        }
    }
    
    private function getDailyData(int $days): array
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($days - 1);
        
        $orders = Order::where('status', Order::STATUS_COMPLETED)
            ->whereBetween('created_at', [$startDate, $endDate->copy()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, SUM(actual_price) as revenue, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $labels = [];
        $revenueData = [];
        $orderData = [];
        
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dayData = $orders->where('date', $dateStr)->first();
            
            $labels[] = $date->format('m/d');
            $revenueData[] = $dayData ? (float) $dayData->revenue : 0;
            $orderData[] = $dayData ? (int) $dayData->count : 0;
        }
        
        return [
            'datasets' => [
                [
                    'label' => '销售额 (¥)',
                    'data' => $revenueData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => '订单数',
                    'data' => $orderData,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }
    
    private function getMonthlyData(int $months): array
    {
        $endDate = Carbon::now()->endOfMonth();
        $startDate = $endDate->copy()->subMonths($months - 1)->startOfMonth();
        
        $orders = Order::where('status', Order::STATUS_COMPLETED)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(actual_price) as revenue, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        $labels = [];
        $revenueData = [];
        $orderData = [];
        
        for ($date = $startDate->copy(); $date <= $endDate; $date->addMonth()) {
            $year = $date->year;
            $month = $date->month;
            $monthData = $orders->where('year', $year)->where('month', $month)->first();
            
            $labels[] = $date->format('Y/m');
            $revenueData[] = $monthData ? (float) $monthData->revenue : 0;
            $orderData[] = $monthData ? (int) $monthData->count : 0;
        }
        
        return [
            'datasets' => [
                [
                    'label' => '销售额 (¥)',
                    'data' => $revenueData,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => '订单数',
                    'data' => $orderData,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
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
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => '销售额 (¥)',
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => '订单数',
                    ],
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
                    'text' => '销售趋势分析',
                ],
            ],
        ];
    }
}