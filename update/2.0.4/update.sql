ALTER TABLE `jshop_cart` ADD `type` TINYINT(2) UNSIGNED NOT NULL DEFAULT '1' COMMENT '购物车类型,1普通类型，2拼团类型' ;


 ALTER TABLE  `jshop_store` ADD  `latitude` VARCHAR( 40 ) NULL DEFAULT NULL COMMENT  '纬度' AFTER  `coordinate` ,
ADD  `longitude` VARCHAR( 40 ) NULL DEFAULT NULL COMMENT  '经度' AFTER  `latitude` ;

ALTER TABLE `jshop_area`
MODIFY COLUMN `postal_code` varchar(10) NOT NULL DEFAULT '' COMMENT '邮编' AFTER `name`;


DROP TABLE IF EXISTS `jshop_pages`;
CREATE TABLE `jshop_pages`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '可视化区域编码',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '可编辑区域名称',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '描述',
  `layout` tinyint(2) UNSIGNED NULL DEFAULT 1 COMMENT '布局样式编码，1，手机端',
  `type` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '1手机端，2PC端',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;


-- ----------------------------
-- Records of jshop_pages
-- ----------------------------
INSERT INTO `jshop_pages` VALUES (1, 'mobile_home', '移动端首页', '移动端首页相关操作，可视化移动端、小程序端首页布局', 1, 1);



DROP TABLE IF EXISTS `jshop_pages_items`;
CREATE TABLE `jshop_pages_items`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `widget_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '组件编码',
  `page_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '页面编码',
  `position_id` tinyint(2) UNSIGNED NOT NULL DEFAULT 1 COMMENT '布局位置',
  `sort` tinyint(2) UNSIGNED NOT NULL DEFAULT 1 COMMENT '排序，越小越靠前',
  `params` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '组件配置内容',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;


INSERT INTO `jshop_pages_items` VALUES (5, 'search', 'mobile_home', 0, 1, '{\"keywords\":\"请输入关键字搜索\",\"style\":\"round\"}');
INSERT INTO `jshop_pages_items` VALUES (6, 'imgSlide', 'mobile_home', 1, 2, '{\"duration\":2500,\"list\":[{\"image\":\"\\/static\\/images\\/empty-banner.png\",\"linkType\":\"\",\"linkValue\":\"\"},{\"image\":\"\\/static\\/images\\/empty-banner.png\",\"linkType\":\"\",\"linkValue\":\"\"}]}');
INSERT INTO `jshop_pages_items` VALUES (7, 'notice', 'mobile_home', 2, 3, '{\"type\":\"auto\",\"list\":[{\"title\":\"这里是第一条公告的标题\",\"content\":\"\",\"id\":\"\"}]}');
INSERT INTO `jshop_pages_items` VALUES (8, 'navBar', 'mobile_home', 3, 4, '{\"limit\":4,\"list\":[{\"image\":\"\\/static\\/images\\/empty.png\",\"text\":\"按钮1\",\"linkType\":\"\",\"linkValue\":\"\"},{\"image\":\"\\/static\\/images\\/empty.png\",\"text\":\"按钮2\",\"linkType\":\"\",\"linkValue\":\"\"},{\"image\":\"\\/static\\/images\\/empty.png\",\"text\":\"按钮3\",\"linkType\":\"\",\"linkValue\":\"\"},{\"image\":\"\\/static\\/images\\/empty.png\",\"text\":\"按钮4\",\"linkType\":\"\",\"linkValue\":\"\"}]}');
INSERT INTO `jshop_pages_items` VALUES (9, 'goods', 'mobile_home', 4, 5, '{\"title\":\"热门商品\",\"lookMore\":\"true\",\"type\":\"auto\",\"classifyId\":\"\",\"brandId\":\"\",\"limit\":10,\"display\":\"list\",\"column\":2,\"list\":[{\"image\":\"\\/static\\/images\\/empty-banner.png\",\"name\":\"\",\"price\":\"\"},{\"image\":\"\\/static\\/images\\/empty-banner.png\",\"name\":\"\",\"price\":\"\"},{\"image\":\"\\/static\\/images\\/empty-banner.png\",\"name\":\"\",\"price\":\"\"},{\"image\":\"\\/static\\/images\\/empty-banner.png\",\"name\":\"\",\"price\":\"\"}]}');
INSERT INTO `jshop_pages_items` VALUES (10, 'imgSlide', 'mobile_home', 5, 6, '{\"duration\":2500,\"list\":[{\"image\":\"\\/static\\/images\\/empty-banner.png\",\"linkType\":\"\",\"linkValue\":\"\"},{\"image\":\"\\/static\\/images\\/empty-banner.png\",\"linkType\":\"\",\"linkValue\":\"\"}]}');
INSERT INTO `jshop_pages_items` VALUES (11, 'goods', 'mobile_home', 6, 7, '{\"title\":\"推荐商品\",\"lookMore\":\"true\",\"type\":\"auto\",\"classifyId\":\"\",\"brandId\":\"\",\"limit\":10,\"display\":\"list\",\"column\":2,\"list\":[{\"image\":\"\\/static\\/images\\/empty-banner.png\",\"name\":\"\",\"price\":\"\"},{\"image\":\"\\/static\\/images\\/empty-banner.png\",\"name\":\"\",\"price\":\"\"},{\"image\":\"\\/static\\/images\\/empty-banner.png\",\"name\":\"\",\"price\":\"\"},{\"image\":\"\\/static\\/images\\/empty-banner.png\",\"name\":\"\",\"price\":\"\"}]}');
INSERT INTO `jshop_pages_items` VALUES (12, 'imgSingle', 'mobile_home', 7, 8, '{\"list\":[{\"image\":\"\\/static\\/images\\/empty-banner.png\",\"linkType\":\"\",\"linkValue\":\"\"}]}');



ALTER TABLE `jshop_goods`
MODIFY COLUMN `stock` int(8) UNSIGNED NULL DEFAULT 0 COMMENT '库存' AFTER `marketable`;

ALTER TABLE `jshop_goods`
MODIFY COLUMN `freeze_stock` int(8) UNSIGNED NULL DEFAULT 0 COMMENT '冻结库存' AFTER `stock`;


ALTER TABLE `jshop_products`
MODIFY COLUMN `stock` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '库存' AFTER `marketable`,
MODIFY COLUMN `freeze_stock` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '冻结库存' AFTER `stock`;

ALTER TABLE `jshop_order` ADD `order_type` TINYINT(2) UNSIGNED NOT NULL DEFAULT '1' COMMENT '订单类型，1是普通订单，2是拼团订单' AFTER `status`;


 ALTER TABLE `jshop_goods`
ADD COLUMN `new_spec` text NULL COMMENT '自定义规格名称' AFTER `label_ids`;



INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (559, 2, '页面管理', 'Pages', 'c', 2, 1, 200);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (560, 559, '布局管理', 'index', 'a', 559, 1, 100);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (561, 559, '保存配置', 'savecustom', 'a', 560, 3, 100);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (562, 559, '页面编辑', 'custom', 'a', 560, 2, 100);
INSERT INTO `jshop_operation`(`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES (563, 466, '生成缓存', 'generatecache', 'a', 467, 2, 100);
