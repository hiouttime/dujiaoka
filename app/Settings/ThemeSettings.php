<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ThemeSettings extends Settings
{
    public ?string $notice;
    public bool $invert_logo;

    public static function group(): string
    {
        return 'theme';
    }
}