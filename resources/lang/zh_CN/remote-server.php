<?php
/**
 * The file was created by Outtime.
 *
 * @author    outtime<beprivacy@icloud.com>
 * @copyright outtime<beprivacy@icloud.com>
 * @link      https://outti.me
 */

return [
    "types" => [
        "http_server" => "HTTP请求",
        "rcon_command" => "RCON执行",
        "sql_execute" => "Mysql执行"
        ],
    'labels' => [
        'RemoteServer' => '回调服务器',
        'remote_server' => '回调服务器',
    ],
    "fields" => [
        "name" => "服务器名称",
        "type" => "回调类型",
        "url" => "URL地址",
        "host" => "主机地址",
        "port" => "端口",
        "username" => "用户名",
        "password" => "密码",
        "database" => "数据库名",
        "command" => "执行命令",
        "headers" => "请求头",
        "body" => "请求体",
        "is_active" => "是否启用",
        "description" => "描述",
    ],
    "helps" => [
        "command" => "要执行的命令，支持变量替换",
        "headers" => "HTTP请求头，格式：key: value",
        "body" => "HTTP请求体，支持JSON格式",
        "type" => "您可以向您的应用开发者了解应该选择何种回调服务器",
    ],
    "you_can_use" => "你可以在执行的命令中使用如下变量："
    ];