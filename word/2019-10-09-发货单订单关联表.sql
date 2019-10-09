SET FOREIGN_KEY_CHECKS=0;

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