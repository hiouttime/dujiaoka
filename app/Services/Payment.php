<?php
/**
 * The file was created by Assimon.
 *
 */

namespace App\Services;


use App\Models\Pay;

class Payment
{

    /**
     * 加载支付网关
     *
     * @param string|int $payClient 支付场景客户端
     * @return array|null
     *
     */
    public function pays(string $payClient = Pay::CLIENT_PC): ?array
    {
        $payGateway = Pay::query()
            ->whereIn('pay_client', [$payClient, Pay::CLIENT_ALL])
            ->where('enable', Pay::ENABLED)
            ->get();
        return $payGateway ? $payGateway->toArray() : null;
    }

    /**
     * 通过支付标识获得支付配置
     *
     * @param string $check 支付标识
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     *
     */
    public function detailByCheck(string $check)
    {
        $gateway = Pay::query()
            ->where('pay_check', $check)
            ->where('enable', Pay::ENABLED)
            ->first();
        return $gateway;
    }

    /**
     * 通过id查询支付网关
     *
     * @param int $id 支付网关id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     *
     */
    public function detail(int $id)
    {
        $gateway = Pay::query()
            ->where('id', $id)
            ->where('enable', Pay::ENABLED)
            ->first();
        return $gateway;
    }

}
