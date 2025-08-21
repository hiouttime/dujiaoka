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
        Schema::create('article_goods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('article_id')->comment('文章ID');
            $table->integer('goods_id')->comment('商品ID');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
            
            $table->unique(['article_id', 'goods_id']);
            $table->index('article_id');
            $table->index('goods_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_goods');
    }
};
