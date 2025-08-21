<?php
/**
 * The file was created by Assimon.
 *
 * @author    assimon<ashang@utf8.hk>
 * @copyright assimon<ashang@utf8.hk>
 * @link      http://utf8.hk/
 */
use Illuminate\Support\Facades\Route;


Route::middleware('dujiaoka.boot')->namespace('Home')->group(function () {
    // 首页和商品
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('check-geetest', 'HomeController@geetest');
    Route::get('buy/{id}', 'HomeController@buy');
    
    // 购物车
    Route::prefix('cart')->controller('CartController')->group(function () {
        Route::get('/', 'index');
    });
    Route::get('/cart', 'CartController@index');
    
    // API路由
    Route::prefix('api')->group(function () {
        Route::post('cart/validate', 'CartController@validateItem');
    });
    
    // 订单相关
    Route::prefix('order')->controller('OrderController')->group(function () {
        Route::post('create', 'createOrder');
        Route::get('bill/{orderSN}', 'bill');
        Route::get('detail/{orderSN}', 'detailOrderSN');
        Route::get('search', 'orderSearch');
        Route::get('status/{orderSN}', 'checkOrderStatus');
        Route::post('search/sn', 'searchOrderBySN');
        Route::post('search/email', 'searchOrderByEmail');
        Route::post('search/browser', 'searchOrderByBrowser');
    });
    
    // 支付相关
    Route::prefix('pay')->controller('OrderController')->group(function () {
        Route::get('checkout/{orderSN}', 'bill')->name('pay.checkout');
    });
    
    // 文章
    Route::prefix('article')->controller('ArticleController')->group(function () {
        Route::get('/', 'listAll')->name('article.list');
        Route::get('{link}', 'show')->name('article.show');
    });
});

// 用户认证路由
Route::middleware('dujiaoka.boot')->namespace('Auth')->prefix('auth')->group(function () {
    Route::get('login', 'AuthController@showLogin')->name('login');
    Route::post('login', 'AuthController@login');
    Route::get('register', 'AuthController@showRegister')->name('register');
    Route::post('register', 'AuthController@register');
    Route::get('forgot-password', 'AuthController@showForgotPassword')->name('password.request');
    Route::post('forgot-password', 'AuthController@sendPasswordResetLink')->name('password.email');
    Route::get('reset-password/{token}', 'AuthController@showResetPassword')->name('password.reset');
    Route::post('reset-password', 'AuthController@resetPassword')->name('password.update');
    Route::post('logout', 'AuthController@logout')->name('logout');
    Route::post('email/verify', 'AuthController@verifyEmail')->name('verification.send');
});

// 用户中心路由
Route::middleware(['dujiaoka.boot', 'auth:web'])->namespace('User')->prefix('user')->group(function () {
    Route::get('center', 'UserCenterController@index')->name('user.center');
    Route::get('profile', 'UserCenterController@profile')->name('user.profile');
    Route::post('profile', 'UserCenterController@updateProfile');
    Route::get('change-password', 'UserCenterController@changePassword')->name('user.change-password');
    Route::post('change-password', 'UserCenterController@updatePassword');
    Route::get('orders', 'UserCenterController@orders')->name('user.orders');
    Route::get('orders/{orderSn}', 'UserCenterController@orderDetail')->name('user.order.detail');
    Route::get('balance', 'UserCenterController@balance')->name('user.balance');
    Route::get('recharge', 'UserCenterController@recharge')->name('user.recharge');
    Route::post('recharge', 'UserCenterController@processRecharge');
    Route::get('level', 'UserCenterController@levelInfo')->name('user.level');
});

Route::middleware('install.check')->namespace('Home')->prefix('install')->controller('HomeController')->group(function () {
    Route::get('/', 'install');
    Route::post('do', 'doInstall');
});

