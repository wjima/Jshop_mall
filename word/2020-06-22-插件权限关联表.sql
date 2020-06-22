
CREATE TABLE IF NOT EXISTS `jshop_manage_role_addons_rel`  (
  `manage_role_id` int(10) NOT NULL,
  `menu_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '插件里的菜单的id'
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '店铺角色操作权限关联表';