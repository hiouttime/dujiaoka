<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ShopSettings extends Settings
{
    // 基础设置（从SystemSettings移动过来）
    public string $title;
    public ?string $img_logo;
    public ?string $text_logo;
    public ?string $keywords;
    public ?string $description;
    public string $template;
    public string $language;
    public string $currency;
    public bool $is_open_anti_red;
    public bool $is_cn_challenge;
    public bool $is_open_search_pwd;
    public bool $is_open_google_translate;
    public ?string $notice;
    public ?string $footer;

    // 导航栏设置
    public array $nav_items;

    public static function group(): string
    {
        return 'shop';
    }
}