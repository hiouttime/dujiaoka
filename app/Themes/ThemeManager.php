<?php

namespace App\Themes;

use App\Themes\Contracts\ThemeConfigInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * 主题管理器
 * 负责主题的发现、注册、配置管理等功能
 */
class ThemeManager
{
    /**
     * 主题目录
     */
    protected string $themesPath;

    /**
     * 已发现的主题
     */
    protected array $themes = [];

    /**
     * 主题配置实例缓存
     */
    protected array $configInstances = [];

    /**
     * 当前激活的主题
     */
    protected ?string $activeTheme = null;

    public function __construct()
    {
        $this->themesPath = resource_path('views/themes');
        $this->discoverThemes();
        $this->setActiveTheme($this->getActiveThemeFromConfig());
    }

    /**
     * 设置激活主题
     */
    public function setActiveTheme(string $theme): void
    {
        if (!$this->hasTheme($theme)) {
            throw new \InvalidArgumentException("Theme [{$theme}] not found");
        }

        $this->activeTheme = $theme;
        
        // 更新视图路径
        $this->updateViewPaths();
    }

    /**
     * 获取当前激活主题
     */
    public function getActiveTheme(): ?string
    {
        return $this->activeTheme;
    }

    /**
     * 检查主题是否存在
     */
    public function hasTheme(string $theme): bool
    {
        return isset($this->themes[$theme]);
    }

    /**
     * 获取所有主题
     */
    public function getAllThemes(): array
    {
        return $this->themes;
    }

    /**
     * 获取主题配置接口实例
     */
    public function getThemeConfig(string $theme): ?ThemeConfigInterface
    {
        if (!$this->hasTheme($theme)) {
            return null;
        }

        if (!isset($this->configInstances[$theme])) {
            $configClass = $this->themes[$theme]['config_class'] ?? null;
            
            if ($configClass && class_exists($configClass)) {
                $this->configInstances[$theme] = app($configClass);
            } else {
                // 使用默认配置类
                $this->configInstances[$theme] = new DefaultThemeConfig($theme);
            }
        }

        return $this->configInstances[$theme];
    }

    /**
     * 获取主题配置值
     */
    public function getThemeConfigValue(string $key, $default = null, ?string $theme = null)
    {
        $theme = $theme ?: $this->activeTheme;
        $config = Cache::get("theme-{$theme}-config", []);
        
        return data_get($config, $key, $default);
    }

    /**
     * 设置主题配置
     */
    public function setThemeConfig(string $theme, array $config): void
    {
        $themeConfig = $this->getThemeConfig($theme);
        
        if ($themeConfig) {
            // 验证配置
            $errors = $themeConfig->validateConfig($config);
            if (!empty($errors)) {
                throw new \InvalidArgumentException('Theme config validation failed: ' . implode(', ', $errors));
            }

            // 处理配置
            $config = $themeConfig->processConfig($config);
        }

        Cache::put("theme-{$theme}-config", $config);
    }

    /**
     * 获取主题信息
     */
    public function getThemeInfo(string $theme): array
    {
        if (!$this->hasTheme($theme)) {
            return [];
        }

        $themeConfig = $this->getThemeConfig($theme);
        $info = $this->themes[$theme];
        
        if ($themeConfig) {
            $info = array_merge($info, $themeConfig->getThemeInfo());
        }

        return $info;
    }

    /**
     * 获取主题配置字段定义
     */
    public function getThemeConfigFields(string $theme): array
    {
        $themeConfig = $this->getThemeConfig($theme);
        
        return $themeConfig ? $themeConfig->getConfigFields() : [];
    }

    /**
     * 编译主题资源
     */
    public function compileThemeAssets(string $theme): void
    {
        $themePath = $this->getThemePath($theme);
        $assetsPath = $themePath . '/assets';
        $publicPath = public_path("themes/{$theme}");

        if (File::exists($assetsPath)) {
            File::ensureDirectoryExists($publicPath);
            File::copyDirectory($assetsPath, $publicPath);
        }
    }

    /**
     * 发现主题
     */
    protected function discoverThemes(): void
    {
        if (!File::exists($this->themesPath)) {
            return;
        }

        $directories = File::directories($this->themesPath);

        foreach ($directories as $directory) {
            $themeName = basename($directory);
            $configFile = $directory . '/theme.json';
            $configClass = null;

            // 检查是否有主题配置文件
            if (File::exists($configFile)) {
                $config = json_decode(File::get($configFile), true);
                
                // 查找配置类
                if (isset($config['config_class'])) {
                    $configClass = $config['config_class'];
                } else {
                    // 尝试自动发现配置类
                    $configClassName = "App\\Themes\\Configs\\" . Str::studly($themeName) . "ThemeConfig";
                    if (class_exists($configClassName)) {
                        $configClass = $configClassName;
                    }
                }

                $this->themes[$themeName] = array_merge([
                    'name' => $themeName,
                    'path' => $directory,
                    'config_class' => $configClass,
                ], $config);
            } else {
                $this->themes[$themeName] = [
                    'name' => $themeName,
                    'display_name' => Str::title($themeName),
                    'path' => $directory,
                    'config_class' => $configClass,
                ];
            }
        }
    }

    /**
     * 从配置获取激活主题
     */
    protected function getActiveThemeFromConfig(): string
    {
        // 从系统配置获取
        return dujiaoka_config_get('website_style', 'neon');
    }

    /**
     * 更新视图路径
     */
    protected function updateViewPaths(): void
    {
        if (!$this->activeTheme) {
            return;
        }

        $themePath = $this->getThemePath($this->activeTheme);
        $viewPath = $themePath . '/views';

        if (File::exists($viewPath)) {
            view()->addLocation($viewPath);
        }
    }

    /**
     * 获取主题路径
     */
    protected function getThemePath(string $theme): string
    {
        return $this->themesPath . '/' . $theme;
    }
}