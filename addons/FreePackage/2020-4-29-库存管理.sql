DROP TABLE IF EXISTS `jshop_stock`;
CREATE TABLE `jshop_stock` (
  `id` varchar(20) NOT NULL COMMENT '单号',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1 入库 2出库',
  `manage_id` int(10) unsigned DEFAULT '0' COMMENT '操作人',
  `ctime` bigint(12) unsigned DEFAULT NULL COMMENT '创建时间',
  `memo` varchar(200) NOT NULL DEFAULT '' COMMENT '备注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='库存操作表';

DROP TABLE IF EXISTS `jshop_stock_log`;
CREATE TABLE `jshop_stock_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stock_id` varchar(20) NOT NULL COMMENT '库存单号',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '货品ID',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `nums` int(11)  DEFAULT '0' COMMENT '库存',
   `sn` varchar(30) DEFAULT NULL COMMENT '货品编码',
  `bn` varchar(30) DEFAULT NULL COMMENT '商品编码',
  `goods_name` varchar(200) DEFAULT NULL COMMENT '商品名称',
  `spes_desc` text COMMENT '货品明细序列号存储',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='库存操作详情表';