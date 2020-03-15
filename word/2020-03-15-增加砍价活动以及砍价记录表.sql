/*
 Navicat Premium Data Transfer

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 50635
 Source Host           : localhost:3306
 Source Schema         : jshop

 Target Server Type    : MySQL
 Target Server Version : 50635
 File Encoding         : 65001

 Date: 16/03/2020 00:17:58
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for jshop_bargain
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bargain`;
CREATE TABLE `jshop_bargain` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8 DEFAULT '' COMMENT '活动名称',
  `intro` tinytext CHARACTER SET utf8 COMMENT '简介',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品id',
  `stime` int(12) DEFAULT NULL COMMENT '开始时间',
  `etime` int(12) DEFAULT NULL COMMENT '结束时间',
  `max_nums` int(10) DEFAULT '0' COMMENT '每人限购数量',
  `max_goods_nums` int(10) DEFAULT '0' COMMENT '活动数量',
  `sort` smallint(10) unsigned DEFAULT '100' COMMENT '排序',
  `status` tinyint(3) unsigned DEFAULT '2' COMMENT '状态 1=开启（默认）  2=禁用',
  `start_price` decimal(20,2) DEFAULT '0.00' COMMENT '砍价起始金额',
  `end_price` decimal(20,2) DEFAULT '0.00' COMMENT '砍价成交金额',
  `bargain_max_price` decimal(20,2) DEFAULT NULL COMMENT '砍价每次最大金额',
  `bargain_min_price` decimal(20,2) unsigned DEFAULT '0.00' COMMENT '砍价每次最小金额',
  `every_max_times` int(10) DEFAULT '0' COMMENT '每人可砍价次数，0为无限制',
  `significant_interval` int(5) DEFAULT NULL COMMENT '砍价发起后有效时长，单位小时',
  `ctime` int(12) unsigned DEFAULT NULL COMMENT '创建时间',
  `utime` int(12) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='砍价活动表';

-- ----------------------------
-- Table structure for jshop_bargain_log
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bargain_log`;
CREATE TABLE `jshop_bargain_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `buyer_id` int(10) unsigned DEFAULT '0' COMMENT '购买人ID',
  `user_id` int(10) unsigned DEFAULT '0' COMMENT '砍价人ID',
  `bargain` int(10) unsigned DEFAULT NULL COMMENT '砍价活动ID',
  `bargain_price` decimal(20,2) unsigned DEFAULT '0.00' COMMENT '砍掉金额',
  `goods_price` decimal(20,2) unsigned DEFAULT '0.00' COMMENT '砍价后商品金额',
  `ctime` int(12) unsigned DEFAULT NULL COMMENT '创建时间',
  `ip` varchar(15) DEFAULT NULL COMMENT '砍价人ip地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='砍价记录表';

SET FOREIGN_KEY_CHECKS = 1;
