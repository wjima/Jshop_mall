/*
 Navicat Premium Data Transfer

 Source Server         : wgg
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : haibao

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 22/04/2020 18:38:41
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for jshop_welfarepro_coupon
-- ----------------------------
DROP TABLE IF EXISTS `jshop_welfarepro_coupon`;
CREATE TABLE `jshop_welfarepro_coupon`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '名称',
  `date_start` bigint(12) UNSIGNED NOT NULL COMMENT '生效时间',
  `date_end` bigint(12) UNSIGNED NOT NULL COMMENT '失效时间',
  `type` tinyint(2) UNSIGNED NOT NULL COMMENT '1，全部用户，2新用户',
  `sendnum` int(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '数量',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL,
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '扫码领优惠券表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for jshop_welfarepro_coupon_rel
-- ----------------------------
DROP TABLE IF EXISTS `jshop_welfarepro_coupon_rel`;
CREATE TABLE `jshop_welfarepro_coupon_rel`  (
  `c_id` int(11) UNSIGNED NOT NULL COMMENT '扫码领优惠券ID',
  `coupon_id` int(11) UNSIGNED NOT NULL COMMENT '优惠券ID',
  `num` int(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '数量'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '扫码领优惠券与优惠券关联表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;

Alter TABLE jshop_welfarepro_hbuser add `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1 红包 2优惠券';
