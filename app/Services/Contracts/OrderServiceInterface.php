<?php

namespace App\Services\Contracts;

interface OrderServiceInterface
{
    public function validateCreateOrder(\Illuminate\Http\Request $request): void;
    public function validateGoods(\Illuminate\Http\Request $request): \App\Models\Goods;
    public function validateCoupon(\Illuminate\Http\Request $request): ?\App\Models\Coupon;
    public function detailOrderSN(string $orderSN): ?\App\Models\Order;
    public function expiredOrderSN(string $orderSN): bool;
    public function createOrder(array $data): \App\Models\Order;
}