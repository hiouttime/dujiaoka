<?php

namespace Database\Seeders;

use App\Settings\SystemSettings;
use App\Settings\ThemeSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 从原有的缓存获取设置数据并迁移到新的 Settings 系统
        $systemSettings = Cache::get('system-setting', []);
        $themeSettings = Cache::get('theme-setting', []);

        // 初始化系统设置默认值
        $systemDefaults = [
            'title' => '独角兽商城',
            'img_logo' => null,
            'text_logo' => '独角兽',
            'keywords' => '',
            'description' => '',
            'template' => 'default',
            'language' => 'zh_CN',
            'currency' => 'CNY',
            'manage_email' => '',
            'is_open_anti_red' => false,
            'is_cn_challenge' => true,
            'is_open_search_pwd' => false,
            'is_open_google_translate' => false,
            'notice' => '',
            'footer' => '',
            'order_expire_time' => 5,
            'is_open_img_code' => false,
            'order_ip_limits' => 1,
            'is_open_server_jiang' => false,
            'server_jiang_token' => '',
            'is_open_telegram_push' => false,
            'telegram_bot_token' => '',
            'telegram_userid' => '',
            'is_open_bark_push' => false,
            'is_open_bark_push_url' => false,
            'bark_server' => '',
            'bark_token' => '',
            'is_open_qywxbot_push' => false,
            'qywxbot_key' => '',
            'driver' => 'smtp',
            'host' => '',
            'port' => 587,
            'username' => '',
            'password' => '',
            'encryption' => '',
            'from_address' => '',
            'from_name' => '',
            'geetest_id' => '',
            'geetest_key' => '',
            'is_open_geetest' => false,
        ];

        // 合并缓存中的值和默认值
        $finalSystemSettings = array_merge($systemDefaults, $systemSettings);

        // 批量插入系统设置
        $systemPayload = [];
        foreach ($finalSystemSettings as $key => $value) {
            $systemPayload[] = [
                'group' => 'system',
                'name' => $key,
                'payload' => json_encode($value),
                'locked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 主题设置默认值
        $themeDefaults = [
            'notice' => '',
            'invert_logo' => false,
        ];

        $finalThemeSettings = array_merge($themeDefaults, $themeSettings);

        // 批量插入主题设置
        $themePayload = [];
        foreach ($finalThemeSettings as $key => $value) {
            $themePayload[] = [
                'group' => 'theme',
                'name' => $key,
                'payload' => json_encode($value),
                'locked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // 删除现有设置
        \DB::table('settings')->where('group', 'system')->delete();
        \DB::table('settings')->where('group', 'theme')->delete();

        // 插入新设置
        \DB::table('settings')->insert(array_merge($systemPayload, $themePayload));

        $this->command->info('Settings migrated successfully!');
    }
}
