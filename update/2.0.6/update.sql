DROP TABLE IF EXISTS `jshop_goods_extend_cat`;
CREATE TABLE `jshop_goods_extend_cat`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `goods_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '商品id',
  `goods_cat_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '商品分类id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '商品分类扩展表' ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;


ALTER TABLE `jshop_goods_cat`
ADD COLUMN `status` tinyint(1) UNSIGNED NULL DEFAULT 1 COMMENT '1=显示 2=不显示' AFTER `image_id`;



-- ----------------------------
-- Table structure for jshop_pintuan_goods
-- ----------------------------
DROP TABLE IF EXISTS `jshop_pintuan_goods`;
CREATE TABLE `jshop_pintuan_goods`  (
  `rule_id` int(11) UNSIGNED NOT NULL,
  `goods_id` int(11) UNSIGNED NOT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '拼团商品表' ROW_FORMAT = Compact;


-- ----------------------------
-- Table structure for jshop_pintuan_record
-- ----------------------------
DROP TABLE IF EXISTS `jshop_pintuan_record`;
CREATE TABLE `jshop_pintuan_record`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `team_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '团id',
  `user_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '用户Id',
  `rule_id` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '规则表Id',
  `goods_id` bigint(11) UNSIGNED NOT NULL COMMENT '商品id',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 1=拼团中2=成功 3=失败',
  `order_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单id',
  `params` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'json格式的参数，主要是拼团人数',
  `close_time` bigint(12) UNSIGNED NOT NULL COMMENT '关闭时间',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '拼团记录表' ROW_FORMAT = Compact;


-- ----------------------------
-- Table structure for jshop_pintuan_rule
-- ----------------------------
DROP TABLE IF EXISTS `jshop_pintuan_rule`;
CREATE TABLE `jshop_pintuan_rule`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '活动名称',
  `stime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '开始时间',
  `etime` bigint(12) UNSIGNED NULL DEFAULT NULL COMMENT '结束时间',
  `people_number` tinyint(1) UNSIGNED NOT NULL DEFAULT 2 COMMENT '人数 2-10人',
  `significant_interval` int(10) UNSIGNED NOT NULL DEFAULT 24 COMMENT '单位 小时',
  `discount_amount` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '优惠金额',
  `sort` smallint(5) UNSIGNED NOT NULL DEFAULT 100 COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1=开启（默认）  2=禁用',
  `ctime` bigint(12) NULL DEFAULT NULL COMMENT '创建时间',
  `utime` bigint(12) NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '拼团规则表' ROW_FORMAT = Compact;


ALTER TABLE `jshop_form`
ADD COLUMN `times` int(5) UNSIGNED NULL DEFAULT 0 COMMENT '可提交次数' AFTER `is_login`;





-- ----------------------------
-- Table structure for jshop_operation
-- ----------------------------
DROP TABLE IF EXISTS `jshop_operation`;
CREATE TABLE `jshop_operation`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) UNSIGNED NOT NULL COMMENT '父ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作名称',
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '操作编码',
  `type` enum('m','c','a') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'a' COMMENT '类型',
  `parent_menu_id` int(10) UNSIGNED NOT NULL COMMENT '菜单id',
  `perm_type` int(1) UNSIGNED NOT NULL DEFAULT 3 COMMENT '权限许可类型，如果为1就是主体权限，， 如果为2就是半主体权限，在左侧菜单不显示，但是在权限菜单上有体现，如果为3就是关联权限',
  `sort` smallint(5) UNSIGNED NULL DEFAULT 100 COMMENT '操作排序 越小越靠前',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `parent_id`(`parent_id`) USING BTREE,
  INDEX `id`(`id`) USING BTREE,
  INDEX `parent_menu_id`(`parent_menu_id`) USING BTREE,
  INDEX `sort`(`sort`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 586 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '权限表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of jshop_operation
-- ----------------------------
INSERT INTO `jshop_operation` VALUES (2, 1, '管理后台', 'manage', 'm', 1, 1, 200);
INSERT INTO `jshop_operation` VALUES (238, 2, '会员管理', 'User', 'c', 2, 1, 100);
INSERT INTO `jshop_operation` VALUES (239, 238, '用户列表', 'index', 'a', 238, 1, 100);
INSERT INTO `jshop_operation` VALUES (240, 238, '商品评价', 'comment', 'a', 305, 1, 101);
INSERT INTO `jshop_operation` VALUES (242, 238, '积分记录查询', 'pointlog', 'a', 239, 2, 100);
INSERT INTO `jshop_operation` VALUES (243, 238, '积分编辑', 'editpoint', 'a', 239, 2, 100);
INSERT INTO `jshop_operation` VALUES (244, 2, '控制面板', 'Setting', 'c', 2, 1, 200);
INSERT INTO `jshop_operation` VALUES (245, 244, '平台设置', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (246, 2, '支付方式', 'Payments', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (247, 246, '支付方式列表', 'index', 'a', 399, 1, 100);
INSERT INTO `jshop_operation` VALUES (249, 246, '支付方式修改', 'edit', 'a', 247, 2, 100);
INSERT INTO `jshop_operation` VALUES (251, 246, '支付方式启/禁用', 'changestatus', 'a', 249, 2, 100);
INSERT INTO `jshop_operation` VALUES (252, 2, '文章分类', 'ArticleType', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (253, 252, '文章分类', 'index', 'a', 266, 1, 100);
INSERT INTO `jshop_operation` VALUES (254, 252, '文章分类添加', 'add', 'a', 253, 2, 100);
INSERT INTO `jshop_operation` VALUES (255, 252, '文章分类修改', 'edit', 'a', 253, 2, 100);
INSERT INTO `jshop_operation` VALUES (256, 252, '文章分类删除', 'del', 'a', 253, 2, 100);
INSERT INTO `jshop_operation` VALUES (257, 252, '文章添加子分类', 'addson', 'a', 253, 3, 100);
INSERT INTO `jshop_operation` VALUES (258, 2, '文章管理', 'Article', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (259, 258, '文章列表', 'index', 'a', 266, 1, 100);
INSERT INTO `jshop_operation` VALUES (260, 258, '文章添加', 'add', 'a', 259, 2, 100);
INSERT INTO `jshop_operation` VALUES (261, 258, '文章修改', 'edit', 'a', 259, 2, 100);
INSERT INTO `jshop_operation` VALUES (262, 258, '文章删除', 'del', 'a', 259, 2, 100);
INSERT INTO `jshop_operation` VALUES (263, 2, '优惠券管理', 'Coupon', 'c', 2, 3, 140);
INSERT INTO `jshop_operation` VALUES (264, 263, '优惠券领取列表', 'index', 'a', 361, 2, 100);
INSERT INTO `jshop_operation` VALUES (265, 263, '优惠券删除', 'del', 'a', 361, 2, 100);
INSERT INTO `jshop_operation` VALUES (266, 2, '运营管理', 'Notice', 'c', 2, 1, 130);
INSERT INTO `jshop_operation` VALUES (267, 266, '公告列表', 'index', 'a', 266, 1, 100);
INSERT INTO `jshop_operation` VALUES (268, 266, '公告添加', 'add', 'a', 267, 2, 100);
INSERT INTO `jshop_operation` VALUES (269, 266, '公告修改', 'edit', 'a', 267, 2, 100);
INSERT INTO `jshop_operation` VALUES (270, 266, '公告删除', 'del', 'a', 267, 2, 100);
INSERT INTO `jshop_operation` VALUES (271, 2, '广告位管理', 'CarouselSeat', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (272, 271, '广告位列表', 'index', 'a', 266, 1, 100);
INSERT INTO `jshop_operation` VALUES (273, 271, '广告位添加', 'add', 'a', 272, 2, 100);
INSERT INTO `jshop_operation` VALUES (274, 271, '广告位修改', 'edit', 'a', 272, 2, 100);
INSERT INTO `jshop_operation` VALUES (275, 271, '广告位删除', 'del', 'a', 272, 2, 100);
INSERT INTO `jshop_operation` VALUES (277, 2, '广告管理', 'Carousel', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (278, 277, '广告列表', 'index', 'a', 266, 1, 100);
INSERT INTO `jshop_operation` VALUES (279, 277, '广告添加', 'add', 'a', 278, 2, 100);
INSERT INTO `jshop_operation` VALUES (280, 277, '广告修改', 'edit', 'a', 278, 2, 100);
INSERT INTO `jshop_operation` VALUES (281, 277, '广告删除', 'del', 'a', 278, 2, 100);
INSERT INTO `jshop_operation` VALUES (282, 277, '获取商品', 'getgoods', 'a', 278, 3, 100);
INSERT INTO `jshop_operation` VALUES (283, 277, '获取文章', 'getarticle', 'a', 278, 3, 100);
INSERT INTO `jshop_operation` VALUES (284, 277, '获取商品名称', 'goodsinfo', 'a', 278, 3, 100);
INSERT INTO `jshop_operation` VALUES (285, 277, '获取文章名称', 'articleinfo', 'a', 278, 3, 100);
INSERT INTO `jshop_operation` VALUES (286, 2, '品牌管理', 'Brand', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (287, 286, '品牌列表', 'index', 'a', 305, 1, 30);
INSERT INTO `jshop_operation` VALUES (288, 286, '品牌添加', 'add', 'a', 287, 2, 100);
INSERT INTO `jshop_operation` VALUES (289, 286, '品牌修改', 'edit', 'a', 287, 2, 100);
INSERT INTO `jshop_operation` VALUES (290, 286, '品牌删除', 'del', 'a', 287, 2, 100);
INSERT INTO `jshop_operation` VALUES (291, 2, '商品分类', 'Categories', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (292, 291, '商品分类', 'index', 'a', 305, 1, 20);
INSERT INTO `jshop_operation` VALUES (293, 291, '添加商品分类', 'add', 'a', 292, 2, 100);
INSERT INTO `jshop_operation` VALUES (294, 291, '编辑商品分类', 'edit', 'a', 292, 2, 100);
INSERT INTO `jshop_operation` VALUES (295, 291, '删除商品分类', 'del', 'a', 292, 2, 100);
INSERT INTO `jshop_operation` VALUES (296, 2, '订单管理', 'Order', 'c', 2, 1, 120);
INSERT INTO `jshop_operation` VALUES (297, 296, '订单列表', 'index', 'a', 296, 1, 90);
INSERT INTO `jshop_operation` VALUES (298, 296, '查看订单详情', 'view', 'a', 297, 3, 100);
INSERT INTO `jshop_operation` VALUES (299, 296, '编辑订单', 'edit', 'a', 297, 2, 100);
INSERT INTO `jshop_operation` VALUES (300, 296, '订单发货', 'ship', 'a', 297, 2, 100);
INSERT INTO `jshop_operation` VALUES (301, 296, '取消订单', 'cancel', 'a', 299, 3, 100);
INSERT INTO `jshop_operation` VALUES (302, 296, '完成订单', 'complete', 'a', 299, 3, 100);
INSERT INTO `jshop_operation` VALUES (303, 296, '删除订单', 'del', 'a', 297, 2, 100);
INSERT INTO `jshop_operation` VALUES (304, 296, '物流查询', 'logistics', 'a', 297, 3, 100);
INSERT INTO `jshop_operation` VALUES (305, 2, '商品管理', 'Goods', 'c', 2, 1, 110);
INSERT INTO `jshop_operation` VALUES (306, 305, '商品列表', 'index', 'a', 305, 1, 10);
INSERT INTO `jshop_operation` VALUES (307, 305, '添加商品', 'add', 'a', 306, 2, 100);
INSERT INTO `jshop_operation` VALUES (308, 305, '保存商品', 'doAdd', 'a', 307, 3, 100);
INSERT INTO `jshop_operation` VALUES (309, 305, '商品评价列表', 'commentlist', 'a', 306, 2, 100);
INSERT INTO `jshop_operation` VALUES (310, 305, '商品评价详情', 'getcommentinfo', 'a', 309, 3, 100);
INSERT INTO `jshop_operation` VALUES (311, 305, '商家回复', 'sellercontent', 'a', 309, 3, 100);
INSERT INTO `jshop_operation` VALUES (312, 305, '前台展示状态', 'setdisplay', 'a', 164, 3, 100);
INSERT INTO `jshop_operation` VALUES (313, 305, '获取子分类', 'getcat', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (314, 305, '获取规格信息', 'getspec', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (315, 305, '生成多规格', 'getspechtml', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (316, 305, '编辑商品', 'edit', 'a', 306, 2, 100);
INSERT INTO `jshop_operation` VALUES (317, 305, '保存编辑商品信息', 'doedit', 'a', 316, 3, 100);
INSERT INTO `jshop_operation` VALUES (318, 305, '批量上下架', 'batchmarketable', 'a', 316, 3, 100);
INSERT INTO `jshop_operation` VALUES (319, 305, '批量删除', 'batchdel', 'a', 321, 3, 100);
INSERT INTO `jshop_operation` VALUES (320, 305, '商品高级筛选', 'goodssearch', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (321, 305, '删除商品', 'del', 'a', 306, 2, 100);
INSERT INTO `jshop_operation` VALUES (322, 305, '设置热门或推荐', 'changestate', 'a', 316, 3, 100);
INSERT INTO `jshop_operation` VALUES (323, 2, '商品类型管理', 'GoodsType', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (324, 323, '商品类型', 'index', 'a', 305, 1, 40);
INSERT INTO `jshop_operation` VALUES (325, 323, '添加类型', 'add', 'a', 324, 2, 100);
INSERT INTO `jshop_operation` VALUES (326, 323, '关联属性', 'addrel', 'a', 324, 3, 100);
INSERT INTO `jshop_operation` VALUES (327, 323, '编辑属性', 'edit', 'a', 324, 2, 100);
INSERT INTO `jshop_operation` VALUES (328, 323, '删除属性', 'del', 'a', 324, 2, 100);
INSERT INTO `jshop_operation` VALUES (329, 323, '关联参数', 'addparams', 'a', 324, 3, 100);
INSERT INTO `jshop_operation` VALUES (330, 2, '商品属性', 'GoodsTypeSpec', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (331, 330, '商品属性', 'index', 'a', 305, 1, 50);
INSERT INTO `jshop_operation` VALUES (332, 330, '添加属性', 'add', 'a', 331, 3, 100);
INSERT INTO `jshop_operation` VALUES (333, 330, '编辑属性', 'edit', 'a', 331, 3, 100);
INSERT INTO `jshop_operation` VALUES (334, 330, '删除属性', 'del', 'a', 331, 3, 100);
INSERT INTO `jshop_operation` VALUES (335, 2, '配送方式', 'Ship', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (336, 335, '配送方式列表', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (337, 335, '添加配送方式', 'add', 'a', 336, 2, 100);
INSERT INTO `jshop_operation` VALUES (338, 335, '编辑配送方式', 'edit', 'a', 336, 2, 100);
INSERT INTO `jshop_operation` VALUES (339, 335, '删除配送方式', 'del', 'a', 336, 2, 100);
INSERT INTO `jshop_operation` VALUES (340, 335, '选择地区', 'choosearea', 'a', 336, 3, 100);
INSERT INTO `jshop_operation` VALUES (343, 2, '售后单管理', 'BillAftersales', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (344, 343, '售后单列表', 'index', 'a', 296, 1, 140);
INSERT INTO `jshop_operation` VALUES (345, 343, '售后单审核', 'audit', 'a', 344, 2, 100);
INSERT INTO `jshop_operation` VALUES (346, 343, '售后单查看', 'view', 'a', 344, 3, 100);
INSERT INTO `jshop_operation` VALUES (347, 2, '退货单管理', 'BillReship', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (348, 347, '退货单列表', 'index', 'a', 296, 1, 160);
INSERT INTO `jshop_operation` VALUES (349, 347, '退货单查看', 'view', 'a', 348, 3, 100);
INSERT INTO `jshop_operation` VALUES (350, 347, '退货单确认收货', 'confirmreship', 'a', 348, 2, 100);
INSERT INTO `jshop_operation` VALUES (351, 2, '退款单管理', 'BillRefund', 'c', 2, 3, 150);
INSERT INTO `jshop_operation` VALUES (352, 351, '退款单列表', 'index', 'a', 399, 1, 110);
INSERT INTO `jshop_operation` VALUES (353, 351, '退款单查看', 'view', 'a', 352, 3, 100);
INSERT INTO `jshop_operation` VALUES (354, 351, '操作退款', 'refund', 'a', 352, 2, 100);
INSERT INTO `jshop_operation` VALUES (355, 351, '再次退款', 'reaudit', 'a', 354, 3, 100);
INSERT INTO `jshop_operation` VALUES (356, 2, '促销管理', 'Promotion', 'c', 2, 1, 150);
INSERT INTO `jshop_operation` VALUES (357, 356, '促销列表', 'index', 'a', 356, 1, 100);
INSERT INTO `jshop_operation` VALUES (358, 356, '添加促销', 'add', 'a', 357, 2, 100);
INSERT INTO `jshop_operation` VALUES (359, 356, '促销编辑', 'edit', 'a', 357, 2, 100);
INSERT INTO `jshop_operation` VALUES (360, 356, '促销删除', 'del', 'a', 357, 2, 100);
INSERT INTO `jshop_operation` VALUES (361, 356, '优惠券列表', 'coupon', 'a', 356, 1, 100);
INSERT INTO `jshop_operation` VALUES (362, 356, '添加优惠券规则', 'couponadd', 'a', 361, 2, 100);
INSERT INTO `jshop_operation` VALUES (363, 356, '优惠券规则编辑', 'couponedit', 'a', 361, 2, 100);
INSERT INTO `jshop_operation` VALUES (364, 356, '优惠券规则删除', 'coupondel', 'a', 361, 2, 100);
INSERT INTO `jshop_operation` VALUES (365, 2, '门店管理', 'Store', 'c', 2, 3, 220);
INSERT INTO `jshop_operation` VALUES (366, 365, '门店列表', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (367, 365, '门店添加', 'add', 'a', 366, 2, 100);
INSERT INTO `jshop_operation` VALUES (368, 365, '门店修改', 'edit', 'a', 366, 2, 100);
INSERT INTO `jshop_operation` VALUES (369, 365, '门店删除', 'del', 'a', 366, 2, 100);
INSERT INTO `jshop_operation` VALUES (370, 365, '门店地图展现', 'showmap', 'a', 366, 3, 100);
INSERT INTO `jshop_operation` VALUES (390, 2, '图片列表', 'images', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (391, 390, '图片列表', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (392, 2, '导入导出管理', 'ietask', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (393, 392, '导入导出', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (394, 392, '导出', 'export', 'a', 306, 2, 100);
INSERT INTO `jshop_operation` VALUES (395, 392, '导入', 'import', 'a', 306, 2, 100);
INSERT INTO `jshop_operation` VALUES (396, 392, '下载导入模板', 'importtemplete', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (397, 392, '下载文件', 'down', 'a', 393, 2, 100);
INSERT INTO `jshop_operation` VALUES (398, 392, '执行下载文件', 'dodown', 'a', 397, 3, 100);
INSERT INTO `jshop_operation` VALUES (399, 2, '财务管理', 'BillPayments', 'c', 2, 1, 160);
INSERT INTO `jshop_operation` VALUES (400, 399, '支付单列表', 'index', 'a', 399, 1, 100);
INSERT INTO `jshop_operation` VALUES (401, 399, '后台支付', 'pay', 'a', 400, 2, 100);
INSERT INTO `jshop_operation` VALUES (402, 399, '后台支付实际操作', 'topay', 'a', 401, 3, 100);
INSERT INTO `jshop_operation` VALUES (403, 399, '支付单查看', 'index', 'a', 400, 3, 100);
INSERT INTO `jshop_operation` VALUES (404, 2, '发货单管理', 'BillDelivery', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (405, 404, '发货单列表', 'index', 'a', 296, 1, 100);
INSERT INTO `jshop_operation` VALUES (406, 404, '发货单详情', 'view', 'a', 405, 3, 100);
INSERT INTO `jshop_operation` VALUES (407, 404, '发货单明细', 'items', 'a', 405, 3, 100);
INSERT INTO `jshop_operation` VALUES (408, 2, '消息中心', 'MessageCenter', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (409, 408, '消息配置', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (410, 2, '角色管理', 'Role', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (411, 410, '角色管理', 'index', 'a', 238, 1, 210);
INSERT INTO `jshop_operation` VALUES (412, 410, '角色添加', 'add', 'a', 411, 2, 100);
INSERT INTO `jshop_operation` VALUES (413, 410, '角色删除', 'del', 'a', 411, 2, 100);
INSERT INTO `jshop_operation` VALUES (414, 410, '编辑权限', 'getoperation', 'a', 411, 2, 100);
INSERT INTO `jshop_operation` VALUES (415, 410, '角色保存权限', 'saveperm', 'a', 411, 3, 100);
INSERT INTO `jshop_operation` VALUES (416, 2, '管理员管理', 'Administrator', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (417, 416, '管理员管理', 'index', 'a', 238, 1, 200);
INSERT INTO `jshop_operation` VALUES (418, 416, '管理员添加', 'add', 'a', 417, 2, 100);
INSERT INTO `jshop_operation` VALUES (419, 416, '管理员修改', 'edit', 'a', 417, 2, 100);
INSERT INTO `jshop_operation` VALUES (420, 416, '管理员删除', 'del', 'a', 417, 2, 100);
INSERT INTO `jshop_operation` VALUES (421, 2, '商品参数', 'GoodsParams', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (422, 421, '参数列表', 'index', 'a', 305, 1, 100);
INSERT INTO `jshop_operation` VALUES (423, 421, '添加参数', 'add', 'a', 422, 2, 100);
INSERT INTO `jshop_operation` VALUES (424, 421, '编辑参数', 'edit', 'a', 422, 2, 100);
INSERT INTO `jshop_operation` VALUES (425, 421, '删除参数', 'del', 'a', 422, 2, 100);
INSERT INTO `jshop_operation` VALUES (426, 2, '标签管理', 'Label', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (427, 426, '设置标签', 'setlabel', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (428, 426, '设置标签', 'dosetlabel', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (429, 426, '删除标签', 'dellabel', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (430, 426, '删除标签', 'dodellabel', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (431, 2, '账户余额模块', 'Balance', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (432, 431, '账户资金管理', 'index', 'a', 399, 1, 150);
INSERT INTO `jshop_operation` VALUES (434, 2, '节点控制器', 'Operation', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (435, 434, '节点管理', 'index', 'a', 244, 1, 255);
INSERT INTO `jshop_operation` VALUES (436, 434, '节点删除', 'del', 'a', 435, 2, 100);
INSERT INTO `jshop_operation` VALUES (437, 434, '节点编辑', 'add', 'a', 435, 2, 100);
INSERT INTO `jshop_operation` VALUES (438, 2, '消息控制器', 'Message', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (439, 438, '消息管理', 'index', 'a', 239, 3, 300);
INSERT INTO `jshop_operation` VALUES (440, 438, '消息删除', 'del', 'a', 439, 3, 100);
INSERT INTO `jshop_operation` VALUES (441, 2, '短信控制器', 'Sms', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (442, 441, '短信管理', 'index', 'a', 238, 1, 310);
INSERT INTO `jshop_operation` VALUES (443, 441, '短信删除', 'del', 'a', 442, 2, 100);
INSERT INTO `jshop_operation` VALUES (460, 431, '提现列表', 'tocash', 'a', 399, 1, 100);
INSERT INTO `jshop_operation` VALUES (461, 431, '提现审核', 'tocashexamine', 'a', 460, 2, 100);
INSERT INTO `jshop_operation` VALUES (462, 2, '钩子列表', 'Hooks', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (463, 462, '钩子列表', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (464, 2, '插件列表', 'Addons', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (465, 464, '插件列表', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (466, 2, '地区管理', 'Area', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (467, 466, '地区管理', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (468, 466, '添加地区', 'add', 'a', 467, 2, 100);
INSERT INTO `jshop_operation` VALUES (469, 466, '编辑地区', 'edit', 'a', 467, 2, 100);
INSERT INTO `jshop_operation` VALUES (470, 466, '删除地区', 'del', 'a', 467, 2, 100);
INSERT INTO `jshop_operation` VALUES (471, 462, '添加钩子', 'add', 'a', 463, 2, 100);
INSERT INTO `jshop_operation` VALUES (472, 462, '编辑钩子', 'edit', 'a', 463, 2, 100);
INSERT INTO `jshop_operation` VALUES (473, 462, '删除钩子', 'del', 'a', 463, 2, 100);
INSERT INTO `jshop_operation` VALUES (474, 464, '安装插件', 'install', 'a', 465, 2, 100);
INSERT INTO `jshop_operation` VALUES (475, 464, '卸载插件', 'uninstall', 'a', 465, 2, 100);
INSERT INTO `jshop_operation` VALUES (476, 464, '插件配置', 'setting', 'a', 465, 2, 100);
INSERT INTO `jshop_operation` VALUES (477, 464, '保存配置', 'dosetting', 'a', 476, 3, 100);
INSERT INTO `jshop_operation` VALUES (478, 464, '插件启用/停用', 'changestatus', 'a', 465, 2, 100);
INSERT INTO `jshop_operation` VALUES (479, 2, '微信管理', 'Wechat', 'c', 2, 1, 220);
INSERT INTO `jshop_operation` VALUES (480, 479, '小程序配置', 'edit', 'a', 479, 1, 100);
INSERT INTO `jshop_operation` VALUES (481, 479, '模板列表', 'template', 'a', 479, 1, 100);
INSERT INTO `jshop_operation` VALUES (482, 479, '保存配置', 'doEdit', 'a', 480, 3, 100);
INSERT INTO `jshop_operation` VALUES (483, 479, '公众号配置', 'official', 'a', 479, 1, 100);
INSERT INTO `jshop_operation` VALUES (484, 365, '店员列表', 'clerklist', 'a', 366, 2, 100);
INSERT INTO `jshop_operation` VALUES (485, 365, '添加店员', 'addclerk', 'a', 366, 2, 100);
INSERT INTO `jshop_operation` VALUES (486, 365, '删除店员', 'delclerk', 'a', 366, 2, 100);
INSERT INTO `jshop_operation` VALUES (487, 2, '提货单列表', 'BillLading', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (488, 487, '提货单列表', 'index', 'a', 296, 1, 100);
INSERT INTO `jshop_operation` VALUES (489, 487, '提货单详情', 'info', 'a', 488, 3, 100);
INSERT INTO `jshop_operation` VALUES (490, 487, '删除提货单', 'dellading', 'a', 488, 2, 100);
INSERT INTO `jshop_operation` VALUES (491, 356, '团购秒杀列表', 'group', 'a', 356, 1, 100);
INSERT INTO `jshop_operation` VALUES (492, 356, '添加团购', 'groupadd', 'a', 491, 2, 100);
INSERT INTO `jshop_operation` VALUES (493, 356, '编辑团购', 'groupedit', 'a', 491, 2, 100);
INSERT INTO `jshop_operation` VALUES (494, 356, '删除团购', 'groupdel', 'a', 491, 2, 100);
INSERT INTO `jshop_operation` VALUES (495, 2, '物流公司', 'Logistics', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (496, 495, '物流公司列表', 'index', 'a', 244, 1, 200);
INSERT INTO `jshop_operation` VALUES (497, 495, '添加', 'add', 'a', 496, 2, 100);
INSERT INTO `jshop_operation` VALUES (498, 495, '编辑', 'edit', 'a', 496, 2, 100);
INSERT INTO `jshop_operation` VALUES (499, 495, '删除', 'del', 'a', 496, 2, 100);
INSERT INTO `jshop_operation` VALUES (500, 277, '获取文章分类', 'getarticletype', 'a', 278, 3, 100);
INSERT INTO `jshop_operation` VALUES (501, 277, '获取文章分类名称', 'articletypeinfo', 'a', 278, 3, 100);
INSERT INTO `jshop_operation` VALUES (502, 466, '地区列表', 'getlist', 'a', 467, 3, 100);
INSERT INTO `jshop_operation` VALUES (503, 487, '提货单核销', 'write', 'a', 488, 2, 100);
INSERT INTO `jshop_operation` VALUES (504, 286, '获取所有品牌', 'getAll', 'a', 287, 3, 100);
INSERT INTO `jshop_operation` VALUES (505, 291, '全部分类', 'getAll', 'a', 292, 3, 100);
INSERT INTO `jshop_operation` VALUES (506, 305, '更新排序', 'updatesort', 'a', 316, 3, 100);
INSERT INTO `jshop_operation` VALUES (507, 421, '弹窗参数', 'getlist', 'a', 422, 3, 100);
INSERT INTO `jshop_operation` VALUES (508, 323, '获取所有', 'getAll', 'a', 324, 3, 100);
INSERT INTO `jshop_operation` VALUES (509, 330, '弹窗属性列表', 'getlist', 'a', 331, 3, 100);
INSERT INTO `jshop_operation` VALUES (510, 408, '消息编辑', 'edit', 'a', 409, 2, 100);
INSERT INTO `jshop_operation` VALUES (511, 2, '操作日志', 'OperationLog', 'c', 2, 3, 100);
INSERT INTO `jshop_operation` VALUES (512, 511, '操作日志', 'index', 'a', 244, 1, 100);
INSERT INTO `jshop_operation` VALUES (517, 479, '公众号菜单', 'officialmenu', 'a', 479, 1, 100);
INSERT INTO `jshop_operation` VALUES (518, 479, '公众号菜单-编辑', 'officialmenu', 'a', 517, 2, 100);
INSERT INTO `jshop_operation` VALUES (519, 479, '公众号菜单-编辑保存', 'doeditmenu', 'a', 517, 2, 100);
INSERT INTO `jshop_operation` VALUES (520, 479, '公众号菜单-删除', 'deletemenu', 'a', 517, 2, 100);
INSERT INTO `jshop_operation` VALUES (521, 479, '公众号菜单-同步', 'updatemenu', 'a', 517, 2, 100);
INSERT INTO `jshop_operation` VALUES (522, 479, '微信消息管理', 'message', 'a', 479, 1, 100);
INSERT INTO `jshop_operation` VALUES (523, 479, '微信消息-添加', 'addMessage', 'a', 522, 2, 100);
INSERT INTO `jshop_operation` VALUES (524, 479, '微信消息-删除', 'delMessage', 'a', 522, 2, 100);
INSERT INTO `jshop_operation` VALUES (525, 479, '微信消息-编辑', 'editMessage', 'a', 522, 2, 100);
INSERT INTO `jshop_operation` VALUES (526, 479, '微信消息-互动', 'autoreply', 'a', 522, 2, 100);
INSERT INTO `jshop_operation` VALUES (527, 479, '编辑图文', 'editmediamessage', 'a', 522, 3, 100);
INSERT INTO `jshop_operation` VALUES (528, 479, '保存图文消息', 'doeditmediamessage', 'a', 522, 3, 100);
INSERT INTO `jshop_operation` VALUES (529, 238, '邀请人修改', 'editinvite', 'a', 239, 2, 100);
INSERT INTO `jshop_operation` VALUES (530, 238, '会员编辑', 'edituser', 'a', 239, 2, 100);
INSERT INTO `jshop_operation` VALUES (531, 238, '余额修改', 'editmoney', 'a', 239, 2, 100);
INSERT INTO `jshop_operation` VALUES (532, 238, '余额明细', 'moneylog', 'a', 239, 2, 100);
INSERT INTO `jshop_operation` VALUES (533, 238, '添加会员', 'adduser', 'a', 239, 2, 100);
INSERT INTO `jshop_operation` VALUES (534, 335, '选择地区', 'getarea', 'a', 336, 3, 100);
INSERT INTO `jshop_operation` VALUES (535, 2, '报表统计', 'Report', 'c', 2, 1, 210);
INSERT INTO `jshop_operation` VALUES (536, 535, '订单销量', 'order', 'a', 535, 1, 100);
INSERT INTO `jshop_operation` VALUES (537, 535, '财务收款', 'payments', 'a', 535, 1, 100);
INSERT INTO `jshop_operation` VALUES (538, 535, '商品销量', 'goods', 'a', 535, 1, 100);
INSERT INTO `jshop_operation` VALUES (539, 390, '删除图片', 'del', 'a', 391, 2, 100);
INSERT INTO `jshop_operation` VALUES (540, 392, '删除任务', 'del', 'a', 393, 3, 100);
INSERT INTO `jshop_operation` VALUES (541, 2, '智能表单', 'Form', 'c', 2, 1, 300);
INSERT INTO `jshop_operation` VALUES (542, 541, '表单列表', 'index', 'a', 541, 1, 100);
INSERT INTO `jshop_operation` VALUES (543, 541, '添加表单', 'add', 'a', 542, 2, 100);
INSERT INTO `jshop_operation` VALUES (544, 541, '编辑表单', 'edit', 'a', 542, 2, 100);
INSERT INTO `jshop_operation` VALUES (545, 541, '删除表单', 'del', 'a', 542, 2, 100);
INSERT INTO `jshop_operation` VALUES (546, 541, '小程序码', 'generate', 'a', 542, 2, 100);
INSERT INTO `jshop_operation` VALUES (547, 541, '提交列表', 'formsubmit', 'a', 541, 1, 100);
INSERT INTO `jshop_operation` VALUES (548, 541, '表单报表', 'report', 'a', 542, 2, 100);
INSERT INTO `jshop_operation` VALUES (549, 541, '表单统计', 'statistics', 'a', 542, 2, 100);
INSERT INTO `jshop_operation` VALUES (550, 541, '删除提交', 'delsubmit', 'a', 547, 2, 100);
INSERT INTO `jshop_operation` VALUES (551, 541, '提交明细', 'formsubmitdetail', 'a', 547, 2, 100);
INSERT INTO `jshop_operation` VALUES (552, 541, '反馈', 'editformsubmit', 'a', 547, 2, 100);
INSERT INTO `jshop_operation` VALUES (553, 238, '用户等级', 'grade', 'a', 238, 1, 150);
INSERT INTO `jshop_operation` VALUES (554, 238, '添加&修改', 'gradeadd', 'a', 553, 2, 100);
INSERT INTO `jshop_operation` VALUES (555, 238, '删除', 'gradedel', 'a', 553, 3, 100);
INSERT INTO `jshop_operation` VALUES (556, 296, '订单打印', 'print_tpl', 'a', 297, 3, 100);
INSERT INTO `jshop_operation` VALUES (557, 296, '打印选择快递', 'print_form', 'a', 297, 3, 100);
INSERT INTO `jshop_operation` VALUES (558, 535, '用户收藏统计', 'goodscollection', 'a', 535, 1, 100);
INSERT INTO `jshop_operation` VALUES (559, 2, '页面管理', 'Pages', 'c', 2, 1, 200);
INSERT INTO `jshop_operation` VALUES (560, 559, '布局管理', 'index', 'a', 559, 1, 100);
INSERT INTO `jshop_operation` VALUES (561, 559, '保存配置', 'savecustom', 'a', 560, 3, 100);
INSERT INTO `jshop_operation` VALUES (562, 559, '页面编辑', 'custom', 'a', 560, 2, 100);
INSERT INTO `jshop_operation` VALUES (563, 466, '生成缓存', 'generatecache', 'a', 467, 2, 100);
INSERT INTO `jshop_operation` VALUES (564, 263, '优惠券启用禁用', 'changestate', 'a', 361, 2, 100);
INSERT INTO `jshop_operation` VALUES (565, 356, '优惠券启用禁用', 'changestate', 'a', 361, 2, 100);
INSERT INTO `jshop_operation` VALUES (566, 305, '批量修改价格', 'batchmodifyprice', 'a', 306, 2, 100);
INSERT INTO `jshop_operation` VALUES (567, 305, '保存批量修改价格', 'dobatchmodifyprice', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (568, 305, '批量修改库存', 'batchmodifystock', 'a', 306, 2, 100);
INSERT INTO `jshop_operation` VALUES (569, 305, '保存批量修改库存', 'dobatchmodifystock', 'a', 306, 3, 100);
INSERT INTO `jshop_operation` VALUES (570, 559, '添加页面', 'add', 'a', 559, 2, 100);
INSERT INTO `jshop_operation` VALUES (571, 559, '删除页面', 'del', 'a', 559, 2, 100);
INSERT INTO `jshop_operation` VALUES (572, 2, '拼团管理', 'Pintuan', 'c', 356, 1, 100);
INSERT INTO `jshop_operation` VALUES (573, 572, '拼团列表', 'index', 'a', 572, 1, 100);
INSERT INTO `jshop_operation` VALUES (574, 572, '删除拼团', 'del', 'a', 573, 2, 100);
INSERT INTO `jshop_operation` VALUES (575, 572, '添加编辑拼团', 'edit', 'a', 573, 2, 100);
INSERT INTO `jshop_operation` VALUES (576, 572, '更新排序', 'updatesort', 'a', 573, 2, 100);
INSERT INTO `jshop_operation` VALUES (577, 572, '拼团禁用启用', 'changestate', 'a', 573, 2, 100);
INSERT INTO `jshop_operation` VALUES (578, 572, '拼团选择商品', 'getgoods', 'a', 573, 3, 100);
INSERT INTO `jshop_operation` VALUES (579, 238, '删除会员', 'deluser', 'a', 239, 2, 100);
INSERT INTO `jshop_operation` VALUES (580, 291, '是否显示', 'changestate', 'a', 292, 2, 100);
INSERT INTO `jshop_operation` VALUES (581, 296, '保存备注', 'savemark', 'a', 297, 2, 100);
INSERT INTO `jshop_operation` VALUES (582, 399, '查看支付单详情', 'view', 'a', 400, 2, 100);
INSERT INTO `jshop_operation` VALUES (583, 464, '插件刷新', 'refresh', 'a', 465, 2, 100);
INSERT INTO `jshop_operation` VALUES (584, 479, '编辑菜单', 'editmenu', 'a', 517, 2, 100);
INSERT INTO `jshop_operation` VALUES (585, 572, '拼团记录', 'record', 'a', 572, 1, 100);



INSERT INTO `jshop_hooks`(`id`, `name`, `description`, `type`, `addons`, `ctime`, `utime`) VALUES (10006, 'appupdate', 'APP相关钩子', 1, NULL, 1564537798, 1564537798);
INSERT INTO `jshop_hooks`(`id`, `name`, `description`, `type`, `addons`, `ctime`, `utime`) VALUES (10007, 'aftersalesreview', '售后审核通过后', 1, NULL, 1564537798, 1564537798);
INSERT INTO `jshop_hooks`(`id`, `name`, `description`, `type`, `addons`, `ctime`, `utime`) VALUES (10008, 'addUserAfter', '创建会员后', 1, NULL, 1564537798, 1564537798);
INSERT INTO `jshop_hooks`(`id`, `name`, `description`, `type`, `addons`, `ctime`, `utime`) VALUES (10010, 'orderFinish', '订单完成后', 1, NULL, 1564537798, 1564537798);
INSERT INTO `jshop_hooks`(`id`, `name`, `description`, `type`, `addons`, `ctime`, `utime`) VALUES (10011, 'loginAfter', '登录后事件', 1, NULL, NULL, 0);
INSERT INTO `jshop_hooks`(`id`, `name`, `description`, `type`, `addons`, `ctime`, `utime`) VALUES (10012, 'loginAfter', '登录后事件', 1, NULL, 1564537798, 1564537798);
