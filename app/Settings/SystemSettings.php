<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SystemSettings extends Settings
{
    // 订单设置
    public int $order_expire_time = 1800;
    public bool $is_open_img_code = false;
    public int $order_ip_limits = 3;
    public string $contact_required = 'email';
    public int $stock_mode = 2;

    // 推送设置
    public bool $is_open_server_jiang = false;
    public ?string $server_jiang_token = null;
    public bool $is_open_telegram_push = false;
    public ?string $telegram_bot_token = null;
    public ?string $telegram_userid = null;
    public bool $is_open_bark_push = false;
    public bool $is_open_bark_push_url = false;
    public ?string $bark_server = null;
    public ?string $bark_token = null;
    public bool $is_open_qywxbot_push = false;
    public ?string $qywxbot_key = null;

    // 极验设置
    public ?string $geetest_id = null;
    public ?string $geetest_key = null;
    public bool $is_open_geetest = false;

    public static function group(): string
    {
        return 'system';
    }
}