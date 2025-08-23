<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ShopSettings extends Settings
{
    // 基础设置（从SystemSettings移动过来）
    public string $title = '独角数卡';
    public ?string $img_logo = null;
    public ?string $text_logo = null;
    public ?string $keywords = '独角数卡,虚拟商品,自动发货';
    public ?string $description = '独角数卡 - 专业的虚拟商品自动发货平台';
    public string $template = 'morpho';
    public string $language = 'zh-CN';
    public string $currency = 'CNY';
    public bool $is_open_anti_red = false;
    public bool $is_cn_challenge = false;
    public bool $is_open_search_pwd = true;
    public bool $is_open_google_translate = false;
    public ?string $notice = null;
    public ?string $footer = null;

    // 导航栏设置
    public array $nav_items = [];

    public static function group(): string
    {
        return 'shop';
    }
}