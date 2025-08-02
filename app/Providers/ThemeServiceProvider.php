<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Themes\ThemeManager;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

/**
 * 主题服务提供者
 */
class ThemeServiceProvider extends ServiceProvider
{
    /**
     * 注册服务
     */
    public function register()
    {
        $this->app->singleton(ThemeManager::class, function ($app) {
            return new ThemeManager();
        });

        $this->app->alias(ThemeManager::class, 'theme.manager');
    }

    /**
     * 启动服务
     */
    public function boot()
    {
        $this->registerViewComposer();
        $this->registerBladeDirectives();
        $this->registerHelpers();
    }

    /**
     * 注册视图组合器
     */
    protected function registerViewComposer()
    {
        View::composer('*', function ($view) {
            $themeManager = app(ThemeManager::class);
            $activeTheme = $themeManager->getActiveTheme();
            
            if ($activeTheme) {
                $view->with('currentTheme', $activeTheme);
                $view->with('themeConfig', function() use ($themeManager, $activeTheme) {
                    $config = [];
                    $configFields = $themeManager->getThemeConfigFields($activeTheme);
                    
                    foreach ($configFields as $section) {
                        foreach ($section['fields'] as $key => $field) {
                            $config[$key] = $themeManager->getThemeConfigValue($key, $field['default'] ?? null);
                        }
                    }
                    
                    return $config;
                });
            }
        });
    }

    /**
     * 注册Blade指令
     */
    protected function registerBladeDirectives()
    {
        // @themeConfig('key', 'default')
        Blade::directive('themeConfig', function ($expression) {
            return "<?php echo app('theme.manager')->getThemeConfigValue({$expression}); ?>";
        });

        // @themeAsset('path')
        Blade::directive('themeAsset', function ($expression) {
            return "<?php echo '/themes/' . app('theme.manager')->getActiveTheme() . '/assets/' . {$expression}; ?>";
        });

        // @themeStyle
        Blade::directive('themeStyle', function () {
            return '<?php 
                $themeManager = app("theme.manager");
                $theme = $themeManager->getActiveTheme();
                if ($theme) {
                    echo "<link rel=\"stylesheet\" href=\"/themes/{$theme}/assets/css/theme.css\">";
                    
                    // 输出自定义CSS
                    $customCSS = $themeManager->getThemeConfigValue("custom_css");
                    if ($customCSS) {
                        echo "<style>{$customCSS}</style>";
                    }
                }
            ?>';
        });

        // @themeScript
        Blade::directive('themeScript', function () {
            return '<?php 
                $themeManager = app("theme.manager");
                $theme = $themeManager->getActiveTheme();
                if ($theme) {
                    // 输出主题配置到JavaScript
                    $config = [];
                    $configFields = $themeManager->getThemeConfigFields($theme);
                    foreach ($configFields as $section) {
                        foreach ($section["fields"] as $key => $field) {
                            $config[$key] = $themeManager->getThemeConfigValue($key, $field["default"] ?? null);
                        }
                    }
                    echo "<script>window.themeConfig = " . json_encode($config) . ";</script>";
                    echo "<script src=\"/themes/{$theme}/assets/js/theme.js\"></script>";
                    
                    // 输出自定义JavaScript
                    $customJS = $themeManager->getThemeConfigValue("custom_js");
                    if ($customJS) {
                        echo "<script>{$customJS}</script>";
                    }
                }
            ?>';
        });

        // @heroSection
        Blade::directive('heroSection', function () {
            return '<?php 
                $themeManager = app("theme.manager");
                $heroTitle = $themeManager->getThemeConfigValue("hero_title", "欢迎来到我们的商店");
                $heroSubtitle = $themeManager->getThemeConfigValue("hero_subtitle", "为您提供优质的数字商品服务");
                $heroBackground = $themeManager->getThemeConfigValue("hero_background");
                $heroOverlayOpacity = $themeManager->getThemeConfigValue("hero_overlay_opacity", 50);
                $ctaButtonText = $themeManager->getThemeConfigValue("cta_button_text", "立即购买");
                $ctaButtonLink = $themeManager->getThemeConfigValue("cta_button_link", "#products");
                
                echo "<section class=\"hero-section\" style=\"" . ($heroBackground ? "background-image: url({$heroBackground}); background-size: cover; background-position: center;" : "") . "\">";
                if ($heroBackground) {
                    echo "<div class=\"hero-overlay\" style=\"background: rgba(0,0,0," . ($heroOverlayOpacity/100) . ");\"></div>";
                }
                echo "<div class=\"hero-content\">";
                echo "<h1 class=\"hero-title\">{$heroTitle}</h1>";
                echo "<p class=\"hero-subtitle\">{$heroSubtitle}</p>";
                echo "<a href=\"{$ctaButtonLink}\" class=\"btn btn-primary hero-cta\">{$ctaButtonText}</a>";
                echo "</div></section>";
            ?>';
        });
    }

    /**
     * 注册辅助函数
     */
    protected function registerHelpers()
    {
        if (!function_exists('theme_config')) {
            /**
             * 获取主题配置值
             */
            function theme_config(string $key, $default = null, ?string $theme = null)
            {
                return app('theme.manager')->getThemeConfigValue($key, $default, $theme);
            }
        }

        if (!function_exists('theme_asset')) {
            /**
             * 获取主题资源URL
             */
            function theme_asset(string $path, ?string $theme = null): string
            {
                $theme = $theme ?: app('theme.manager')->getActiveTheme();
                return "/themes/{$theme}/assets/{$path}";
            }
        }

        if (!function_exists('current_theme')) {
            /**
             * 获取当前主题名称
             */
            function current_theme(): ?string
            {
                return app('theme.manager')->getActiveTheme();
            }
        }
    }
}