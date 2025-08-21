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
        // 使用Laravel Settings包的方式添加默认值
        DB::table('settings')->updateOrInsert(
            ['group' => 'system', 'name' => 'contact_required'],
            ['payload' => '"email"', 'locked' => false]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where([
            'group' => 'system',
            'name' => 'contact_required'
        ])->delete();
    }
};
