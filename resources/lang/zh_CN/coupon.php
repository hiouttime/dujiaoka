<?php

return [
    'labels' => [
        'Coupon' => '优惠码',
        'coupon' => '优惠码',
    ],
    'fields' => [
        'type' => '优惠类型',
        'discount' => '优惠金额/系数',
        'is_open' => '是否启用',
        'coupon' => '优惠码',
        'ret' => '剩余使用次数',
        'type_one_time' => '一次性使用',
        'type_repeat' => '重复使用',
        'type_percent' => '系数折扣 (0-1)',
        'type_fixed' => '整体固定金额',
        'type_each' => '每件固定金额',
        'goods_id' => '可用商品'
    ],
    'options' => [
    ],
    'helps' =>[
        'discount' => '系数折扣：价格在下单时会乘以这个数字，0.9就是九折。固定金额：下单时价格会直接减去。'
    ]
];
