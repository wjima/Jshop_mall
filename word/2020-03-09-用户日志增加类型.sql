ALTER TABLE `jshop_user_log`
ADD COLUMN `type` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '类型，1会员，2管理员' AFTER `ip`;