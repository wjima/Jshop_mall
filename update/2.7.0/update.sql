ALTER TABLE `jshop_bill_aftersales_items`
MODIFY COLUMN `image_url` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片' AFTER `name`;

ALTER TABLE `jshop_order_items`
MODIFY COLUMN `image_url` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片' AFTER `mktprice`;

ALTER TABLE `jshop_goods`
ADD COLUMN `is_combo` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '是否是套餐商品 1=是 2=不是';
