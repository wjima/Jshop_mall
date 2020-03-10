-- 2019-11-29-插件菜单权限表
CREATE TABLE IF NOT EXISTS  `jshop_manage_role_addons_rel` (
  `manage_role_id` int(10) NOT NULL,
  `menu_id` varchar(100) NOT NULL COMMENT '插件里的菜单的id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='店铺角色操作权限关联表';

-- 拼团表增加每人限购数量
ALTER TABLE `jshop_pintuan_rule`
ADD COLUMN `max_nums` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '每人限购数量' AFTER `discount_amount`,
ADD COLUMN `max_goods_nums` int(10) UNSIGNED NULL DEFAULT 0 COMMENT '每个商品活动数量' AFTER `max_nums`;

-- 添加变更优惠券领取者节点
--INSERT INTO `jshop_operation`( `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ( 263, '变更领取者', 'binduser', 'a', 361, 2, 100);

-- 发货明细addons字段设置默认值
ALTER TABLE `jshop_bill_delivery_items` CHANGE `addon` `addon` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;


-- 商品增加默认类型
-- ----------------------------
-- Records of jshop_goods_params
-- ----------------------------
INSERT INTO `jshop_goods_params` VALUES (1, '产地', '', 'text', 1582454016, 1582454016);
INSERT INTO `jshop_goods_params` VALUES (2, '材质', '', 'text', 1582454017, 1582454017);
INSERT INTO `jshop_goods_params` VALUES (3, '型号', '', 'text', 1582454017, 1582454017);


-- ----------------------------
-- Records of jshop_goods_type
-- ----------------------------
INSERT INTO `jshop_goods_type` VALUES (3, '通用类型', NULL);



-- ----------------------------
-- Records of jshop_goods_type_params
-- ----------------------------
INSERT INTO `jshop_goods_type_params` VALUES (1, 3);
INSERT INTO `jshop_goods_type_params` VALUES (2, 3);
INSERT INTO `jshop_goods_type_params` VALUES (3, 3);



-- ----------------------------
-- Records of jshop_goods_type_spec
-- ----------------------------
INSERT INTO `jshop_goods_type_spec` VALUES (3, '规格', 100);



-- ----------------------------
-- Records of jshop_goods_type_spec_rel
-- ----------------------------
INSERT INTO `jshop_goods_type_spec_rel` VALUES (3, 3);



-- ----------------------------
-- Records of jshop_goods_type_spec_value
-- ----------------------------
INSERT INTO `jshop_goods_type_spec_value` VALUES (32, 3, '规格1', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (33, 3, '规格2', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (34, 3, '规格3', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (35, 3, '规格4', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (36, 3, '规格5', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (37, 3, '规格6', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (38, 3, '规格7', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (39, 3, '规格8', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (40, 3, '规格9', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (41, 3, '规格10', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (42, 3, '规格11', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (43, 3, '规格12', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (44, 3, '规格13', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (45, 3, '规格14', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (46, 3, '规格15', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (47, 3, '规格16', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (48, 3, '规格17', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (49, 3, '规格18', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (50, 3, '规格19', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (51, 3, '规格20', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (52, 3, '规格21', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (53, 3, '规格22', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (54, 3, '规格23', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (55, 3, '规格24', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (56, 3, '规格25', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (57, 3, '规格26', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (58, 3, '规格27', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (59, 3, '规格28', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (60, 3, '规格29', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (61, 3, '规格30', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (62, 3, '规格31', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (63, 3, '规格32', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (64, 3, '规格33', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (65, 3, '规格34', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (66, 3, '规格35', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (67, 3, '规格36', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (68, 3, '规格37', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (69, 3, '规格38', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (70, 3, '规格39', 100);
INSERT INTO `jshop_goods_type_spec_value` VALUES (71, 3, '规格40', 100);

-- 修复促销信息过多时无法打开订单列表页和详情页问题
ALTER TABLE `jshop_order_items`
MODIFY COLUMN `promotion_list` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '促销信息' AFTER `promotion_amount`;


ALTER TABLE `jshop_pages_items` CHANGE `params` `params` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '组件配置内容';

ALTER TABLE `jshop_operation_log` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作数据序列号存储';

-- 用户日志增加类型
ALTER TABLE `jshop_user_log`
ADD COLUMN `type` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '类型，1会员，2管理员' AFTER `ip`;