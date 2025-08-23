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
        // 添加库存模式配置到系统设置
        DB::table('settings')->insert([
            'group' => 'system',
            'name' => 'stock_mode',
            'payload' => json_encode(2), // 默认：发货时减库存
            'locked' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')
            ->where('group', 'system')
            ->where('name', 'stock_mode')
            ->delete();
    }
};