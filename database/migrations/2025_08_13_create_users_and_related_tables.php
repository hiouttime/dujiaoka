<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. 创建用户表
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('nickname', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->decimal('balance', 10, 2)->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->unsignedTinyInteger('level_id')->default(1);
            $table->tinyInteger('status')->default(1)->comment('1:正常 2:禁用');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            $table->index(['email', 'status']);
            $table->index('level_id');
        });

        // 2. 创建用户等级表
        Schema::create('user_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->decimal('min_spent', 10, 2)->default(0);
            $table->decimal('discount_rate', 3, 2)->default(1.00)->comment('折扣率，1.00=无折扣，0.95=95折');
            $table->string('color', 7)->default('#6b7280');
            $table->text('description')->nullable();
            $table->tinyInteger('sort')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            
            $table->index('min_spent');
        });

        // 3. 创建余额记录表
        Schema::create('user_balance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type', 20)->comment('recharge:充值 consume:消费 refund:退款 admin:管理员调整');
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_before', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->string('description')->nullable();
            $table->string('related_order_sn')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'type']);
            $table->index('related_order_sn');
        });

        // 4. 创建密码重置表
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 5. 修改orders表添加用户关联
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('order_sn');
            $table->decimal('user_discount_rate', 3, 2)->default(1.00)->after('coupon_discount_price');
            $table->decimal('user_discount_amount', 10, 2)->default(0)->after('user_discount_rate');
            $table->tinyInteger('payment_method')->default(1)->after('pay_id')->comment('1:在线支付 2:余额支付 3:混合支付');
            $table->decimal('balance_used', 10, 2)->default(0)->after('payment_method');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index('user_id');
        });

        // 6. 插入默认用户等级
        DB::table('user_levels')->insert([
            [
                'id' => 1,
                'name' => '普通用户',
                'min_spent' => 0,
                'discount_rate' => 1.00,
                'color' => '#6b7280',
                'description' => '默认用户等级',
                'sort' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'VIP用户',
                'min_spent' => 100,
                'discount_rate' => 0.95,
                'color' => '#f59e0b',
                'description' => '消费满100元自动升级，享受95折优惠',
                'sort' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => '钻石用户',
                'min_spent' => 500,
                'discount_rate' => 0.90,
                'color' => '#8b5cf6',
                'description' => '消费满500元自动升级，享受9折优惠',
                'sort' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'user_discount_rate', 'user_discount_amount', 'payment_method', 'balance_used']);
        });
        
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('user_balance_records');
        Schema::dropIfExists('user_levels');
        Schema::dropIfExists('users');
    }
};