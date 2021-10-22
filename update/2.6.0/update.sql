ALTER TABLE `jshop_order_items`
ADD COLUMN `ave_amount` decimal(20, 2) NULL COMMENT '货品实际总金额' AFTER `price`,
ADD COLUMN `ave_price` decimal(20, 2) NULL COMMENT '货品实际单价' AFTER `ave_amount`;


ALTER TABLE `jshop_order_items`
ADD COLUMN `is_gift` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '是否赠品，1赠品，2不是赠品' AFTER `sendnums`;