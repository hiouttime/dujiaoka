<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class MailSettings extends Settings
{
    // 邮件基础配置
    public string $driver = 'smtp';
    public ?string $host = null;
    public ?int $port = 465;
    public ?string $username = null;
    public ?string $password = null;
    public ?string $encryption = 'ssl';
    public ?string $from_address = null;
    public ?string $from_name = '独角发卡';
    public ?string $manage_email = null;

    public static function group(): string
    {
        return 'mail';
    }
}