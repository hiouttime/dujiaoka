<?php

namespace App\PaymentGateways;

use App\PaymentGateways\Contracts\PaymentDriverInterface;
use App\Exceptions\AppException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * 支付管理器
 * 负责管理所有支付驱动的注册、发现和调用
 */
class PaymentManager
{
    /**
     * 已注册的驱动
     */
    protected array $drivers = [];

    /**
     * 驱动实例缓存
     */
    protected array $instances = [];

    /**
     * 自动发现的驱动
     */
    protected array $discoveredDrivers = [];

    public function __construct()
    {
        $this->autoDiscoverDrivers();
    }

    /**
     * 注册支付驱动
     */
    public function registerDriver(string $name, string $driverClass): void
    {
        if (!class_exists($driverClass)) {
            throw new AppException("Payment driver class {$driverClass} not found");
        }

        if (!in_array(PaymentDriverInterface::class, class_implements($driverClass))) {
            throw new AppException("Payment driver {$driverClass} must implement PaymentDriverInterface");
        }

        $this->drivers[$name] = $driverClass;
    }

    /**
     * 获取驱动实例
     */
    public function driver(string $name): PaymentDriverInterface
    {
        if (!isset($this->instances[$name])) {
            if (!isset($this->drivers[$name])) {
                throw new AppException("Payment driver [{$name}] not found");
            }

            $this->instances[$name] = app($this->drivers[$name]);
        }

        return $this->instances[$name];
    }

    /**
     * 获取所有已注册的驱动
     */
    public function getRegisteredDrivers(): array
    {
        return array_keys($this->drivers);
    }

    /**
     * 获取所有驱动的显示信息
     */
    public function getAllDriversInfo(): Collection
    {
        return collect($this->drivers)->map(function ($driverClass, $name) {
            $driver = $this->driver($name);
            return [
                'name' => $name,
                'display_name' => $driver->getDisplayName(),
                'supported_payways' => $driver->getSupportedPayways(),
            ];
        });
    }

    /**
     * 根据支付方式查找对应的驱动
     */
    public function findDriverByPayway(string $payway): ?string
    {
        foreach ($this->getRegisteredDrivers() as $driverName) {
            $driver = $this->driver($driverName);
            if (in_array($payway, $driver->getSupportedPayways())) {
                return $driverName;
            }
        }

        return null;
    }

    /**
     * 自动发现支付驱动
     */
    protected function autoDiscoverDrivers(): void
    {
        $driversPath = app_path('PaymentGateways/Drivers');
        
        if (!is_dir($driversPath)) {
            return;
        }

        $files = glob($driversPath . '/*Driver.php');
        
        foreach ($files as $file) {
            $className = basename($file, '.php');
            $driverName = Str::snake(str_replace('Driver', '', $className));
            $fullClassName = "App\\PaymentGateways\\Drivers\\{$className}";
            
            if (class_exists($fullClassName)) {
                $this->registerDriver($driverName, $fullClassName);
            }
        }
    }

    /**
     * 检查驱动是否存在
     */
    public function hasDriver(string $name): bool
    {
        return isset($this->drivers[$name]);
    }
}