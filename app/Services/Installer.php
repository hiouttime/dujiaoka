<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Encryption\Encrypter;
use Exception;

class Installer
{
    /**
     * 检查环境要求
     */
    public static function checkEnvironment(): array
    {
        $checks = [
            'php_version' => [
                'label' => 'PHP版本 >= 8.2',
                'status' => version_compare(PHP_VERSION, '8.2.0', '>='),
                'message' => 'PHP ' . PHP_VERSION
            ],
            'redis_extension' => [
                'label' => 'Redis扩展',
                'status' => extension_loaded('redis'),
                'message' => extension_loaded('redis') ? '已安装' : '未安装'
            ],
            'proc_open' => [
                'label' => 'proc_open函数',
                'status' => function_exists('proc_open'),
                'message' => function_exists('proc_open') ? '可用' : '被禁用'
            ],
            'pcntl_signal' => [
                'label' => 'pcntl_signal函数',
                'status' => function_exists('pcntl_signal'),
                'message' => function_exists('pcntl_signal') ? '可用' : '被禁用'
            ],
            'pcntl_alarm' => [
                'label' => 'pcntl_alarm函数',
                'status' => function_exists('pcntl_alarm'),
                'message' => function_exists('pcntl_alarm') ? '可用' : '被禁用'
            ],
            'putenv' => [
                'label' => 'putenv函数',
                'status' => function_exists('putenv'),
                'message' => function_exists('putenv') ? '可用' : '被禁用'
            ],
            'storage_writable' => [
                'label' => 'storage目录可写',
                'status' => is_writable(storage_path()),
                'message' => is_writable(storage_path()) ? '可写' : '不可写'
            ],
            'bootstrap_writable' => [
                'label' => 'bootstrap/cache目录可写 (建议)',
                'status' => is_writable(base_path('bootstrap/cache')),
                'message' => is_writable(base_path('bootstrap/cache')) ? '可写' : '不可写',
                'required' => false
            ],
        ];

        return $checks;
    }

    /**
     * 测试数据库连接
     */
    public static function testDatabase(array $config): bool
    {
        try {
            config(['database.connections.test' => [
                'driver' => 'mysql',
                'host' => $config['host'],
                'port' => $config['port'],
                'database' => $config['database'],
                'username' => $config['username'],
                'password' => $config['password'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
            ]]);

            DB::connection('test')->select('SELECT 1');
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 测试Redis连接
     */
    public static function testRedis(array $config): bool
    {
        try {
            $redis = new \Redis();
            $redis->connect($config['host'], $config['port']);
            if (!empty($config['password']) && $config['password'] !== 'null') {
                $redis->auth($config['password']);
            }
            $redis->ping();
            $redis->close();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 执行安装
     */
    public static function install(array $data): string
    {
        try {
            // 生成应用密钥
            $appKey = 'base64:' . base64_encode(Encrypter::generateKey(config('app.cipher')));
            
            // 读取环境文件模板
            $envTemplate = file_get_contents(base_path('.env.example'));
            
            // 替换变量
            $envContent = str_replace([
                '{title}',
                '{app_key}',
                '{app_url}',
                '{db_host}',
                '{db_port}',
                '{db_database}',
                '{db_username}',
                '{db_password}',
                '{redis_host}',
                '{redis_password}',
                '{redis_port}',
                '{admin_path}',
            ], [
                $data['title'],
                $appKey,
                $data['app_url'],
                $data['db_host'],
                $data['db_port'],
                $data['db_database'],
                $data['db_username'],
                $data['db_password'],
                $data['redis_host'],
                $data['redis_password'] ?: 'null',
                $data['redis_port'],
                $data['admin_path'],
            ], $envTemplate);

            // 写入环境文件
            file_put_contents(base_path('.env'), $envContent);

            // 重新配置数据库和Redis
            config(['database.connections.mysql' => [
                'driver' => 'mysql',
                'host' => $data['db_host'],
                'port' => $data['db_port'],
                'database' => $data['db_database'],
                'username' => $data['db_username'],
                'password' => $data['db_password'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
            ]]);

            DB::purge('mysql');

            // 执行数据库初始化
            $installSql = file_get_contents(database_path('sql/install.sql'));
            DB::unprepared($installSql);

            // 创建安装锁文件
            file_put_contents(base_path('install.lock'), 'install ok');

            return 'success';
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}