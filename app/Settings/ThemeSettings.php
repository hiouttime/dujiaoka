<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ThemeSettings extends Settings
{
    // 顶部轮播文案
    public ?string $notices = null;
    
    // 首页轮播图
    public array $banners = [];
    
    // Logo反色
    public bool $invert_logo = false;

    public static function group(): string
    {
        return 'theme';
    }
}