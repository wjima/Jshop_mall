SET FOREIGN_KEY_CHECKS=0;

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