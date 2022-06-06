CREATE TABLE `jshop_pages_menu`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) NOT NULL COMMENT '菜单id',
  `pid` int(10) NOT NULL DEFAULT 0 COMMENT '父级菜单',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '菜单名称',
  `ptype` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '菜单类型',
  `params` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '菜单参数',
  `selecticon` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '选中图标',
  `icon` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '未选中图标',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '前端页面菜单表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of jshop_pages_menu
-- ----------------------------
INSERT INTO `jshop_pages_menu` VALUES (9, 2, 0, '首页', 'redirectTo', '{\"url\":\"\\/pages\\/index\\/index\"}', '483b8ee6429a680654c454d0fdd6fd5a', '98ebc1f521934da1d41b35178c0339ae');
INSERT INTO `jshop_pages_menu` VALUES (10, 3, 0, '分类页', 'redirectTo', '{\"url\":\"\\/pages\\/classify\\/classify\"}', '70ff17564a360cb74159d82095492ca7', 'f4c5604ff4f9d52f88538579aeadfe54');
INSERT INTO `jshop_pages_menu` VALUES (11, 4, 0, '购物车', 'redirectTo', '{\"url\":\"\\/pages\\/cart\\/index\\/index\"}', '4602163f525b84fd0de4b080aa256d92', 'ca84a5bb1899cfa2b7d78d2f66025112');
INSERT INTO `jshop_pages_menu` VALUES (12, 5, 0, '会员', 'redirectTo', '{\"url\":\"\\/pages\\/member\\/index\\/index\"}', 'b5f7ae2456ad1a01d772a24363e72fa2', 'af01a9f897c86e59b79aac9fb142701a');



INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (607, 559, '页面菜单', 'setmenu', 'a', 266, 1, 100);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (608, 559, '编辑页面菜单', 'editmenu', 'a', 266, 3, 100);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (609, 559, '保存页面菜单', 'doeditmenu', 'a', 266, 3, 100);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (610, 559, '删除页面菜单', 'deletemenu', 'a', 266, 3, 100);
