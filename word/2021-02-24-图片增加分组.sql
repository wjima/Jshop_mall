ALTER TABLE `jshop_images`
ADD COLUMN `group_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '分组id，0为默认分组' AFTER `id`;

-- 图片分组表
CREATE TABLE `jshop_images_group`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '分组名称',
  `show` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '是否显示，1显示，2不显示',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '图片分组表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of jshop_images_group
-- ----------------------------
INSERT INTO `jshop_images_group` VALUES (1, '头像', 2);
INSERT INTO `jshop_images_group` VALUES (2, '海报', 2);
INSERT INTO `jshop_images_group` VALUES (3, '二维码', 2);