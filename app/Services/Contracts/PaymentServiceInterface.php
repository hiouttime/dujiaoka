<?php

namespace App\Services\Contracts;

interface PaymentServiceInterface
{
    public function detail(int $payId): ?\App\Models\Pay;
    public function getAvailableGateways(): array;
    public function processPayment(string $gateway, array $data): array;
}