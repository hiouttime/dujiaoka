<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Config\ConfigManager;

class ConfigServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ConfigManager::class, function ($app) {
            return new ConfigManager();
        });

        $this->app->alias(ConfigManager::class, 'config.manager');
    }

    public function boot()
    {
        if (!function_exists('config_get')) {
            function config_get(string $key, $default = null)
            {
                return app('config.manager')->get($key, $default);
            }
        }

        if (!function_exists('config_set')) {
            function config_set(string $key, $value): void
            {
                app('config.manager')->set($key, $value);
            }
        }
    }
}