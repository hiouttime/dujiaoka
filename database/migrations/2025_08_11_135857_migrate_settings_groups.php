<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 基础设置字段，从 system 组迁移到 shop_decoration 组
        $basicFields = [
            'title',
            'img_logo', 
            'text_logo',
            'keywords',
            'description',
            'template',
            'language',
            'currency',
            'is_open_anti_red',
            'is_cn_challenge',
            'is_open_search_pwd',
            'is_open_google_translate',
            'notice',
            'footer'
        ];

        // 迁移基础设置到店铺组
        foreach ($basicFields as $field) {
            DB::table('settings')
                ->where('group', 'system')
                ->where('name', $field)
                ->update(['group' => 'shop']);
        }

        // 添加新的导航栏设置
        DB::table('settings')->updateOrInsert(
            ['group' => 'shop', 'name' => 'nav_items'],
            [
                'locked' => false,
                'payload' => json_encode([
                    [
                        'name' => '主页',
                        'url' => '/',
                        'target_blank' => false,
                        'children' => []
                    ],
                    [
                        'name' => '联系客服', 
                        'url' => '#',
                        'target_blank' => false,
                        'children' => [
                            [
                                'name' => '站点客服',
                                'url' => 'https://t.me/riniba',
                                'target_blank' => true
                            ],
                            [
                                'name' => 'Telegram客服',
                                'url' => 'https://t.me/riniba', 
                                'target_blank' => true
                            ]
                        ]
                    ],
                    [
                        'name' => '站点公告',
                        'url' => '#',
                        'target_blank' => false,
                        'children' => []
                    ],
                    [
                        'name' => '订单查询',
                        'url' => '/order-search',
                        'target_blank' => false,
                        'children' => []
                    ]
                ])
            ]
        );

        // 添加主题设置的默认值
        DB::table('settings')->updateOrInsert(
            ['group' => 'theme', 'name' => 'notices'],
            [
                'locked' => false,
                'payload' => json_encode("欢迎使用我们的服务！\n限时优惠，立即购买享受折扣\n24小时客服在线，随时为您服务\n优质产品，值得信赖")
            ]
        );

        DB::table('settings')->updateOrInsert(
            ['group' => 'theme', 'name' => 'banners'],
            [
                'locked' => false,
                'payload' => json_encode([])
            ]
        );

        DB::table('settings')->updateOrInsert(
            ['group' => 'theme', 'name' => 'invert_logo'],
            [
                'locked' => false,
                'payload' => json_encode(false)
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 将店铺设置迁移回系统设置
        $basicFields = [
            'title',
            'img_logo', 
            'text_logo',
            'keywords',
            'description',
            'template',
            'language',
            'currency',
            'is_open_anti_red',
            'is_cn_challenge',
            'is_open_search_pwd',
            'is_open_google_translate',
            'notice',
            'footer'
        ];

        foreach ($basicFields as $field) {
            DB::table('settings')
                ->where('group', 'shop')
                ->where('name', $field)
                ->update(['group' => 'system']);
        }

        // 删除新增的设置
        DB::table('settings')
            ->where('group', 'shop')
            ->where('name', 'nav_items')
            ->delete();

        DB::table('settings')
            ->where('group', 'theme')
            ->whereIn('name', ['notices', 'banners', 'invert_logo'])
            ->delete();
    }
};
