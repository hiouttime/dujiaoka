<?php

namespace App\Config;

use Illuminate\Support\Facades\Cache;

class ConfigManager
{
    protected array $configs = [];
    protected string $cachePrefix = 'config:';
    protected int $cacheTime = 3600;

    public function get(string $key, $default = null)
    {
        $cacheKey = $this->cachePrefix . $key;
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($key, $default) {
            return $this->resolveConfig($key, $default);
        });
    }

    public function set(string $key, $value): void
    {
        $this->configs[$key] = $value;
        Cache::put($this->cachePrefix . $key, $value, $this->cacheTime);
    }

    public function forget(string $key): void
    {
        unset($this->configs[$key]);
        Cache::forget($this->cachePrefix . $key);
    }

    public function flush(): void
    {
        $this->configs = [];
        Cache::flush();
    }

    protected function resolveConfig(string $key, $default)
    {
        if (isset($this->configs[$key])) {
            return $this->configs[$key];
        }

        $parts = explode('.', $key);
        $type = $parts[0];

        return match ($type) {
            'system' => $this->getSystemConfig($key, $default),
            'theme' => $this->getThemeConfig($key, $default),
            'payment' => $this->getPaymentConfig($key, $default),
            default => $default,
        };
    }

    protected function getSystemConfig(string $key, $default)
    {
        $systemConfig = Cache::get('system-setting', []);
        $configKey = str_replace('system.', '', $key);
        
        return $systemConfig[$configKey] ?? $default;
    }

    protected function getThemeConfig(string $key, $default)
    {
        $parts = explode('.', $key);
        $theme = $parts[1] ?? app('theme.manager')->getActiveTheme();
        $configKey = implode('.', array_slice($parts, 2));
        
        $themeConfig = Cache::get("theme-{$theme}-config", []);
        
        return data_get($themeConfig, $configKey, $default);
    }

    protected function getPaymentConfig(string $key, $default)
    {
        $paymentConfig = Cache::get('payment-config', []);
        $configKey = str_replace('payment.', '', $key);
        
        return $paymentConfig[$configKey] ?? $default;
    }
}