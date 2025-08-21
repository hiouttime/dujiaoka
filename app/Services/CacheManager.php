<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache as LaravelCache;

/**
 * 统一缓存服务
 */
class CacheManager
{
    /**
     * 商品详情缓存时间（秒）- 6小时
     */
    const GOODS_CACHE_TIME = 21600;

    /**
     * 订单详情缓存时间（秒）- 1小时
     */
    const ORDER_CACHE_TIME = 3600;

    /**
     * 统计数据缓存时间（秒）- 30分钟
     */
    const STATS_CACHE_TIME = 1800;

    /**
     * 生成商品缓存键
     */
    public static function goodsKey(int $id): string
    {
        return "goods_detail_{$id}";
    }

    /**
     * 生成订单缓存键
     */
    public static function orderKey(string $orderSn): string
    {
        return "order_detail_{$orderSn}";
    }

    /**
     * 生成统计缓存键
     */
    public static function statsKey(string $type): string
    {
        return "admin_stats_{$type}";
    }

    /**
     * 缓存商品数据
     */
    public static function rememberGoods(int $id, \Closure $callback)
    {
        return LaravelCache::remember(
            self::goodsKey($id),
            self::GOODS_CACHE_TIME,
            $callback
        );
    }

    /**
     * 缓存订单数据
     */
    public static function rememberOrder(string $orderSn, \Closure $callback)
    {
        return LaravelCache::remember(
            self::orderKey($orderSn),
            self::ORDER_CACHE_TIME,
            $callback
        );
    }

    /**
     * 缓存统计数据
     */
    public static function rememberStats(string $type, \Closure $callback)
    {
        return LaravelCache::remember(
            self::statsKey($type),
            self::STATS_CACHE_TIME,
            $callback
        );
    }

    /**
     * 清除商品缓存
     */
    public static function forgetGoods(int $id): void
    {
        LaravelCache::forget(self::goodsKey($id));
    }

    /**
     * 清除订单缓存
     */
    public static function forgetOrder(string $orderSn): void
    {
        LaravelCache::forget(self::orderKey($orderSn));
    }

    /**
     * 清除统计缓存
     */
    public static function forgetStats(string $type): void
    {
        LaravelCache::forget(self::statsKey($type));
    }

    /**
     * 清除所有统计缓存
     */
    public static function forgetAllStats(): void
    {
        LaravelCache::forget(self::statsKey('overview'));
        LaravelCache::forget(self::statsKey('daily'));
        LaravelCache::forget(self::statsKey('monthly'));
    }

    /**
     * 清除邮件模板缓存
     */
    public static function forgetEmailTemplate(string $token): void
    {
        LaravelCache::forget("email_template_{$token}");
    }

    /**
     * 清除所有邮件模板缓存
     */
    public static function forgetAllEmailTemplates(): void
    {
        LaravelCache::forget('email_template_pending_order');
        LaravelCache::forget('email_template_completed_order');
        LaravelCache::forget('email_template_failed_order');
        LaravelCache::forget('email_template_manual_send_manage_mail');
        LaravelCache::forget('email_template_card_send_user_email');
    }

    /**
     * 清除支付方式缓存
     */
    public static function forgetPayMethods(): void
    {
        LaravelCache::forget('enabled_pay_methods');
        // 注意：单个支付方式缓存会在具体编辑时通过 forgetPayMethod() 清除
    }

    /**
     * 清除单个支付方式缓存
     */
    public static function forgetPayMethod(int $payId): void
    {
        LaravelCache::forget("pay_method_{$payId}");
    }

    /**
     * 清除商品相关缓存（扩展原有方法）
     */
    public static function forgetGoodsWithSub(int $goodsId): void
    {
        LaravelCache::forget("goods_with_sub_{$goodsId}");
        self::forgetGoods($goodsId); // 清除原有商品缓存
    }
}