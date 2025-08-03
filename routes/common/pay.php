<?php
/**
 * The file was created by Assimon.
 *
 * @author    assimon<ashang@utf8.hk>
 * @copyright assimon<ashang@utf8.hk>
 * @link      http://utf8.hk/
 */
use Illuminate\Support\Facades\Route;

// 支付网关入口
Route::get('pay/{driver}/{payway}/{orderSN}', 'UnifiedPaymentController@gateway')
    ->middleware('dujiaoka.pay_gate_way');

// 支付回调统一入口  
Route::post('pay/{driver}/notify', 'UnifiedPaymentController@notify');
Route::get('pay/{driver}/return', 'UnifiedPaymentController@return');
