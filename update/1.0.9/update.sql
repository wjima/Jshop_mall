ALTER TABLE `jshop_hooks`
MODIFY COLUMN `description`  text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '钩子描述' AFTER `name`,
MODIFY COLUMN `type`  tinyint(1) NULL DEFAULT 1 COMMENT '钩子类型,1是控制器，2是视图' AFTER `description`,
MODIFY COLUMN `addons`  text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '钩子挂载的插件，逗号分隔' AFTER `type`,
MODIFY COLUMN `ctime`  int(10) NULL COMMENT '创建时间' AFTER `addons`,
MODIFY COLUMN `utime`  int(11) NULL DEFAULT 0 COMMENT '更新时间' AFTER `ctime`;