ALTER TABLE `jshop_sms`
MODIFY COLUMN `params`  varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '参数' AFTER `code`;

ALTER TABLE `jshop_message`
MODIFY COLUMN `params`  varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '参数' AFTER `code`;