SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;



-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of permissions
-- ----------------------------
BEGIN;
INSERT INTO `permissions` VALUES (1, 'manage_admins', 'admin', now(), now());
INSERT INTO `permissions` VALUES (2, 'manage_roles', 'admin', now(), now());
INSERT INTO `permissions` VALUES (3, 'manage_users', 'admin', now(), now());
INSERT INTO `permissions` VALUES (4, 'manage_user_levels', 'admin', now(), now());
INSERT INTO `permissions` VALUES (5, 'manage_products', 'admin', now(), now());
INSERT INTO `permissions` VALUES (6, 'manage_categories', 'admin', now(), now());
INSERT INTO `permissions` VALUES (7, 'manage_cards', 'admin', now(), now());
INSERT INTO `permissions` VALUES (8, 'manage_coupons', 'admin', now(), now());
INSERT INTO `permissions` VALUES (9, 'manage_servers', 'admin', now(), now());
INSERT INTO `permissions` VALUES (10, 'manage_orders', 'admin', now(), now());
INSERT INTO `permissions` VALUES (11, 'view_orders', 'admin', now(), now());
INSERT INTO `permissions` VALUES (12, 'manage_payments', 'admin', now(), now());
INSERT INTO `permissions` VALUES (13, 'manage_articles', 'admin', now(), now());
INSERT INTO `permissions` VALUES (14, 'manage_article_categories', 'admin', now(), now());
INSERT INTO `permissions` VALUES (15, 'manage_email_templates', 'admin', now(), now());
INSERT INTO `permissions` VALUES (16, 'manage_settings', 'admin', now(), now());
COMMIT;

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of roles
-- ----------------------------
BEGIN;
INSERT INTO `roles` VALUES (1, 'super-admin', 'admin', now(), now());
INSERT INTO `roles` VALUES (2, 'admin', 'admin', now(), now());
INSERT INTO `roles` VALUES (3, 'manager', 'admin', now(), now());
INSERT INTO `roles` VALUES (4, 'order-processor', 'admin', now(), now());
COMMIT;

-- ----------------------------
-- Table structure for model_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  INDEX `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of model_has_permissions
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for model_has_roles
-- ----------------------------
DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  INDEX `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of model_has_roles
-- ----------------------------
BEGIN;
INSERT INTO `model_has_roles` VALUES (1, 'App\\Models\\AdminUser', 1);
COMMIT;

-- ----------------------------
-- Table structure for role_has_permissions
-- ----------------------------
DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  PRIMARY KEY (`permission_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of role_has_permissions
-- ----------------------------
BEGIN;
-- super-admin 拥有全部权限
INSERT INTO `role_has_permissions` VALUES (1, 1), (2, 1), (3, 1), (4, 1), (5, 1), (6, 1), (7, 1), (8, 1), (9, 1), (10, 1), (11, 1), (12, 1), (13, 1), (14, 1), (15, 1), (16, 1);

-- admin 拥有除了管理员和角色管理的所有权限
INSERT INTO `role_has_permissions` VALUES (3, 2), (4, 2), (5, 2), (6, 2), (7, 2), (8, 2), (9, 2), (10, 2), (11, 2), (12, 2), (13, 2), (14, 2), (15, 2), (16, 2);

-- manager 拥有用户、商店、内容管理权限
INSERT INTO `role_has_permissions` VALUES (3, 3), (4, 3), (5, 3), (6, 3), (7, 3), (8, 3), (13, 3), (14, 3), (15, 3);

-- order-processor 拥有订单和库存管理权限
INSERT INTO `role_has_permissions` VALUES (7, 4), (10, 4), (11, 4);
COMMIT;

-- ----------------------------
-- Table structure for user_levels
-- ----------------------------
DROP TABLE IF EXISTS `user_levels`;
CREATE TABLE `user_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '等级名称',
  `min_spent` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最低消费金额',
  `discount_rate` decimal(4,2) NOT NULL DEFAULT '1.00' COMMENT '折扣率（1.00=原价，0.9=9折）',
  `color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '#6c757d' COMMENT '等级颜色',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '等级描述',
  `sort` int NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1启用 0禁用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_min_spent` (`min_spent`),
  KEY `idx_status_sort` (`status`, `sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户等级表';

-- ----------------------------
-- Records of user_levels
-- ----------------------------
BEGIN;
INSERT INTO `user_levels` (`id`, `name`, `min_spent`, `discount_rate`, `color`, `description`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, '普通会员', 0.00, 1.00, '#6c757d', '新注册用户默认等级', 1, 1, now(), now(), NULL);
INSERT INTO `user_levels` (`id`, `name`, `min_spent`, `discount_rate`, `color`, `description`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, '白银会员', 500.00, 0.95, '#c0c0c0', '累计消费满500元可升级', 2, 1, now(), now(), NULL);
INSERT INTO `user_levels` (`id`, `name`, `min_spent`, `discount_rate`, `color`, `description`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, '黄金会员', 1000.00, 0.92, '#ffd700', '累计消费满1000元可升级', 3, 1, now(), now(), NULL);
INSERT INTO `user_levels` (`id`, `name`, `min_spent`, `discount_rate`, `color`, `description`, `sort`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, '钻石会员', 3000.00, 0.88, '#b9f2ff', '累计消费满3000元可升级', 4, 1, now(), now(), NULL);
COMMIT;

-- ----------------------------
-- Table structure for admin_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of admin_users
-- ----------------------------
BEGIN;
INSERT INTO `admin_users` (`id`, `username`, `password`, `name`, `avatar`, `remember_token`, `created_at`, `updated_at`) VALUES (1, 'admin', '$2y$10$e7z99Mhxm9BOHL55xHZTx.QcNTZJC6ftRXHCR/ZkBja/jBeasVeBy', 'Administrator', NULL, '4UAXF2BEw9EL1Tr2aGmwkv5DKwxqRF6djOMAHSiBMSOrPfPNHYrjCCQMtnTC', now(), now());
COMMIT;

-- ----------------------------
-- Table structure for carmis
-- ----------------------------
DROP TABLE IF EXISTS `carmis`;
CREATE TABLE `carmis` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `goods_id` int NOT NULL COMMENT '所属商品',
  `sub_id` int unsigned DEFAULT '0' COMMENT '子规格ID',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1未售出 2已售出',
  `is_loop` tinyint(1) NOT NULL DEFAULT '0' COMMENT '循环卡密 1是 0否',
  `carmi` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '卡密',
  `info` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '卡密说明',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_goods_id` (`goods_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='卡密表';

-- ----------------------------
-- Records of carmis
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for coupons
-- ----------------------------
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠金额',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '优惠类型 1百分比优惠 2固定金额优惠',
  `is_open` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用 1是 0否',
  `coupon` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '优惠码',
  `ret` int NOT NULL DEFAULT '0' COMMENT '剩余使用次数',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_coupon` (`coupon`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='优惠码表';

-- ----------------------------
-- Records of coupons
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for coupons_goods
-- ----------------------------
DROP TABLE IF EXISTS `coupons_goods`;
CREATE TABLE `coupons_goods` (
  `id` int NOT NULL AUTO_INCREMENT,
  `goods_id` int NOT NULL COMMENT '商品id',
  `coupons_id` int NOT NULL COMMENT '优惠码id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='优惠码关联商品表';

-- ----------------------------
-- Records of coupons_goods
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for emailtpls
-- ----------------------------
DROP TABLE IF EXISTS `emailtpls`;
CREATE TABLE `emailtpls` (
  `id` int NOT NULL AUTO_INCREMENT,
  `tpl_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '邮件标题',
  `tpl_token` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '邮件标识',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mail_token` (`tpl_token`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of emailtpls
-- ----------------------------
BEGIN;
INSERT INTO `emailtpls` (`id`, `tpl_name`, `tpl_token`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, '【{{site.name}}】感谢您的购买，请查收您的收据', 'card_send_user_email', now(), now(), NULL);
INSERT INTO `emailtpls` (`id`, `tpl_name`, `tpl_token`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, '【{{site.name}}】新订单等待处理！', 'manual_send_manage_mail', now(), now(), NULL);
INSERT INTO `emailtpls` (`id`, `tpl_name`, `tpl_token`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, '【{{site.name}}】订单处理失败！', 'failed_order', now(), now(), NULL);
INSERT INTO `emailtpls` (`id`, `tpl_name`, `tpl_token`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, '【{{site.name}}】您的订单已经处理完成！', 'completed_order', now(), now(), NULL);
INSERT INTO `emailtpls` (`id`, `tpl_name`, `tpl_token`, `created_at`, `updated_at`, `deleted_at`) VALUES (5, '【{{site.name}}】已收到您的订单，请等候处理', 'pending_order', now(), now(), NULL);
COMMIT;

-- ----------------------------
-- Table structure for failed_jobs
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of failed_jobs
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for goods
-- ----------------------------
DROP TABLE IF EXISTS `goods`;
CREATE TABLE `goods` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL COMMENT '所属分类id',
  `gd_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品名称',
  `gd_description` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品描述',
  `gd_keywords` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品关键字',
  `picture` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '商品图片',
  `sales_volume` int DEFAULT '0' COMMENT '销量',
  `ord` int DEFAULT '1' COMMENT '排序权重 越大越靠前',
  `payment_limit` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '仅允许的支付方式',
  `buy_limit_num` int NOT NULL DEFAULT '0' COMMENT '限制单次购买最大数量，0为不限制',
  `buy_prompt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '购买提示',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '商品描述',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '商品类型  1自动发货 2人工处理 3自动处理',
  `wholesale_price_cnf` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '批发价配置',
  `other_ipu_cnf` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '其他输入框配置',
  `api_hook` tinyint(3) UNSIGNED NULL DEFAULT 0 COMMENT '回调事件',
  `preselection` decimal(10,2) DEFAULT '0.0' COMMENT '自选加价',
  `require_login` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否需要登录才能购买',
  `picture_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '图片URL地址',
  `buy_min_num` int NOT NULL DEFAULT '1' COMMENT '最小购买数量',
  `usage_instructions` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '使用说明',
  `customer_form_fields` json DEFAULT NULL COMMENT '客户输入表单配置',
  `wholesale_prices` json DEFAULT NULL COMMENT '批发价格配置（新格式）',
  `is_open` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用，1是 0否',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='商品表';

-- ----------------------------
-- Records of goods
-- ----------------------------
BEGIN;
INSERT INTO `goods` (`id`, `group_id`, `gd_name`, `gd_description`, `gd_keywords`, `picture`, `sales_volume`, `ord`, `payment_limit`, `buy_limit_num`, `buy_prompt`, `description`, `type`, `wholesale_price_cnf`, `other_ipu_cnf`, `api_hook`, `preselection`, `require_login`, `picture_url`, `buy_min_num`, `usage_instructions`, `customer_form_fields`, `wholesale_prices`, `is_open`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, 1, '示例商品', '这是一个示例商品', '示例,商品', NULL, 0, 1, NULL, 0, '这是一个用于演示的示例商品，您可以修改或删除它。', '这是一个用于演示的示例商品的详细描述', 2, NULL, NULL, 0, 0.00, 0, NULL, 1, NULL, NULL, NULL, 1, now(), now(), NULL);
COMMIT;

-- ----------------------------
-- Table structure for goods_sub
-- ----------------------------
DROP TABLE IF EXISTS `goods_sub`;
CREATE TABLE `goods_sub` (
  `id` int NOT NULL AUTO_INCREMENT,
  `goods_id` int DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL COMMENT '价格',
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stock` int DEFAULT NULL COMMENT '库存',
  `sales_volume` int DEFAULT NULL COMMENT '销量',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `good_id` (`goods_id`),
  CONSTRAINT `good_id` FOREIGN KEY (`goods_id`) REFERENCES `goods` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of goods_sub
-- ----------------------------
BEGIN;
INSERT INTO `goods_sub` (`id`, `goods_id`, `price`, `name`, `stock`, `sales_volume`, `created_at`, `updated_at`) VALUES (1, 1, 10.00, '默认规格', 1, 0, now(), now());
COMMIT;

-- ----------------------------
-- Table structure for goods_group
-- ----------------------------
DROP TABLE IF EXISTS `goods_group`;
CREATE TABLE `goods_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `gp_name` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '分类名称',
  `is_open` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用，1是 0否',
  `ord` int NOT NULL DEFAULT '1' COMMENT '排序权重 越大越靠前',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='商品分类表';

-- ----------------------------
-- Records of goods_group
-- ----------------------------
BEGIN;
INSERT INTO `goods_group` (`id`, `gp_name`, `is_open`, `ord`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, '默认分类', 1, 1, now(), now(), NULL);
COMMIT;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE COMMENT '订单号',
  `user_id` bigint unsigned DEFAULT NULL COMMENT '关联用户id',
  `email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '下单邮箱',
  `total_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单总价',
  `actual_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际支付价格',
  `coupon_discount_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠券折扣',
  `user_discount_rate` decimal(3,2) NOT NULL DEFAULT '1.00' COMMENT '用户等级折扣率',
  `user_discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '用户等级优惠金额',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '订单状态：1待支付 2待处理 3处理中 4已完成 5失败 6异常 -1过期',
  `pay_id` int DEFAULT NULL COMMENT '支付方式ID',
  `payment_method` tinyint NOT NULL DEFAULT '1' COMMENT '1:在线支付 2:余额支付 3:混合支付',
  `balance_used` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '使用的余额金额',
  `search_pwd` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '查询密码',
  `buy_ip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '下单IP',
  `trade_no` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '第三方支付订单号',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_order_sn_index` (`order_sn`),
  KEY `orders_email_index` (`email`),
  KEY `orders_status_index` (`status`),
  KEY `orders_user_id_index` (`user_id`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci COMMENT='订单表';

-- ----------------------------
-- Records of orders
-- ----------------------------
BEGIN;
COMMIT;



-- ----------------------------
-- Table structure for pays
-- ----------------------------
DROP TABLE IF EXISTS `pays`;
CREATE TABLE `pays` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pay_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '支付名称',
  `pay_check` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '支付标识',
  `pay_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '通道费率',
  `pay_method` tinyint(1) NOT NULL COMMENT '支付方式 1跳转 2扫码',
  `pay_client` tinyint(1) NOT NULL DEFAULT '1' COMMENT '支付场景：1电脑pc 2手机 3全部',
  `merchant_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '商户 ID',
  `merchant_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '商户 KEY',
  `merchant_pem` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商户密钥',
  `pay_handleroute` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '支付处理路由',
  `china_only` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否仅允许中国大陆IP下单 1是 0否',
  `enable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用 1是 0否',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_pay_check` (`pay_check`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of pays
-- ----------------------------
BEGIN;
INSERT INTO `pays` VALUES (null ,'USDT-TRC20', 'tokenpay-usdt-trc', 0, 1, 3, 'USDT_TRC20', '你的API密钥', 'TokenPay地址', 'tokenpay', 0, 1, now(), now(), NULL);
INSERT INTO `pays` VALUES (null ,'TRX', 'tokenpay-trx', 0, 1, 3, 'TRX', 'API密钥', 'TokenPay地址', 'tokenpay', 0, 1, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, 'Epusdt[trc20]', 'epusdt', 0, 1, 3, 'API密钥', '不填即可', 'api请求地址', 'epusdt', 0, 1, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, '支付宝当面付', 'zfbf2f', 0, 2, 3, '商户号', '支付宝公钥', '商户私钥', 'alipay', 1, 1, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, '支付宝 PC', 'aliweb', 0, 1, 1, '商户号', '', '密钥', 'alipay', 1, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, '支付宝 WAP', 'aliwap', 0, 1, 2, '商户号', '', '密钥', 'alipay', 1, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, '微信扫码', 'wescan', 0, 2, 1, '商户号', '', 'V2密钥', 'wepay', 1, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, '微信小程序', 'miniapp', 0, 1, 2, '商户号', '', 'V2密钥', 'wepay', 1, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, '码支付 QQ', 'mqq', 0, 1, 1, '商户号', '', '密钥', 'mapay', 1, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, '码支付支付宝', 'mzfb', 0, 1, 1, '商户号', '', '密钥', 'mapay', 1, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, '码支付微信', 'mwx', 0, 1, 1, '商户号', '', '密钥', 'mapay', 1, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, 'Paysapi 支付宝', 'pszfb', 0, 1, 1, '商户号', '', '密钥', 'paysapi', 1, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, 'Paysapi 微信', 'pswx', 0, 1, 1, '商户号', '', '密钥', 'paysapi', 1, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, 'Payjs 微信扫码', 'payjswescan', 0, 1, 1, '商户号', '', '密钥', 'payjs', 1, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, '易支付-支付宝', 'alipay', 0, 1, 1, '商户号', '', '密钥', 'yipay', 1, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, '易支付-微信', 'wxpay', 0, 1, 1, '商户号', NULL, '密钥', 'yipay', 1, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, '易支付-QQ 钱包', 'qqpay', 0, 1, 1, '商户号', NULL, '密钥', 'yipay', 1, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, 'PayPal', 'paypal', 0, 1, 1, '商户号', NULL, '密钥', 'paypal', 0, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, 'V 免签支付宝', 'vzfb', 0, 1, 1, 'V 免签通讯密钥', NULL, 'V 免签地址 例如 https://vpay.qq.com/    结尾必须有/', 'vpay', 1, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, 'V 免签微信', 'vwx', 0, 1, 1, 'V 免签通讯密钥', NULL, 'V 免签地址 例如 https://vpay.qq.com/    结尾必须有/', 'vpay', 1, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, 'Stripe[微信支付宝]', 'stripe', 0, 1, 1, 'pk开头的可发布密钥', NULL, 'sk开头的密钥', 'stripe', 0, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null, 'Coinbase[加密货币]', 'coinbase', 0, 1, 3, '费率', 'API密钥', '共享密钥', 'coinbase', 0, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null ,'ETH', 'tokenpay-eth', 0, 1, 3, 'ETH', 'API密钥', 'TokenPay地址', 'tokenpay', 0, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null ,'USDT-ERC20', 'tokenpay-usdt-erc', 0, 1, 3, 'USDT_ERC20', 'API密钥', 'TokenPay地址', 'tokenpay', 0, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null ,'USDC-ERC20', 'tokenpay-usdc-erc', 0, 1, 3, 'USDC_ERC20', 'API密钥', 'TokenPay地址', 'tokenpay', 0, 0, now(), now(), NULL);
INSERT INTO `pays` VALUES (null ,'币安支付', 'binance', 0, 1, 3, 'USDT', 'API密钥', '密钥', 'binance', 0, 0, now(), now(), NULL);
COMMIT;
-- ----------------------------
-- Table structure for article_categories
-- ----------------------------
DROP TABLE IF EXISTS `article_categories`;
CREATE TABLE `article_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分类名称',
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci UNIQUE DEFAULT NULL COMMENT '分类标识',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '分类描述',
  `sort` int NOT NULL DEFAULT '0' COMMENT '排序',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `article_categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of article_categories
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for articles
-- ----------------------------

DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文章ID',
  `category_id` bigint unsigned DEFAULT NULL COMMENT '文章分类ID',
  `link` varchar(255) NOT NULL COMMENT '文章链接',
  `title` varchar(255) NOT NULL COMMENT '文章标题',
  `category` varchar(255) NULL COMMENT '文章分类',
  `content` text NOT NULL COMMENT '文章内容',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `articles_category_id_foreign` (`category_id`),
  CONSTRAINT `articles_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `article_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of articles
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for article_goods
-- ----------------------------
DROP TABLE IF EXISTS `article_goods`;
CREATE TABLE `article_goods` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int NOT NULL COMMENT '文章ID',
  `goods_id` int NOT NULL COMMENT '商品ID',
  `sort` int NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `article_goods_article_id_goods_id_unique` (`article_id`,`goods_id`),
  KEY `article_goods_article_id_index` (`article_id`),
  KEY `article_goods_goods_id_index` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of article_goods
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for remote_servers
-- ----------------------------

DROP TABLE IF EXISTS `remote_servers`;
CREATE TABLE `remote_servers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '服务器ID',
  `name` varchar(255) NOT NULL COMMENT '服务器名称',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '服务器类型  1HTTP 2RCON 3SQL',
  `data` text COMMENT '服务器数据',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of remote_servers
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_spent` decimal(10,2) NOT NULL DEFAULT '0.00',
  `level_id` tinyint unsigned NOT NULL DEFAULT '1',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1:正常 2:禁用',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_email_status_index` (`email`,`status`),
  KEY `users_level_id_index` (`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for user_balance_records
-- ----------------------------
DROP TABLE IF EXISTS `user_balance_records`;
CREATE TABLE `user_balance_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'recharge:充值 consume:消费 refund:退款 admin:管理员调整',
  `amount` decimal(10,2) NOT NULL,
  `balance_before` decimal(10,2) NOT NULL,
  `balance_after` decimal(10,2) NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `related_order_sn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_id` bigint unsigned DEFAULT NULL,
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_balance_records_user_id_foreign` (`user_id`),
  KEY `user_balance_records_user_id_type_index` (`user_id`,`type`),
  KEY `user_balance_records_related_order_sn_index` (`related_order_sn`),
  CONSTRAINT `user_balance_records_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of user_balance_records
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for password_reset_tokens
-- ----------------------------
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of password_reset_tokens
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for order_items
-- ----------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL COMMENT '订单ID',
  `goods_id` int NOT NULL COMMENT '商品ID',
  `sub_id` int DEFAULT NULL COMMENT '商品规格ID',
  `goods_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '商品名称',
  `unit_price` decimal(10,2) NOT NULL COMMENT '商品单价',
  `quantity` int NOT NULL COMMENT '购买数量',
  `subtotal` decimal(10,2) NOT NULL COMMENT '小计金额',
  `info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '商品详情/卡密信息',
  `type` tinyint NOT NULL DEFAULT '1' COMMENT '商品类型：1自动发货 2人工处理',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_goods_id_index` (`goods_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of order_items
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for notifications
-- ----------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of notifications
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for settings
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` json NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`group`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of settings
-- ----------------------------
BEGIN;
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('system', 'contact_required', '"email"', 0, now(), now());

-- Shop Settings 基础设置
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('shop', 'title', '"独角数卡"', 0, now(), now());
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('shop', 'img_logo', '"/assets/common/images/logo.svg"', 0, now(), now());
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('shop', 'text_logo', 'null', 0, now(), now());
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('shop', 'keywords', '"独角数卡,虚拟商品,自动发货"', 0, now(), now());
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('shop', 'description', '"独角数卡 - 专业的虚拟商品自动发货平台"', 0, now(), now());
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('shop', 'template', '"morpho"', 0, now(), now());
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('shop', 'language', '"zh-CN"', 0, now(), now());
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('shop', 'currency', '"CNY"', 0, now(), now());
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('shop', 'is_open_anti_red', 'false', 0, now(), now());
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('shop', 'is_cn_challenge', 'false', 0, now(), now());
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('shop', 'is_open_search_pwd', 'true', 0, now(), now());
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('shop', 'is_open_google_translate', 'false', 0, now(), now());
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('shop', 'notice', 'null', 0, now(), now());
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('shop', 'footer', 'null', 0, now(), now());

-- Shop Settings 导航栏设置
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('shop', 'nav_items', '[{\"name\":\"首页\",\"url\":\"/\",\"target_blank\":false,\"children\":[]},{\"name\":\"站点文章\",\"url\":\"/article\",\"target_blank\":false,\"children\":[]},{\"name\":\"订单查询\",\"url\":\"/order/search\",\"target_blank\":false,\"children\":[]}]', 0, now(), now());

-- Theme Settings
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('theme', 'notices', '"欢迎使用我们的服务！\\n限时优惠，立即购买享受折扣\\n24小时客服在线，随时为您服务\\n优质产品，值得信赖"', 0, now(), now());
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('theme', 'banners', '[]', 0, now(), now());
INSERT INTO `settings` (`group`, `name`, `payload`, `locked`, `created_at`, `updated_at`) VALUES ('theme', 'invert_logo', 'false', 0, now(), now());
COMMIT;