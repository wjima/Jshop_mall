INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (NULL, '296', '订单编辑-订单明细显示', 'edititemslist', 'a', '299', '3', '100'), (NULL, '296', '订单编辑-订单明细添加', 'edititemsadd', 'a', '299', '3', '100');
INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (NULL, '296', '订单编辑-订单明细删除', 'edititemsdel', 'a', '299', '3', '100'), (NULL, '296', '订单编辑-订单明细编辑', 'edititemsedit', 'a', '299', '3', '100');
ALTER TABLE `jshop_sms`
MODIFY COLUMN `mobile` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '手机号码，允许同时存在多个手机号码，英文逗号分隔' AFTER `id`;

ALTER TABLE `jshop_images` CHANGE `name` `name` VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '图片名称';


ALTER TABLE `jshop_order`
MODIFY COLUMN `promotion_list` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '优惠信息' AFTER `coupon`;

ALTER TABLE `jshop_user`
MODIFY COLUMN `balance` decimal(10, 2) NULL DEFAULT 0.00 COMMENT '余额' AFTER `nickname`,
MODIFY COLUMN `point` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '积分' AFTER `balance`,
MODIFY COLUMN `grade` tinyint(2) UNSIGNED NULL DEFAULT 0 COMMENT '用户等级' AFTER `point`,
MODIFY COLUMN `remarks` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '备注' AFTER `isdel`;


ALTER TABLE `jshop_pages`
ADD COLUMN `is_main` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '是否首页' AFTER `type`;

-- 更新首页配置
UPDATE `jshop_pages` SET `is_main` = 1 WHERE `code` = 'mobile_home'


ALTER TABLE `jshop_coupon` ADD `endtime` BIGINT(12) UNSIGNED NOT NULL DEFAULT '0' COMMENT '到期时间' AFTER `used_id`;

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

-- 296   订单管理(Order)     297  订单列表(index)
INSERT INTO `jshop_operation`( `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (296, '申请售后', 'aftersales', 'a', 297, 2, 100);
-- 347 退货单管理(BillReship)    348 退货单列表(index)
INSERT INTO `jshop_operation`( `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (347, '填写退货单', 'reship', 'a', 348, 2, 100);
