ALTER TABLE `images` DROP `seller_id`;

ALTER TABLE `hooks` CHANGE `type` `type` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '钩子类型,1是控制器，2是视图';
