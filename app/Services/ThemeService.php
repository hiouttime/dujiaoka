<?php

namespace App\Services;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

class ThemeService
{
    protected string $currentTheme;
    
    public function __construct()
    {
        $this->currentTheme = cfg('template', 'morpho');
        $this->registerViews();
    }
    
    public function getCurrentTheme(): string
    {
        return $this->currentTheme;
    }
    
    /**
     * 注册主题视图路径
     */
    protected function registerViews(): void
    {
        $themePath = resource_path("views/themes/{$this->currentTheme}/views");
        
        if (is_dir($themePath)) {
            View::addNamespace($this->currentTheme, $themePath);
        }
    }
    
    /**
     * 获取主题配置值
     */
    public function getConfig(string $key, $default = null)
    {
        return Cache::get("theme.{$this->currentTheme}.{$key}", $default);
    }
    
    /**
     * 设置主题配置值
     */
    public function setConfig(string $key, $value): void
    {
        Cache::put("theme.{$this->currentTheme}.{$key}", $value);
    }
    
    /**
     * 获取主题资源URL
     */
    public function asset(string $path): string
    {
        return "/assets/{$this->currentTheme}/{$path}";
    }
}