ALTER TABLE `jshop_sms`
MODIFY COLUMN `mobile` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '手机号码，允许同时存在多个手机号码，英文逗号分隔' AFTER `id`;

ALTER TABLE `jshop_images` CHANGE `name` `name` VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '图片名称';


ALTER TABLE `jshop_order`
MODIFY COLUMN `promotion_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '优惠信息' AFTER `coupon`;
