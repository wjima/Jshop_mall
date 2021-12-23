ALTER TABLE `jshop_order_items`
ADD COLUMN `is_free` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '是否免单商品 0=不是 1=是';

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for jshop_free_package
-- ----------------------------
DROP TABLE IF EXISTS `jshop_free_package`;
CREATE TABLE `jshop_free_package`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NULL DEFAULT NULL COMMENT '下单人',
  `mobile` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '手机号',
  `order_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '订单号',
  `status` tinyint(1) UNSIGNED NULL DEFAULT 0 COMMENT '状态 0=未支付 1=已支付 2=已退款',
  `order_amount` decimal(10, 2) UNSIGNED NULL DEFAULT 0.00 COMMENT '订单总金额',
  `ctime` bigint(12) NULL DEFAULT NULL COMMENT '添加时间',
  `utime` bigint(12) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB;


INSERT INTO `jshop_hooks` VALUES (10100, 'addgoodfreepackage', '添加商品时', 1, 'FreePackage', 1636441809, 1636441809);
INSERT INTO `jshop_hooks` VALUES (10101, 'addgoodsafterfreepackage', '添加商品后', 1, 'FreePackage', 1636441818, 1636441818);
INSERT INTO `jshop_hooks` VALUES (10102, 'editgoodsafterfreepackage', '编辑商品后', 1, 'FreePackage', 1636441826, 1636441826);
INSERT INTO `jshop_hooks` VALUES (10103, 'editgoodsfreepackage', '编辑商品时', 1, 'FreePackage', 1636441835, 1636441835);
INSERT INTO `jshop_hooks` VALUES (10104, 'orderpayedafter', '订单支付后', 1, 'FreePackage', 1636441843, 1636441843);