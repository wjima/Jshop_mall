ALTER TABLE `jshop_order_items`
MODIFY COLUMN `promotion_list` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '促销信息' AFTER `promotion_amount`;
