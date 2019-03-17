
DROP TABLE IF EXISTS `jshop_user_grade`;
CREATE TABLE `jshop_user_grade` (
  `id` tinyint(2) unsigned NOT NULL COMMENT 'id',
  `name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `is_def` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1默认，2不默认',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户等级表';

INSERT INTO `jshop_user_grade` VALUES ('1', '普通等级', '1');

ALTER TABLE `jshop_user` ADD `grade` TINYINT(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户等级' AFTER `point`;


INSERT INTO jshop_operation (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ('553', '238', '用户等级', 'grade', 'a', '238', '1', '150'), ('554', '238', '添加&修改', 'gradeadd', 'a', '553', '2', '100'), ('555', '238', '删除', 'gradedel', 'a', '553', '3', '100');

DROP TABLE IF EXISTS `jshop_goods_grade`;
CREATE TABLE `jshop_goods_grade`  (
  `id` bigint(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `goods_id` bigint(10) UNSIGNED NULL DEFAULT 0 COMMENT '商品id',
  `grade_id` tinyint(2) UNSIGNED NULL DEFAULT 1 COMMENT '会员等级id',
  `grade_price` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '会员价',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品会员价表' ROW_FORMAT = Compact;



DROP TABLE IF EXISTS `jshop_invoice_record`;
CREATE TABLE `jshop_invoice_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) DEFAULT NULL COMMENT '发票抬头',
  `code` varchar(30) DEFAULT NULL COMMENT '发票税号',
  `frequency` mediumint(6) unsigned DEFAULT '1' COMMENT '被使用次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='发票信息记录';

INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (NULL, '535', '用户收藏统计', 'goodscollection', 'a', '535', '1', '100');


ALTER TABLE `jshop_weixin_menu`
MODIFY COLUMN `params` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '菜单参数' AFTER `type`;


INSERT INTO `jshop_operation`( `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ( 296, '订单打印', 'print_tpl', 'a', 297, 3, 100);
INSERT INTO `jshop_operation`( `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ( 296, '打印选择快递', 'print_form', 'a', 297, 3, 100);


INSERT INTO `jshop_hooks`(`name`, `description`, `type`, `addons`, `ctime`, `utime`) VALUES ( 'printOrder', '打印订单埋点', 1, 'PrintExpress,KdniaoExpress', 1552448691, 1552448691);
INSERT INTO `jshop_hooks`( `name`, `description`, `type`, `addons`, `ctime`, `utime`) VALUES ( 'getPrintExpressInfo', '获取打印信息', 1, 'KdniaoExpress', 1552557472, 1552557472);
INSERT INTO `jshop_hooks`( `name`, `description`, `type`, `addons`, `ctime`, `utime`) VALUES ( 'orderExtJs', '订单扩展js', 2, 'KdniaoExpress', 1552558689, 1552559645);
INSERT INTO `jshop_hooks`( `name`, `description`, `type`, `addons`, `ctime`, `utime`) VALUES ( 'orderExtBtn', '订单扩展按钮', 2, 'KdniaoExpress', 1552558705, 1552559662);
INSERT INTO `jshop_hooks`( `name`, `description`, `type`, `addons`, `ctime`, `utime`) VALUES ( 'logisticsQuery', '快递查询', 1, 'KdniaoExpress', 1552704811, 1552704811);
