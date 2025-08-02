<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SystemSettings extends Settings
{
    // 基础设置
    public string $title;
    public ?string $img_logo;
    public ?string $text_logo;
    public ?string $keywords;
    public ?string $description;
    public string $template;
    public string $language;
    public string $currency;
    public ?string $manage_email;
    public bool $is_open_anti_red;
    public bool $is_cn_challenge;
    public bool $is_open_search_pwd;
    public bool $is_open_google_translate;
    public ?string $notice;
    public ?string $footer;

    // 订单设置
    public int $order_expire_time;
    public bool $is_open_img_code;
    public int $order_ip_limits;

    // 推送设置
    public bool $is_open_server_jiang;
    public ?string $server_jiang_token;
    public bool $is_open_telegram_push;
    public ?string $telegram_bot_token;
    public ?string $telegram_userid;
    public bool $is_open_bark_push;
    public bool $is_open_bark_push_url;
    public ?string $bark_server;
    public ?string $bark_token;
    public bool $is_open_qywxbot_push;
    public ?string $qywxbot_key;

    // 邮件设置
    public string $driver;
    public ?string $host;
    public ?int $port;
    public ?string $username;
    public ?string $password;
    public ?string $encryption;
    public ?string $from_address;
    public ?string $from_name;

    // 极验设置
    public ?string $geetest_id;
    public ?string $geetest_key;
    public bool $is_open_geetest;

    public static function group(): string
    {
        return 'system';
    }
}