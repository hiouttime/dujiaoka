<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 创建新的订单表结构
        Schema::create('orders_new', function (Blueprint $table) {
            $table->id();
            $table->string('order_sn', 150)->unique()->comment('订单号');
            $table->string('email', 200)->comment('下单邮箱');
            $table->decimal('total_price', 10, 2)->default(0)->comment('订单总价');
            $table->decimal('actual_price', 10, 2)->default(0)->comment('实际支付价格');
            $table->decimal('coupon_discount_price', 10, 2)->default(0)->comment('优惠券折扣');
            $table->tinyInteger('status')->default(1)->comment('订单状态：1待支付 2待处理 3处理中 4已完成 5失败 6异常 -1过期');
            $table->integer('pay_id')->nullable()->comment('支付方式ID');
            $table->string('search_pwd', 200)->default('')->comment('查询密码');
            $table->string('buy_ip', 50)->comment('下单IP');
            $table->string('trade_no', 200)->default('')->comment('第三方支付订单号');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('order_sn');
            $table->index('email');
            $table->index('status');
        });

        // 创建订单商品明细表
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->integer('goods_id')->comment('商品ID');
            $table->integer('sub_id')->nullable()->comment('商品规格ID');
            $table->string('goods_name', 200)->comment('商品名称');
            $table->decimal('unit_price', 10, 2)->comment('商品单价');
            $table->integer('quantity')->comment('购买数量');
            $table->decimal('subtotal', 10, 2)->comment('小计金额');
            $table->text('info')->nullable()->comment('商品详情/卡密信息');
            $table->tinyInteger('type')->default(1)->comment('商品类型：1自动发货 2人工处理');
            $table->timestamps();
            
            $table->foreign('order_id')->references('id')->on('orders_new')->onDelete('cascade');
            $table->index('goods_id');
            $table->index('order_id');
        });

        // 备份原表
        Schema::rename('orders', 'orders_backup');
        Schema::rename('orders_new', 'orders');
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
        Schema::rename('orders', 'orders_new');
        Schema::rename('orders_backup', 'orders');
        Schema::dropIfExists('orders_new');
    }
};