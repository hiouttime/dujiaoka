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
        // 更新shop_decoration组为shop组
        DB::table('settings')
            ->where('group', 'shop_decoration')
            ->update(['group' => 'shop']);

        // 更新主题设置字段名
        DB::table('settings')
            ->where('group', 'theme')
            ->where('name', 'carousel_texts')
            ->update(['name' => 'notices']);

        DB::table('settings')
            ->where('group', 'theme')  
            ->where('name', 'carousel_banners')
            ->update(['name' => 'banners']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 回滚shop组为shop_decoration组
        DB::table('settings')
            ->where('group', 'shop')
            ->update(['group' => 'shop_decoration']);

        // 回滚主题设置字段名
        DB::table('settings')
            ->where('group', 'theme')
            ->where('name', 'notices')
            ->update(['name' => 'carousel_texts']);

        DB::table('settings')
            ->where('group', 'theme')
            ->where('name', 'banners') 
            ->update(['name' => 'carousel_banners']);
    }
};
