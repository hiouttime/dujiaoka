<?php

namespace App\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class SalesOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $now = Carbon::now();
        
        // 今日数据
        $todayStart = $now->startOfDay();
        $todayEnd = $now->copy()->endOfDay();
        $todayOrders = Order::whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('status', 4) // 已完成
            ->count();
        $todayRevenue = Order::whereBetween('created_at', [$todayStart, $todayEnd])
            ->where('status', 4)
            ->sum('actual_price');
            
        // 本周数据
        $weekStart = $now->copy()->startOfWeek();
        $weekEnd = $now->copy()->endOfWeek();
        $weekOrders = Order::whereBetween('created_at', [$weekStart, $weekEnd])
            ->where('status', 4)
            ->count();
        $weekRevenue = Order::whereBetween('created_at', [$weekStart, $weekEnd])
            ->where('status', 4)
            ->sum('actual_price');
            
        // 本月数据
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();
        $monthOrders = Order::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 4)
            ->count();
        $monthRevenue = Order::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 4)
            ->sum('actual_price');

        return [
            Stat::make('今日销售', '¥' . number_format($todayRevenue, 2))
                ->description('订单数: ' . $todayOrders)
                ->icon('heroicon-m-currency-dollar')
                ->color('success'),
                
            Stat::make('本周销售', '¥' . number_format($weekRevenue, 2))
                ->description('订单数: ' . $weekOrders)
                ->icon('heroicon-m-chart-bar')
                ->color('info'),
                
            Stat::make('本月销售', '¥' . number_format($monthRevenue, 2))
                ->description('订单数: ' . $monthOrders)
                ->icon('heroicon-m-chart-pie')
                ->color('warning'),
        ];
    }
}