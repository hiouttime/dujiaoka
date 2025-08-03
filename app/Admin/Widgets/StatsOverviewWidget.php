<?php

namespace App\Admin\Widgets;

use App\Models\Order;
use App\Models\Goods;
use App\Models\Carmis;
use App\Models\Coupon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $cacheKey = 'stats_overview_' . $today->format('Y-m-d') . '_' . $thisMonth->format('Y-m');
        
        $data = Cache::remember($cacheKey, 600, function () use ($today, $thisMonth) {
            // 今日订单统计
            $todayOrders = Order::whereDate('created_at', $today)->count();
            $todaySuccessOrders = Order::whereDate('created_at', $today)
                ->where('status', Order::STATUS_COMPLETED)
                ->count();
            
            // 今日销售额
            $todayRevenue = Order::whereDate('created_at', $today)
                ->where('status', Order::STATUS_COMPLETED)
                ->sum('actual_price');
            
            // 本月销售额
            $monthRevenue = Order::where('created_at', '>=', $thisMonth)
                ->where('status', Order::STATUS_COMPLETED)
                ->sum('actual_price');
            
            // 库存统计
            $totalCarmis = Carmis::count();
            $availableCarmis = Carmis::where('status', Carmis::STATUS_UNSOLD)->count();
            
            // 商品统计
            $totalGoods = Goods::count();
            $activeGoods = Goods::where('is_open', true)->count();
            
            // 优惠券统计
            $activeCoupons = Coupon::where('is_open', true)->count();
            
            return [
                'todayOrders' => $todayOrders,
                'todaySuccessOrders' => $todaySuccessOrders,
                'todayRevenue' => $todayRevenue,
                'monthRevenue' => $monthRevenue,
                'totalCarmis' => $totalCarmis,
                'availableCarmis' => $availableCarmis,
                'totalGoods' => $totalGoods,
                'activeGoods' => $activeGoods,
                'activeCoupons' => $activeCoupons,
            ];
        });
        
        return [
            Stat::make('今日订单', $data['todayOrders'])
                ->description('今日成功: ' . $data['todaySuccessOrders'])
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info'),
            
            Stat::make('今日销售额', '¥' . number_format($data['todayRevenue'], 2))
                ->description('人民币')
                ->descriptionIcon('heroicon-m-currency-yen')
                ->color('success'),
            
            Stat::make('本月销售额', '¥' . number_format($data['monthRevenue'], 2))
                ->description('人民币')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
            
            Stat::make('可用卡密', $data['availableCarmis'])
                ->description('总卡密: ' . $data['totalCarmis'])
                ->descriptionIcon('heroicon-m-key')
                ->color('warning'),
            
            Stat::make('活跃商品', $data['activeGoods'])
                ->description('总商品: ' . $data['totalGoods'])
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),
            
            Stat::make('优惠券', $data['activeCoupons'])
                ->description('可用优惠券')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('success'),
        ];
    }
    
    protected function getColumns(): int
    {
        return 3;
    }
}