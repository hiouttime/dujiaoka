<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
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
                'label' => 'bootstrap/cache目录可写',
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
    public static function testDatabase(array $config): array|bool
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
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * 测试Redis连接
     */
    public static function testRedis(array $config): bool
    {
        try {
            // 如果没有Redis扩展，返回false
            if (!extension_loaded('redis')) {
                return false;
            }
            
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
     * 生成环境配置文件
     */
    public static function generateEnvFile(array $data): string
    {
        try {
            $appKey = $data['app_key'] ?? 'base64:' . base64_encode(random_bytes(32));
            $config = [
                // 基础配置
                'APP_NAME' => $data['title'] ?? '独角数卡',
                'APP_ENV' => $data['app_env'] ?? 'production',
                'APP_KEY' => $appKey,
                'APP_DEBUG' => $data['app_debug'] ?? 'false',
                'APP_URL' => $data['app_url'] ?? 'http://localhost',
                '',
                'LOG_CHANNEL' => 'stack',
                '',
                '# 数据库配置',
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $data['db_host'] ?? '127.0.0.1',
                'DB_PORT' => $data['db_port'] ?? '3306',
                'DB_DATABASE' => $data['db_database'] ?? 'dujiaoka',
                'DB_USERNAME' => $data['db_username'] ?? 'root',
                'DB_PASSWORD' => $data['db_password'] ?? '',
                '',
                '# redis配置',
                'REDIS_HOST' => $data['redis_host'] ?? '127.0.0.1',
                'REDIS_PASSWORD' => empty($data['redis_password']) ? 'null' : $data['redis_password'],
                'REDIS_PORT' => $data['redis_port'] ?? '6379',
                '',
                'BROADCAST_DRIVER' => 'log',
                'SESSION_DRIVER' => 'redis',
                'SESSION_LIFETIME' => '120',
                '',
                '# 缓存配置',
                '# file为磁盘文件  redis为内存级别',
                '# redis为内存需要安装好redis服务端并配置',
                'CACHE_DRIVER' => 'redis',
                '',
                '# 异步消息队列',
                '# sync为同步  redis为异步',
                '# 使用redis异步需要安装好redis服务端并配置',
                'QUEUE_CONNECTION' => 'redis',
                '',
                '# 后台语言',
                '## zh_CN 简体中文',
                '## zh_TW 繁体中文',
                '## en    英文',
                'DUJIAO_ADMIN_LANGUAGE' => $data['admin_language'] ?? 'zh_CN',
                '',
                '# 后台登录地址',
                'ADMIN_ROUTE_PREFIX' => $data['admin_path'] ?? '/admin'
            ];
            
            // 构建.env内容
            $envContent = '';
            foreach ($config as $key => $value) {
                if ($key === '') {
                    $envContent .= "\n";
                } elseif (str_starts_with($key, '#')) {
                    $envContent .= $key . "\n";
                } else {
                    $envContent .= $key . '=' . $value . "\n";
                }
            }
            
            // 写入文件
            $basePath = dirname(dirname(dirname(__FILE__)));
            file_put_contents($basePath . '/.env', $envContent);
            
            return 'success';
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 执行安装
     */
    public static function install(array $data): string
    {
        try {
            $data['cache_driver'] = $data['cache_driver'] ?? 'redis';
            $data['queue_connection'] = $data['queue_connection'] ?? 'redis';
            
            $envResult = self::generateEnvFile($data);
            if ($envResult !== 'success') {
                return 'Failed to generate .env file: ' . $envResult;
            }

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

            // 执行数据库及初始化
            $installSql = file_get_contents(database_path('sql/install.sql'));
            DB::unprepared($installSql);
            file_put_contents(base_path('install.lock'), 'install ok');

            return 'success';
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}