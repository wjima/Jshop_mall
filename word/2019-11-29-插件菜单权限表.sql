CREATE TABLE `jshop_manage_role_addons_rel` (
  `manage_role_id` int(10) NOT NULL,
  `menu_id` varchar(100) NOT NULL COMMENT '插件里的菜单的id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='店铺角色操作权限关联表';
