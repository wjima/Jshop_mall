SET FOREIGN_KEY_CHECKS=0;
ALTER TABLE `jshop_bill_delivery` DROP COLUMN `order_id`, DROP COLUMN `user_id`;

-- ----------------------------
-- Table structure for jshop_bill_delivery_order_rel
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bill_delivery_order_rel`;
CREATE TABLE `jshop_bill_delivery_order_rel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_id` varchar(20) DEFAULT NULL COMMENT '订单号',
  `delivery_id` varchar(20) DEFAULT NULL COMMENT '发货单号',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `delivery_id` (`delivery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='发货单订单关联表';

ALTER TABLE `jshop_article_type` ADD `sort` smallint(5) NULL DEFAULT 100 COMMENT '排序 ';

-- ----------------------------
-- Table structure for jshop_invoice
-- ----------------------------
DROP TABLE IF EXISTS `jshop_invoice`;
CREATE TABLE `jshop_invoice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `class` tinyint(1) unsigned DEFAULT '1' COMMENT '开票类型 1=订单',
  `source_id` varchar(32) DEFAULT NULL COMMENT '资源ID',
  `type` tinyint(1) unsigned DEFAULT '2' COMMENT '发票类型 2=个人 3=企业',
  `title` varchar(255) DEFAULT NULL COMMENT '发票抬头',
  `tax_number` varchar(32) DEFAULT NULL COMMENT '发票税号',
  `amount` decimal(10,2) unsigned DEFAULT NULL COMMENT '发票金额',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '开票状态 1=未开票 2=已开票',
  `remarks` varchar(2000) DEFAULT NULL COMMENT '开票备注',
  `ctime` bigint(12) unsigned DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `class` (`class`),
  KEY `source_id` (`source_id`),
  KEY `type` (`type`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='发票表';

ALTER TABLE `jshop_order` DROP COLUMN `tax_content`;

ALTER TABLE `jshop_invoice` ADD COLUMN `user_id`  int(10) UNSIGNED NULL COMMENT '所属用户ID' AFTER `source_id`;

ALTER TABLE `jshop_article` ADD `pv` int(10) NULL DEFAULT 0 COMMENT '访问量', ADD `brief` char(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '简介';

ALTER TABLE `jshop_bill_delivery_items` DROP `order_items_id`;
ALTER TABLE `jshop_bill_delivery_items`
    ADD `goods_id` INT(11) UNSIGNED NOT NULL AFTER `delivery_id`,
    ADD `product_id` INT(11) UNSIGNED NOT NULL AFTER `goods_id`,
    ADD `sn` VARCHAR(30) NOT NULL AFTER `product_id`,
    ADD `bn` VARCHAR(30) NOT NULL AFTER `sn`,
    ADD `name` VARCHAR(200) NOT NULL AFTER `bn`;
ALTER TABLE `jshop_bill_delivery_items`
    ADD `weight` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `nums`,
    ADD `addon` TEXT NOT NULL AFTER `weight`;
ALTER TABLE `jshop_bill_delivery_items` CHANGE `addon` `addon` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;