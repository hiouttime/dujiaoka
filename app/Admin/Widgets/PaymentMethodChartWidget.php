<?php

namespace App\Admin\Widgets;

use App\Models\Order;
use App\Models\Pay;
use Filament\Widgets\ChartWidget;

class PaymentMethodChartWidget extends ChartWidget
{
    protected static ?string $heading = '支付方式分布';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    protected function getData(): array
    {
        // 获取所有支付方式的订单统计
        $paymentStats = Order::where('orders.status', Order::STATUS_COMPLETED)
            ->join('pays', 'orders.pay_id', '=', 'pays.id')
            ->selectRaw('pays.pay_name, COUNT(*) as count, SUM(orders.actual_price) as revenue')
            ->groupBy('pays.id', 'pays.pay_name')
            ->orderByDesc('count')
            ->get();

        // 如果没有已完成的订单数据，显示所有可用的支付方式
        if ($paymentStats->isEmpty()) {
            $paymentStats = \App\Models\Pay::where('enable', Pay::ENABLED)
                ->select('pay_name')
                ->selectRaw('0 as count, 0 as revenue')
                ->limit(8)
                ->get();
        }

        $labels = [];
        $data = [];
        $colors = [
            '#3B82F6', // 蓝色
            '#10B981', // 绿色
            '#F59E0B', // 橙色
            '#EF4444', // 红色
            '#8B5CF6', // 紫色
            '#06B6D4', // 青色
            '#F97316', // 橙红色
            '#84CC16', // 绿黄色
        ];

        foreach ($paymentStats as $index => $stat) {
            $labels[] = $stat->pay_name;
            $data[] = $stat->count;
        }

        // 如果所有数据都是0，添加一个占位数据避免图表显示错误
        if (array_sum($data) === 0) {
            $labels = ['暂无数据'];
            $data = [1];
            $colors = ['#E5E7EB'];
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
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
            'cutout' => '60%',
        ];
    }
}