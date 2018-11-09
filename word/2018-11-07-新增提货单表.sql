/*
Navicat MySQL Data Transfer

Source Server         : 本地宝塔数据库
Source Server Version : 50717
Source Host           : localhost:3306
Source Database       : b2c

Target Server Type    : MYSQL
Target Server Version : 50717
File Encoding         : 65001

Date: 2018-11-07 18:08:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for jshop_bill_lading
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bill_lading`;
CREATE TABLE `jshop_bill_lading` (
  `id` varchar(20) NOT NULL COMMENT '提货单号',
  `order_id` varchar(20) DEFAULT NULL COMMENT '订单号',
  `store_id` int(10) unsigned DEFAULT NULL COMMENT '提货门店ID',
  `name` varchar(30) DEFAULT NULL COMMENT '提货人姓名',
  `mobile` varchar(15) DEFAULT NULL COMMENT '提货手机号',
  `clerk_id` int(10) unsigned DEFAULT NULL COMMENT '处理店员ID',
  `ptime` bigint(12) unsigned DEFAULT NULL COMMENT '提货时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '提货状态1=未提货 2=已提货',
  `ctime` bigint(12) unsigned DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) unsigned DEFAULT NULL COMMENT '更新时间',
  `isdel` bigint(12) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='提货单表';
