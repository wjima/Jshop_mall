SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for jshop_template_message
-- ----------------------------
DROP TABLE IF EXISTS `jshop_template_message`;
CREATE TABLE `jshop_template_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息ID',
  `type` varchar(32) DEFAULT NULL COMMENT '消息类型',
  `code` varchar(32) DEFAULT NULL COMMENT '单号',
  `from_id` varchar(64) DEFAULT NULL COMMENT '要发生给的用户',
  `ctime` bigint(12) unsigned DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) unsigned DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '发送状态 1=未发送 2=已发送',
  `isdel` bigint(12) unsigned DEFAULT NULL COMMENT '删除标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
