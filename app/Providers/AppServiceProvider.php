<?php

namespace App\Providers;

use App\Services\Cards;
use App\Services\Coupons;
use App\Services\Email;
use App\Services\Shop;
use App\Services\OrderProcess;
use App\Services\Orders;
use App\Services\Payment;
use App\Services\Validator;
use App\Services\CacheManager;
use Illuminate\Support\ServiceProvider;
use Jenssegers\Agent\Agent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Shop::class);
        $this->app->singleton(Payment::class);
        $this->app->singleton(Cards::class);
        $this->app->singleton(Orders::class);
        $this->app->singleton(Coupons::class);
        $this->app->singleton(OrderProcess::class);
        $this->app->singleton(Email::class);
        $this->app->singleton(Validator::class);
        $this->app->singleton(CacheManager::class);
        $this->app->singleton('Jenssegers\Agent', function () {
            return $this->app->make(Agent::class);
        });

        $this->app->singleton('App\\Services\\ConfigService', function ($app) {
            return new \App\Services\ConfigService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
