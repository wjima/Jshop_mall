/*
 Navicat Premium Data Transfer

 Source Server         : 本地
 Source Server Type    : MySQL
 Source Server Version : 50554
 Source Host           : localhost:3306
 Source Schema         : jshop

 Target Server Type    : MySQL
 Target Server Version : 50554
 File Encoding         : 65001

 Date: 15/04/2020 00:39:15
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for jshop_bargain
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bargain`;
CREATE TABLE `jshop_bargain`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '活动名称',
  `intro` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '简介',
  `goods_id` int(10) NULL DEFAULT NULL COMMENT '商品id',
  `stime` int(12) NULL DEFAULT NULL COMMENT '开始时间',
  `etime` int(12) NULL DEFAULT NULL COMMENT '结束时间',
  `max_goods_nums` int(10) NULL DEFAULT 0 COMMENT '活动数量',
  `sales_num` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '活动销量',
  `sort` smallint(10) UNSIGNED NULL DEFAULT 100 COMMENT '排序',
  `status` tinyint(3) UNSIGNED NULL DEFAULT 2 COMMENT '状态 1=开启（默认）  2=禁用',
  `start_price` decimal(20, 2) NULL DEFAULT 0.00 COMMENT '砍价起始金额',
  `end_price` decimal(20, 2) NULL DEFAULT 0.00 COMMENT '砍价成交金额',
  `bargain_max_price` decimal(20, 2) NULL DEFAULT NULL COMMENT '砍价每次最大金额',
  `bargain_min_price` decimal(20, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '砍价每次最小金额',
  `total_times` int(10) NULL DEFAULT NULL COMMENT '总砍价次数',
  `significant_interval` int(5) NULL DEFAULT NULL COMMENT '砍价发起后有效时长，单位小时',
  `desc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '活动规则描述',
  `ctime` int(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` int(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '砍价活动表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_bargain_log
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bargain_log`;
CREATE TABLE `jshop_bargain_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `record_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '砍价人ID',
  `user_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '砍价人ID，砍价人ID和发起人ID一样时，表示自己砍价',
  `bargain_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '砍价活动ID',
  `bargain_price` decimal(20, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '砍掉金额',
  `goods_price` decimal(20, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '砍价后商品金额',
  `product_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '砍价货品id',
  `ctime` int(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `ip` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '砍价人ip地址',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '砍价记录表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for jshop_bargain_record
-- ----------------------------
DROP TABLE IF EXISTS `jshop_bargain_record`;
CREATE TABLE `jshop_bargain_record`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `bargain_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '砍价活动id',
  `user_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '砍价发起人id',
  `status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '状态，1进行中，2成功，3,已下单，4超过活动时间砍价失败',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '活动名称',
  `goods_id` int(11) NULL DEFAULT NULL COMMENT '商品id',
  `product_id` int(11) NULL DEFAULT NULL COMMENT '货品id',
  `goods_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '商品名称',
  `image_url` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '图片地址',
  `start_price` decimal(20, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '开始价格',
  `end_price` decimal(10, 2) NULL DEFAULT NULL COMMENT '底价',
  `price` decimal(20, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '当前价格',
  `stime` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '开始时间',
  `etime` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '结束时间',
  `order_id` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单号',
  `ctime` int(12) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `utime` int(12) UNSIGNED NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '砍价活动订单表' ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
