ALTER TABLE `jshop_order_items`
MODIFY COLUMN `promotion_list` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '促销信息' AFTER `promotion_amount`;


ALTER TABLE `jshop_pages_items` CHANGE `params` `params` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '组件配置内容';

ALTER TABLE `jshop_operation_log` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作数据序列号存储';
