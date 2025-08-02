<?php

namespace App\PaymentGateways\Contracts;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Pay;

/**
 * 支付驱动接口
 * 所有支付驱动必须实现此接口
 */
interface PaymentDriverInterface
{
    /**
     * 支付网关处理
     *
     * @param string $payway 支付方式
     * @param string $orderSN 订单号
     * @param Order $order 订单模型
     * @param Pay $payGateway 支付网关配置
     * @return mixed
     */
    public function gateway(string $payway, string $orderSN, Order $order, Pay $payGateway);

    /**
     * 异步通知处理
     *
     * @param Request $request
     * @return string 返回给第三方的响应
     */
    public function notify(Request $request): string;

    /**
     * 验证支付结果
     *
     * @param array $config 支付配置
     * @param Request $request 请求数据
     * @return array 验证结果
     */
    public function verify(array $config, Request $request): array;

    /**
     * 获取支持的支付方式
     *
     * @return array
     */
    public function getSupportedPayways(): array;

    /**
     * 获取驱动名称
     *
     * @return string
     */
    public function getName(): string;

    /**
     * 获取驱动显示名称
     *
     * @return string
     */
    public function getDisplayName(): string;
}