<?php

namespace App\Admin\Widgets;

use App\Models\Order;
use App\Models\Goods;
use App\Models\Carmis;
use App\Models\Coupon;
use App\Models\Pay;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class EnhancedStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        
        // 今日统计
        $todayOrders = Order::whereDate('created_at', $today)->count();
        $todaySuccessOrders = Order::whereDate('created_at', $today)
            ->where('status', Order::STATUS_COMPLETED)
            ->count();
        $todayRevenue = Order::whereDate('created_at', $today)
            ->where('status', Order::STATUS_COMPLETED)
            ->sum('actual_price');
        
        // 昨日统计（用于计算增长率）
        $yesterdayOrders = Order::whereDate('created_at', $yesterday)->count();
        $yesterdayRevenue = Order::whereDate('created_at', $yesterday)
            ->where('status', Order::STATUS_COMPLETED)
            ->sum('actual_price');
        
        // 本月统计
        $monthRevenue = Order::where('created_at', '>=', $thisMonth)
            ->where('status', Order::STATUS_COMPLETED)
            ->sum('actual_price');
        $monthOrders = Order::where('created_at', '>=', $thisMonth)->count();
        
        // 上月统计（用于计算增长率）
        $lastMonthRevenue = Order::whereBetween('created_at', [$lastMonth, $lastMonthEnd])
            ->where('status', Order::STATUS_COMPLETED)
            ->sum('actual_price');
        $lastMonthOrders = Order::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();
        
        // 库存统计
        $totalCarmis = Carmis::count();
        $availableCarmis = Carmis::where('status', Carmis::STATUS_UNSOLD)->count();
        $stockRate = $totalCarmis > 0 ? round(($availableCarmis / $totalCarmis) * 100, 1) : 0;
        
        // 商品统计
        $totalGoods = Goods::count();
        $activeGoods = Goods::where('is_open', true)->count();
        
        // 计算增长率
        $orderGrowth = $yesterdayOrders > 0 ? 
            round((($todayOrders - $yesterdayOrders) / $yesterdayOrders) * 100, 1) : 
            ($todayOrders > 0 ? 100 : 0);
        
        $revenueGrowth = $yesterdayRevenue > 0 ? 
            round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 1) : 
            ($todayRevenue > 0 ? 100 : 0);
            
        $monthlyRevenueGrowth = $lastMonthRevenue > 0 ? 
            round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 
            ($monthRevenue > 0 ? 100 : 0);
        
        // 转换率
        $conversionRate = $todayOrders > 0 ? 
            round(($todaySuccessOrders / $todayOrders) * 100, 1) : 0;
        
        return [
            Stat::make('今日订单', $todayOrders)
                ->description(($orderGrowth >= 0 ? '+' : '') . $orderGrowth . '% 较昨日')
                ->descriptionIcon($orderGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($orderGrowth >= 0 ? 'success' : 'danger'),
            
            Stat::make('今日销售额', '¥' . number_format($todayRevenue, 2))
                ->description(($revenueGrowth >= 0 ? '+' : '') . $revenueGrowth . '% 较昨日')
                ->descriptionIcon($revenueGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueGrowth >= 0 ? 'success' : 'danger'),
            
            Stat::make('本月销售额', '¥' . number_format($monthRevenue, 2))
                ->description(($monthlyRevenueGrowth >= 0 ? '+' : '') . $monthlyRevenueGrowth . '% 较上月')
                ->descriptionIcon($monthlyRevenueGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthlyRevenueGrowth >= 0 ? 'success' : 'danger'),
            
            Stat::make('转换率', $conversionRate . '%')
                ->description('今日成功订单率')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color($conversionRate >= 80 ? 'success' : ($conversionRate >= 60 ? 'warning' : 'danger')),
            
            Stat::make('库存状态', $availableCarmis)
                ->description($stockRate . '% 可用 (总计: ' . $totalCarmis . ')')
                ->descriptionIcon('heroicon-m-cube')
                ->color($stockRate >= 50 ? 'success' : ($stockRate >= 20 ? 'warning' : 'danger')),
            
            Stat::make('商品状态', $activeGoods)
                ->description('活跃商品 (总计: ' . $totalGoods . ')')
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color($activeGoods > 0 ? 'info' : 'warning'),
        ];
    }
    
    protected function getColumns(): int
    {
        return 3;
    }
}