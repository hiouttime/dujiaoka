<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\PaymentGateways\PaymentManager;

class PaymentDriverCommand extends Command
{
    protected $signature = 'payment:drivers {action?} {driver?}';
    protected $description = '管理支付驱动';

    public function handle()
    {
        $action = $this->argument('action') ?: 'list';
        $paymentManager = app(PaymentManager::class);

        switch ($action) {
            case 'list':
                $this->listDrivers($paymentManager);
                break;
            
            case 'info':
                $driver = $this->argument('driver');
                if (!$driver) {
                    $this->error('请指定驱动名称');
                    return 1;
                }
                $this->showDriverInfo($paymentManager, $driver);
                break;
            
            case 'test':
                $driver = $this->argument('driver');
                if (!$driver) {
                    $this->error('请指定驱动名称');
                    return 1;
                }
                $this->testDriver($paymentManager, $driver);
                break;
            
            default:
                $this->error("未知操作: {$action}");
                return 1;
        }

        return 0;
    }

    protected function listDrivers(PaymentManager $paymentManager): void
    {
        $drivers = $paymentManager->getAllDriversInfo();
        
        $this->info('已注册的支付驱动:');
        $this->table(
            ['驱动名称', '显示名称', '支持的支付方式'],
            $drivers->map(function ($driver) {
                return [
                    $driver['name'],
                    $driver['display_name'],
                    implode(', ', $driver['supported_payways'])
                ];
            })->toArray()
        );
    }

    protected function showDriverInfo(PaymentManager $paymentManager, string $driverName): void
    {
        if (!$paymentManager->hasDriver($driverName)) {
            $this->error("驱动 [{$driverName}] 不存在");
            return;
        }

        $driver = $paymentManager->driver($driverName);
        
        $this->info("驱动信息: {$driverName}");
        $this->line("名称: {$driver->getDisplayName()}");
        $this->line("支持的支付方式: " . implode(', ', $driver->getSupportedPayways()));
        $this->line("类名: " . get_class($driver));
    }

    protected function testDriver(PaymentManager $paymentManager, string $driverName): void
    {
        if (!$paymentManager->hasDriver($driverName)) {
            $this->error("驱动 [{$driverName}] 不存在");
            return;
        }

        try {
            $driver = $paymentManager->driver($driverName);
            $this->info("驱动 [{$driverName}] 测试成功");
            $this->line("支持的支付方式: " . implode(', ', $driver->getSupportedPayways()));
        } catch (\Exception $e) {
            $this->error("驱动 [{$driverName}] 测试失败: " . $e->getMessage());
        }
    }
}