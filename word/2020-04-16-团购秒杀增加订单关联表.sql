SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for jshop_promotion_record
-- ----------------------------
DROP TABLE IF EXISTS `jshop_promotion_record`;
CREATE TABLE `jshop_promotion_record`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `promotion_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '促销id',
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户Id',
  `goods_id` bigint(11) UNSIGNED NOT NULL COMMENT '商品id',
  `product_id` int(10) NULL DEFAULT NULL COMMENT '货品id',
  `order_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单id',
  `type` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '1团购，2秒杀',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '促销活动记录表，目前主要记录团购秒杀记录' ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
