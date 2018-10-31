ALTER TABLE `jshop_order`
MODIFY COLUMN `payment_time`  bigint(12) NULL COMMENT '支付时间' AFTER `payment_code`;

ALTER TABLE `jshop_user`
MODIFY COLUMN `point`  int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '积分' AFTER `balance`;