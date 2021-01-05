ALTER TABLE `jshop_sms`
MODIFY COLUMN `mobile` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '手机号码，允许同时存在多个手机号码，英文逗号分隔' AFTER `id`;

ALTER TABLE `jshop_images` CHANGE `name` `name` VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '图片名称';


ALTER TABLE `jshop_order`
MODIFY COLUMN `promotion_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '优惠信息' AFTER `coupon`;

ALTER TABLE `jshop_user`
MODIFY COLUMN `balance` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '余额' AFTER `nickname`,
MODIFY COLUMN `point` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '积分' AFTER `balance`,
MODIFY COLUMN `grade` tinyint(2) UNSIGNED NULL DEFAULT 0 COMMENT '用户等级' AFTER `point`,
MODIFY COLUMN `remarks` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '备注' AFTER `isdel`;