<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');
    $router->resource('goods', 'GoodsController');
    $router->group(['prefix' => 'goods_api'], function (Router $router) {
        $router->get('goods_sub', 'GoodsController@getGoodsSub');
    });
    $router->resource('goods-group', 'GoodsGroupController');
    $router->resource('carmis', 'CarmisController');
    $router->resource('coupon', 'CouponController');
    $router->resource('emailtpl', 'EmailtplController');
    $router->resource('pay', 'PayController');
    $router->resource('order', 'OrderController');
    $router->resource('article', 'ArticleController');
    $router->resource('remote-server', 'RemoteServerController');
    $router->get('import-carmis', 'CarmisController@importCarmis');
    $router->get('email-test', 'EmailTestController@emailTest');
    $router->get('system-setting', 'SystemSettingController@systemSetting');
    $router->get('theme-setting', 'ThemeSettingController@themeSetting');
});
