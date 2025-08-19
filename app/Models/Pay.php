<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Pay extends BaseModel
{

    use SoftDeletes;

    protected $table = 'pays';

    // 状态常量
    const ENABLED = 1;
    const DISABLED = 0;

    // 支付方式
    const METHOD_JUMP = 1;  // 跳转
    const METHOD_SCAN = 2;  // 扫码

    // 客户端类型
    const CLIENT_PC = 1;     // 电脑
    const CLIENT_MOBILE = 2; // 手机  
    const CLIENT_ALL = 3;    // 通用

    public static function getMethodMap()
    {
        return [
            self::METHOD_JUMP => __('pay.fields.method_jump'),
            self::METHOD_SCAN => __('pay.fields.method_scan'),
        ];
    }

    public static function getClientMap()
    {
        return [
            self::CLIENT_PC => __('pay.fields.pay_client_pc'),
            self::CLIENT_MOBILE => __('pay.fields.pay_client_mobile'),
            self::CLIENT_ALL => __('pay.fields.pay_client_all'),
        ];
    }

    // 作用域：获取启用的支付方式
    public function scopeEnabled($query)
    {
        return $query->where('enable', self::ENABLED);
    }

    // 作用域：根据客户端类型筛选
    public function scopeForClient($query, $client)
    {
        return $query->whereIn('pay_client', [$client, self::CLIENT_ALL]);
    }

}
