<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class RemoteServer extends BaseModel
{

    const HTTP_SERVER = 1; // http请求
    const RCON_COMMAND = 2; // rcon命令
    const SQL_EXECUTE = 3; // mysql执行
    

    protected $table = 'remote_servers';

    /**
     * 获取类型映射
     *
     * @return array
     *
     * @author    outtime<i@treeo.cn>
     * @copyright outtime<i@treeo.cn>
     * @link      https://outti.me
     */
    public static function getServerTypeMap()
    {
        return [
            self::HTTP_SERVER => admin_trans('remote-server.types.http_server'),
            self::RCON_COMMAND => admin_trans('remote-server.types.rcon_command'),
            self::SQL_EXECUTE => admin_trans('remote-server.types.sql_execute'),
        ];
    }
    
}
