<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->string('picture_url')->nullable()->comment('图片URL地址');
            $table->integer('buy_min_num')->default(1)->comment('最小购买数量');
            $table->text('usage_instructions')->nullable()->comment('使用说明');
            $table->json('customer_form_fields')->nullable()->comment('客户输入表单配置');
            $table->json('wholesale_prices')->nullable()->comment('批发价格配置（新格式）');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->dropColumn([
                'picture_url',
                'buy_min_num', 
                'usage_instructions',
                'customer_form_fields',
                'wholesale_prices'
            ]);
        });
    }
};
