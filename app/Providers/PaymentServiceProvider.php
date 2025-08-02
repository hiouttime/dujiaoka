<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\PaymentGateways\PaymentManager;
use Illuminate\Support\Facades\Route;

/**
 * 支付服务提供者
 * 负责注册支付服务和动态路由
 */
class PaymentServiceProvider extends ServiceProvider
{
    /**
     * 注册服务
     */
    public function register()
    {
        $this->app->singleton(PaymentManager::class, function ($app) {
            return new PaymentManager();
        });

        // 注册支付管理器别名
        $this->app->alias(PaymentManager::class, 'payment.manager');
    }

    /**
     * 启动服务
     */
    public function boot()
    {
        $this->registerPaymentRoutes();
        $this->registerPaymentDrivers();
    }

    /**
     * 注册支付路由
     */
    protected function registerPaymentRoutes()
    {
        Route::macro('paymentRoutes', function () {
            $paymentManager = app(PaymentManager::class);
            
            foreach ($paymentManager->getRegisteredDrivers() as $driver) {
                Route::group([
                    'prefix' => "pay/{$driver}",
                    'middleware' => ['dujiaoka.pay_gate_way']
                ], function () use ($driver) {
                    Route::get('{payway}/{orderSN}', 'UnifiedPaymentController@gateway')
                        ->name("payment.{$driver}.gateway");
                    
                    Route::post('notify_url', 'UnifiedPaymentController@notify')
                        ->name("payment.{$driver}.notify");
                    
                    Route::get('return_url', 'UnifiedPaymentController@returnUrl')
                        ->name("payment.{$driver}.return");
                });
            }
        });
    }

    /**
     * 注册默认支付驱动
     */
    protected function registerPaymentDrivers()
    {
        $paymentManager = app(PaymentManager::class);
        
        // 可以在这里手动注册额外的驱动
        // $paymentManager->registerDriver('custom_payment', CustomPaymentDriver::class);
    }
}