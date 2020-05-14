ALTER TABLE jshop_ietask`
MODIFY COLUMN `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '任务消息' AFTER `name`;