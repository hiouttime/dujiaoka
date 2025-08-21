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
        "http_server" => "HTTP請求",
        "rcon_command" => "RCON執行",
        "sql_execute" => "Mysql執行"
        ],
    'labels' => [
        'RemoteServer' => '回調服務器',
        'remote_server' => '回調服務器',
    ],
    "fields" => [
        "name" => "服務器名稱",
        "type" => "回調類型",
        "url" => "URL地址",
        "host" => "主機地址",
        "port" => "端口",
        "username" => "用戶名",
        "password" => "密碼",
        "database" => "數據庫名",
        "command" => "執行命令",
        "headers" => "請求頭",
        "body" => "請求體",
        "is_active" => "是否啟用",
        "description" => "描述",
    ],
    "helps" => [
        "command" => "要執行的命令，支持變量替換",
        "headers" => "HTTP請求頭，格式：key: value",
        "body" => "HTTP請求體，支持JSON格式",
        "type" => "您可以向您的應用開發者了解應該選擇何種回調服務器",
    ],
    "you_can_use" => "你可以在執行的命令中使用如下變量："
    ];