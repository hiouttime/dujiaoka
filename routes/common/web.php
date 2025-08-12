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
    Route::get('/', 'HomeController@index');
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
    
    // 文章
    Route::prefix('article')->controller('ArticleController')->group(function () {
        Route::get('/', 'listAll');
        Route::get('{link}', 'show');
    });
});

Route::middleware('install.check')->namespace('Home')->prefix('install')->controller('HomeController')->group(function () {
    Route::get('/', 'install');
    Route::post('do', 'doInstall');
});

