ALTER TABLE `jshop_pintuan_record`
MODIFY COLUMN `params` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT 'json格式的参数，主要是拼团人数' AFTER `order_id`,
MODIFY COLUMN `close_time` bigint(12) UNSIGNED NULL COMMENT '关闭时间' AFTER `params`;