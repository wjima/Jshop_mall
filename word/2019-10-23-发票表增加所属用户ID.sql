ALTER TABLE `jshop_invoice`
ADD COLUMN `user_id`  int(10) UNSIGNED NULL COMMENT '所属用户ID' AFTER `source_id`;