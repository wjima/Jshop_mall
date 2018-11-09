/*
Navicat MySQL Data Transfer

Source Server         : 本地宝塔数据库
Source Server Version : 50717
Source Host           : localhost:3306
Source Database       : b2c

Target Server Type    : MYSQL
Target Server Version : 50717
File Encoding         : 65001

Date: 2018-11-07 16:45:38
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for jshop_clerk
-- ----------------------------
DROP TABLE IF EXISTS `jshop_clerk`;
CREATE TABLE `jshop_clerk` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(10) unsigned DEFAULT NULL COMMENT '店铺ID',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '用户ID',
  `ctime` bigint(12) unsigned DEFAULT NULL COMMENT '关联时间',
  `utime` bigint(12) unsigned DEFAULT NULL COMMENT '更新时间',
  `isdel` bigint(12) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='店铺店员关联表';
