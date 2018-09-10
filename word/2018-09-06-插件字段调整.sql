ALTER TABLE `addons` DROP `times`;
ALTER TABLE `addons` DROP `price`;
ALTER TABLE `addons` DROP `unit`;
ALTER TABLE `addons` DROP `has_adminlist`;
ALTER TABLE `addons` CHANGE `create_time` `ctime` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '安装时间';

-- 增加钩子表
CREATE TABLE `hooks` ( `id` INT(10) NOT NULL AUTO_INCREMENT , `name` VARCHAR(50) NULL DEFAULT NULL COMMENT '钩子名称' , `description` TEXT NOT NULL COMMENT '钩子描述' , `type` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '钩子类型' , `addons` TEXT NOT NULL COMMENT '钩子挂载的插件，逗号分隔' , `ctime` INT(10) NOT NULL COMMENT '创建时间' , PRIMARY KEY (`id`)) ENGINE = InnoDB;


ALTER TABLE `addons` ADD `utime` INT(10) NOT NULL DEFAULT '0' COMMENT '更新时间' AFTER `ctime`;

ALTER TABLE `hooks` ADD `utime` INT(11) NOT NULL DEFAULT '0' COMMENT '更新时间' AFTER `ctime`;