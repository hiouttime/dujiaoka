<?php

namespace App\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = '销售趋势';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 2;

    public ?string $filter = 'week';

    protected function getData(): array
    {
        $filter = $this->filter;
        
        if ($filter === 'week') {
            return $this->getWeeklyData();
        } elseif ($filter === 'month') {
            return $this->getMonthlyData();
        }
        
        return $this->getDailyData();
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => '最近7天',
            'month' => '最近30天',
            'today' => '今日每小时',
        ];
    }

    private function getDailyData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('m/d');
            
            $revenue = Order::whereDate('created_at', $date)
                ->where('status', 4)
                ->sum('actual_price');
                
            $data[] = (float) $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => '销售额',
                    'data' => $data,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getWeeklyData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subWeeks($i)->startOfWeek();
            $endDate = $date->copy()->endOfWeek();
            $labels[] = $date->format('m/d') . '-' . $endDate->format('m/d');
            
            $revenue = Order::whereBetween('created_at', [$date, $endDate])
                ->where('status', 4)
                ->sum('actual_price');
                
            $data[] = (float) $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => '销售额',
                    'data' => $data,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getMonthlyData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('m/d');
            
            $revenue = Order::whereDate('created_at', $date)
                ->where('status', 4)
                ->sum('actual_price');
                
            $data[] = (float) $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => '销售额',
                    'data' => $data,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}