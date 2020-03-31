DROP TABLE IF EXISTS `jshop_group_goods`;
CREATE TABLE `jshop_group_goods`  (
  `rule_id` int(11) UNSIGNED NOT NULL,
  `goods_id` int(11) UNSIGNED NOT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '团购秒杀商品表' ROW_FORMAT = Compact;
