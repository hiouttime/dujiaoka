<?php

return [
    'labels' => [
        'Goods' => '商品',
        'goods' => '商品',
        'soldout' => '已售完'
    ],
    'fields' => [
        'sell_price' => '售价',
        'preselection' => '自选加价',
        'group_id' => '所属分类',
        'api_hook' => '回调事件',
        'buy_prompt' => '购买提示',
        'description' => '商品描述',
        'gd_name' => '商品名称',
        'gd_description' => '商品描述',
        'gd_keywords' => '商品关键字',
        'in_stock' => '库存',
        'ord' => '排序权重',
        'other_ipu_cnf' => '其他输入框配置',
        'picture' => '商品图片',
        'picture_url' => '商品图片URL',
        'sales_volume' => '销量',
        'type' => '商品类型',
        'buy_limit_num' => '限制单次购买最大数量',
        'wholesale_price_cnf' => '批发价配置',
        'automatic_delivery' => '自动发货',
        'manual_processing' => '人工处理',
        'is_open' => '是否上架',
        'coupon_id' => '可用优惠码',
        'payment_limit' => '限制支付方式'
    ],
    'options' => [
    ],
    'helps' => [
        'picture' => '可不上传，为默认图片',
        'picture_url' => '输入站外图片链接，将自动替换商品图片。',
        'in_stock' => '当商品类型为"人工处理"时，手动填写的库存数量才会生效。"自动发货"类型的商品系统会自动识别库存数量',
        'buy_limit_num' => '防止恶意刷库存，0为不限制客户单次下单最大数量',
        'other_ipu_cnf' => '格式为<code>唯一标识(英文)=输入框名字=是否必填</code>，例如：填写 <code>qq_account=QQ账号=true</code> 表示产品详情页会新增一个 <code>QQ账号</code> 输入框。true 为必填，false 为选填。（一行一个）',
        'wholesale_price_cnf' => '例如：填写 5=3 表示客户购买 5 件或以上时，每件价格为 3 元。一行一个',
        'payment_limit' => '仅允许使用这些支付方式购买此商品，若为空，则支持全部已启用的的支付方式',
        'preselection' => '自动发货的商品支持在下单时预先选择想要的卡密，填写一个价格则代表开启自选加价。需在卡密处完成设置。'
    ]
];
