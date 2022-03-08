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


ALTER TABLE `jshop_goods_comment`
ADD COLUMN `product_id` int(10) NOT NULL DEFAULT '0' COMMENT '货品ID 关联products.id' AFTER `goods_id`,
ADD COLUMN `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '商品名称' AFTER `order_id`;


ALTER TABLE `jshop_pages`
ADD COLUMN `is_main` tinyint(1) UNSIGNED NULL DEFAULT 2 COMMENT '是否首页' AFTER `type`;

-- 更新首页配置
UPDATE `jshop_pages` SET `is_main` = 1 WHERE `code` = 'mobile_home';


INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (591, 296, '订单编辑-订单明细显示', 'edititemslist', 'a', 299, 3, 100);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (592, 296, '订单编辑-订单明细添加', 'edititemsadd', 'a', 299, 3, 100);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (593, 296, '订单编辑-订单明细删除', 'edititemsdel', 'a', 299, 3, 100);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (594, 296, '订单编辑-订单明细编辑', 'edititemsedit', 'a', 299, 3, 100);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (595, 2, '砍价管理', 'Bargain', 'c', 356, 1, 100);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (596, 595, '砍价列表', 'index', 'a', 595, 1, 1);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (597, 595, '添加编辑砍价', 'edit', 'a', 596, 2, 100);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (598, 595, '删除砍价', 'del', 'a', 596, 2, 100);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (599, 595, '更改排序', 'updatesort', 'a', 596, 2, 100);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (600, 595, '更改状态', 'changestate', 'a', 596, 2, 100);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (601, 595, '参与记录', 'record', 'a', 595, 1, 2);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (602, 595, '砍价日志', 'recordlog', 'a', 595, 1, 3);
