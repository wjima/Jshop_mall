ALTER TABLE `jshop_images`
ADD COLUMN `group_id` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '分组id，0为默认分组' AFTER `id`;

-- 图片分组表
CREATE TABLE `jshop_images_group`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '分组名称',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '图片分组表' ROW_FORMAT = Compact;
