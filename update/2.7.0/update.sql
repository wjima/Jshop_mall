ALTER TABLE `jshop_bill_aftersales_items`
MODIFY COLUMN `image_url` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片' AFTER `name`;

ALTER TABLE `jshop_order_items`
MODIFY COLUMN `image_url` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片' AFTER `mktprice`;
