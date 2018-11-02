ALTER TABLE `jshop_template_message`
CHANGE COLUMN `from_id` `form_id`  varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '要发生给的用户' AFTER `code`;
