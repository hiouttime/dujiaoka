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
     * 库存锁定缓存时间（秒）- 30分钟（与订单过期时间相关）
     */
    const STOCK_LOCK_TIME = 1800;

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

    /**
     * 生成库存锁定缓存键
     */
    public static function stockLockKey(int $subId): string
    {
        return "stock_lock_{$subId}";
    }

    /**
     * 生成订单库存锁定缓存键
     */
    public static function orderStockLockKey(string $orderSn): string
    {
        return "order_stock_lock_{$orderSn}";
    }

    /**
     * 简单锁定库存（下单即减库存模式）
     */
    public static function lockStock(int $subId, int $quantity, string $orderSn): bool
    {
        $lockKey = self::stockLockKey($subId);
        $orderStockKey = self::orderStockLockKey($orderSn);
        
        // 记录锁定数量
        LaravelCache::increment($lockKey, $quantity);
        
        // 记录订单锁定的商品信息（用于释放）
        $orderStock = LaravelCache::get($orderStockKey, []);
        $orderStock[] = ['sub_id' => $subId, 'quantity' => $quantity];
        LaravelCache::put($orderStockKey, $orderStock, self::STOCK_LOCK_TIME);
        
        return true;
    }

    /**
     * 简单释放库存锁定（订单过期或取消）
     */
    public static function unlockStock(string $orderSn): bool
    {
        $orderStockKey = self::orderStockLockKey($orderSn);
        $orderStock = LaravelCache::get($orderStockKey, []);
        
        foreach ($orderStock as $item) {
            $lockKey = self::stockLockKey($item['sub_id']);
            LaravelCache::decrement($lockKey, $item['quantity']);
        }
        
        LaravelCache::forget($orderStockKey);
        return true;
    }

    /**
     * 获取已锁定的库存数量
     */
    public static function getLockedStock(int $subId): int
    {
        return (int) LaravelCache::get(self::stockLockKey($subId), 0);
    }

    /**
     * 检查库存是否足够（考虑锁定库存）
     */
    public static function checkStockAvailable(int $subId, int $requestQuantity, int $actualStock): bool
    {
        $availableStock = $actualStock - self::getLockedStock($subId);
        return $availableStock >= $requestQuantity;
    }
}