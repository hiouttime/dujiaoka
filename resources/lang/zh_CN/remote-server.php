<?php

return [
    "types" => [
        "http_server" => "HTTP请求",
        "rcon_command" => "RCON执行",
        "sql_execute" => "Mysql执行"
        ],
    'labels' => [
        'RemoteServer' => '回调服务器',
        'remote-server' => '回调服务器',
        'uid'   => '唯一标识',
    ],
    "fields" => [
        "remote-server" => "回调服务器",
        "type" => "回调类型",
        "http_url" => "URL地址",
        "http_method" => "HTTP方法",
        "http_return" => "成功的返回",
        "request" => "请求数据",
        "rcon_host" => "RCON地址",
        "rcon_port" => "RCON端口",
        "rcon_pass" => "RCON密码",
        "rcon_return" => "成功执行返回",
        "command" => "执行命令",
        "db_host" => "数据库地址",
        "db_user" => "数据库用户名",
        "db_pass" => "数据库密码",
        "db_name" => "数据库名",
        "db_sql" => "执行的SQL语句"
        ],
    "helps" => [
        "goods" => "仅自动处理的商品依赖回调事件来完成订单，您可以在 配置->回调服务器 进行设置。", 
        "type" => "您可以向您的应用开发者了解应该选择何种回调服务器",
        "http_method" => "一般地，此处应为GET或POST，可以根据实际开发需要更换。",
        "http_request" => "格式：key1=value1&key2=value2 ，发送的数据将会被url编码。",
        "http_return" => "请确保服务器仅返回此字符，有差异将视为处理失败。",
        "rcon_return" => "RCON返回内容请务必进行测试，避免出现格式不对造成匹配失败的情况。",
        "rcon_command" => "要执行的RCON命令，一行一个。",
        "uid" => "商品的其他输入框配置中的唯一标识"
        ],
    "you_can_use" => "你可以在执行的命令中使用如下变量："
    ];