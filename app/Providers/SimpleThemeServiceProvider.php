<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ThemeService;

class SimpleThemeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ThemeService::class);
    }

    public function boot()
    {
        $this->app->make(ThemeService::class);
    }
}