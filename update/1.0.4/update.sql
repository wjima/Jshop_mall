-- 优化表结构

ALTER TABLE  `jshop_advertisement` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_advert_position`  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_article` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_article_type` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_backstage_notice` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_goods_params` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_goods_type_params` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_hooks` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_ietask` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_jobs` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_label` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_message_center` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_notice` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_template` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_template_order` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_weixin_author` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_weixin_publish` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_worksheet` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
ALTER TABLE  `jshop_wsdetail` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

ALTER TABLE  `jshop_advertisement` ADD INDEX (  `id` ) ;
ALTER TABLE  `jshop_advertisement` CHANGE  `id`  `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ;

ALTER TABLE `jshop_coupon`
MODIFY COLUMN `user_id`  int(10) UNSIGNED NULL COMMENT '谁领取了' AFTER `is_used`,
MODIFY COLUMN `used_id`  int(10) UNSIGNED NULL COMMENT '被谁用了' AFTER `user_id`;

ALTER TABLE `jshop_user_wx` ADD `type` TINYINT(1) UNSIGNED NOT NULL COMMENT '第三方登录类型，1微信小程序，2微信公众号' AFTER `id`;


ALTER TABLE `jshop_bill_aftersales`
MODIFY COLUMN `mark`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '卖家备注，如果审核失败了，会显示到前端' AFTER `reason`;


--
-- 表的结构 `jshop_weixin_menu`
--

CREATE TABLE IF NOT EXISTS `jshop_weixin_menu` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) NOT NULL COMMENT '菜单id',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '父级菜单',
  `name` varchar(100) CHARACTER SET utf8 NOT NULL COMMENT '菜单名称',
  `type` varchar(11) CHARACTER SET utf8 NOT NULL COMMENT '菜单类型',
  `params` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '菜单参数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='微信公众号菜单表' AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- 表的结构 `jshop_weixin_message`
--

CREATE TABLE IF NOT EXISTS `jshop_weixin_message` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8 DEFAULT NULL COMMENT '消息名称',
  `type` tinyint(1) DEFAULT '1' COMMENT '消息类型1:文本消息，2:图文消息',
  `params` text CHARACTER SET utf8 COMMENT '消息参数',
  `ctime` bigint(12) DEFAULT '0' COMMENT '创建时间',
  `utime` bigint(12) DEFAULT '0' COMMENT '更新时间',
  `is_attention` tinyint(1) DEFAULT '2' COMMENT '关注回复，1是关注回复，2不是关注回复',
  `is_default` tinyint(1) DEFAULT '2' COMMENT '是否默认回复，1是，2不是',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='微信消息表' AUTO_INCREMENT=8 ;


-- 菜单更新


DROP TABLE IF EXISTS `jshop_operation`;
CREATE TABLE `jshop_operation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL COMMENT '父ID',
  `name` varchar(50) DEFAULT NULL COMMENT '操作名称',
  `code` varchar(50) DEFAULT NULL COMMENT '操作编码',
  `type` enum('m','c','a') NOT NULL DEFAULT 'a' COMMENT '类型',
  `parent_menu_id` int(10) unsigned NOT NULL COMMENT '菜单id',
  `perm_type` int(1) unsigned NOT NULL DEFAULT '3' COMMENT '权限许可类型，如果为1就是主体权限，， 如果为2就是半主体权限，在左侧菜单不显示，但是在权限菜单上有体现，如果为3就是关联权限',
  `sort` tinyint(3) unsigned DEFAULT '100' COMMENT '操作排序 越小越靠前',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `id` (`id`),
  KEY `parent_menu_id` (`parent_menu_id`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB AUTO_INCREMENT=505 DEFAULT CHARSET=utf8mb4 COMMENT='权限表';

-- ----------------------------
-- Records of jshop_operation
-- ----------------------------
INSERT INTO `jshop_operation` VALUES ('2', '1', '管理后台', 'manage', 'm', '1', '1', '200');
INSERT INTO `jshop_operation` VALUES ('91', '88', '添加物流公司', 'add', 'a', '0', '3', '100');
INSERT INTO `jshop_operation` VALUES ('155', '129', '商品评价查看', 'getcommentinfo', 'a', '129', '3', '100');
INSERT INTO `jshop_operation` VALUES ('156', '129', '商品评价回复', 'sellercontent', 'a', '129', '2', '100');
INSERT INTO `jshop_operation` VALUES ('238', '2', '会员管理', 'User', 'c', '2', '1', '100');
INSERT INTO `jshop_operation` VALUES ('239', '238', '用户列表', 'index', 'a', '238', '1', '100');
INSERT INTO `jshop_operation` VALUES ('240', '238', '商品评价', 'comment', 'a', '238', '1', '101');
INSERT INTO `jshop_operation` VALUES ('242', '238', '积分记录查询', 'pointLog', 'a', '241', '3', '100');
INSERT INTO `jshop_operation` VALUES ('243', '238', '积分编辑', 'editPoint', 'a', '241', '2', '100');
INSERT INTO `jshop_operation` VALUES ('244', '2', '控制面板', 'Setting', 'c', '2', '1', '200');
INSERT INTO `jshop_operation` VALUES ('245', '244', '店铺设置', 'index', 'a', '244', '1', '100');
INSERT INTO `jshop_operation` VALUES ('246', '2', '支付方式', 'Payments', 'c', '2', '2', '100');
INSERT INTO `jshop_operation` VALUES ('247', '246', '支付方式列表', 'index', 'a', '244', '1', '100');
INSERT INTO `jshop_operation` VALUES ('248', '246', '支付方式添加', 'add', 'a', '247', '2', '100');
INSERT INTO `jshop_operation` VALUES ('249', '246', '支付方式修改', 'edit', 'a', '247', '2', '100');
INSERT INTO `jshop_operation` VALUES ('250', '246', '支付方式删除', 'del', 'a', '247', '2', '100');
INSERT INTO `jshop_operation` VALUES ('251', '246', '支付方式启/禁用', 'changeStatus', 'a', '249', '3', '100');
INSERT INTO `jshop_operation` VALUES ('252', '2', '文章分类', 'ArticleType', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('253', '252', '文章分类', 'index', 'a', '266', '1', '100');
INSERT INTO `jshop_operation` VALUES ('254', '252', '文章分类添加', 'add', 'a', '253', '2', '100');
INSERT INTO `jshop_operation` VALUES ('255', '252', '文章分类修改', 'edit', 'a', '253', '2', '100');
INSERT INTO `jshop_operation` VALUES ('256', '252', '文章分类删除', 'del', 'a', '253', '2', '100');
INSERT INTO `jshop_operation` VALUES ('257', '252', '文章添加子分类', 'addson', 'a', '253', '3', '100');
INSERT INTO `jshop_operation` VALUES ('258', '2', '文章管理', 'Article', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('259', '258', '文章列表', 'index', 'a', '266', '1', '100');
INSERT INTO `jshop_operation` VALUES ('260', '258', '文章添加', 'add', 'a', '259', '2', '100');
INSERT INTO `jshop_operation` VALUES ('261', '258', '文章修改', 'edit', 'a', '259', '2', '100');
INSERT INTO `jshop_operation` VALUES ('262', '258', '文章删除', 'del', 'a', '259', '2', '100');
INSERT INTO `jshop_operation` VALUES ('263', '2', '优惠券管理', 'Coupon', 'c', '0', '1', '140');
INSERT INTO `jshop_operation` VALUES ('264', '263', '优惠券领取列表', 'index', 'a', '361', '2', '100');
INSERT INTO `jshop_operation` VALUES ('265', '263', '优惠券删除', 'del', 'a', '361', '2', '100');
INSERT INTO `jshop_operation` VALUES ('266', '2', '运营管理', 'Notice', 'c', '2', '1', '130');
INSERT INTO `jshop_operation` VALUES ('267', '266', '公告列表', 'index', 'a', '266', '1', '100');
INSERT INTO `jshop_operation` VALUES ('268', '266', '公告添加', 'add', 'a', '267', '2', '100');
INSERT INTO `jshop_operation` VALUES ('269', '266', '公告修改', 'edit', 'a', '267', '2', '100');
INSERT INTO `jshop_operation` VALUES ('270', '266', '公告删除', 'del', 'a', '267', '2', '100');
INSERT INTO `jshop_operation` VALUES ('271', '2', '广告位管理', 'AdvertPosition', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('272', '271', '广告位列表', 'index', 'a', '266', '1', '100');
INSERT INTO `jshop_operation` VALUES ('273', '271', '广告位添加', 'add', 'a', '272', '2', '100');
INSERT INTO `jshop_operation` VALUES ('274', '271', '广告位修改', 'edit', 'a', '272', '2', '100');
INSERT INTO `jshop_operation` VALUES ('275', '271', '广告位删除', 'del', 'a', '272', '2', '100');
INSERT INTO `jshop_operation` VALUES ('276', '271', '广告位启/禁用', 'changeState', 'a', '272', '3', '100');
INSERT INTO `jshop_operation` VALUES ('277', '2', '广告管理', 'Advertisement', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('278', '277', '广告列表', 'index', 'a', '266', '1', '100');
INSERT INTO `jshop_operation` VALUES ('279', '277', '广告添加', 'add', 'a', '278', '2', '100');
INSERT INTO `jshop_operation` VALUES ('280', '277', '广告修改', 'edit', 'a', '278', '2', '100');
INSERT INTO `jshop_operation` VALUES ('281', '277', '广告删除', 'del', 'a', '278', '2', '100');
INSERT INTO `jshop_operation` VALUES ('282', '277', '获取商品', 'getGoods', 'a', '278', '3', '100');
INSERT INTO `jshop_operation` VALUES ('283', '277', '获取文章', 'getArticle', 'a', '278', '3', '100');
INSERT INTO `jshop_operation` VALUES ('284', '277', '获取商品名称', 'goodsInfo', 'a', '278', '3', '100');
INSERT INTO `jshop_operation` VALUES ('285', '277', '获取文章名称', 'articleInfo', 'a', '278', '3', '100');
INSERT INTO `jshop_operation` VALUES ('286', '2', '品牌管理', 'Brand', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('287', '286', '品牌列表', 'index', 'a', '305', '1', '30');
INSERT INTO `jshop_operation` VALUES ('288', '286', '品牌添加', 'add', 'a', '287', '2', '100');
INSERT INTO `jshop_operation` VALUES ('289', '286', '品牌修改', 'edit', 'a', '287', '2', '100');
INSERT INTO `jshop_operation` VALUES ('290', '286', '品牌删除', 'del', 'a', '287', '2', '100');
INSERT INTO `jshop_operation` VALUES ('291', '2', '商品分类', 'Categories', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('292', '291', '商品分类', 'index', 'a', '305', '1', '20');
INSERT INTO `jshop_operation` VALUES ('293', '291', '添加商品分类', 'add', 'a', '292', '2', '100');
INSERT INTO `jshop_operation` VALUES ('294', '291', '编辑商品分类', 'edit', 'a', '292', '2', '100');
INSERT INTO `jshop_operation` VALUES ('295', '291', '删除商品分类', 'del', 'a', '292', '2', '100');
INSERT INTO `jshop_operation` VALUES ('296', '2', '订单管理', 'Order', 'c', '2', '1', '120');
INSERT INTO `jshop_operation` VALUES ('297', '296', '订单列表', 'index', 'a', '296', '1', '90');
INSERT INTO `jshop_operation` VALUES ('298', '296', '查看订单详情', 'view', 'a', '297', '3', '100');
INSERT INTO `jshop_operation` VALUES ('299', '296', '编辑订单', 'edit', 'a', '297', '2', '100');
INSERT INTO `jshop_operation` VALUES ('300', '296', '订单发货', 'ship', 'a', '297', '2', '100');
INSERT INTO `jshop_operation` VALUES ('301', '296', '取消订单', 'cancel', 'a', '299', '3', '100');
INSERT INTO `jshop_operation` VALUES ('302', '296', '完成订单', 'complete', 'a', '299', '3', '100');
INSERT INTO `jshop_operation` VALUES ('303', '296', '删除订单', 'del', 'a', '297', '2', '100');
INSERT INTO `jshop_operation` VALUES ('304', '296', '物流查询', 'logistics', 'a', '297', '3', '100');
INSERT INTO `jshop_operation` VALUES ('305', '2', '商品管理', 'Goods', 'c', '2', '1', '110');
INSERT INTO `jshop_operation` VALUES ('306', '305', '商品列表', 'index', 'a', '305', '1', '10');
INSERT INTO `jshop_operation` VALUES ('307', '305', '添加商品', 'add', 'a', '306', '2', '100');
INSERT INTO `jshop_operation` VALUES ('308', '305', '保存商品', 'doAdd', 'a', '307', '3', '100');
INSERT INTO `jshop_operation` VALUES ('309', '305', '商品评价列表', 'commentList', 'a', '306', '2', '100');
INSERT INTO `jshop_operation` VALUES ('310', '305', '商品评价详情', 'getCommentInfo', 'a', '309', '3', '100');
INSERT INTO `jshop_operation` VALUES ('311', '305', '商家回复', 'sellerContent', 'a', '309', '3', '100');
INSERT INTO `jshop_operation` VALUES ('312', '305', '前台展示状态', 'setDisplay', 'a', '164', '3', '100');
INSERT INTO `jshop_operation` VALUES ('313', '305', '获取子分类', 'getCat', 'a', '306', '3', '100');
INSERT INTO `jshop_operation` VALUES ('314', '305', '获取规格信息', 'getSpec', 'a', '306', '3', '100');
INSERT INTO `jshop_operation` VALUES ('315', '305', '生成多规格', 'getSpecHtml', 'a', '306', '3', '100');
INSERT INTO `jshop_operation` VALUES ('316', '305', '编辑商品', 'edit', 'a', '306', '2', '100');
INSERT INTO `jshop_operation` VALUES ('317', '305', '保存编辑商品信息', 'doEdit', 'a', '316', '3', '100');
INSERT INTO `jshop_operation` VALUES ('318', '305', '批量上下架', 'batchMarketable', 'a', '316', '3', '100');
INSERT INTO `jshop_operation` VALUES ('319', '305', '批量删除', 'batchDel', 'a', '321', '3', '100');
INSERT INTO `jshop_operation` VALUES ('320', '305', '商品高级筛选', 'goodsSearch', 'a', '306', '3', '100');
INSERT INTO `jshop_operation` VALUES ('321', '305', '删除商品', 'del', 'a', '306', '2', '100');
INSERT INTO `jshop_operation` VALUES ('322', '305', '设置热门或推荐', 'changeState', 'a', '316', '3', '100');
INSERT INTO `jshop_operation` VALUES ('323', '2', '商品类型管理', 'GoodsType', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('324', '323', '商品类型', 'index', 'a', '305', '1', '40');
INSERT INTO `jshop_operation` VALUES ('325', '323', '添加类型', 'add', 'a', '324', '2', '100');
INSERT INTO `jshop_operation` VALUES ('326', '323', '关联属性', 'addrel', 'a', '324', '3', '100');
INSERT INTO `jshop_operation` VALUES ('327', '323', '编辑属性', 'edit', 'a', '324', '2', '100');
INSERT INTO `jshop_operation` VALUES ('328', '323', '删除属性', 'del', 'a', '324', '2', '100');
INSERT INTO `jshop_operation` VALUES ('329', '323', '关联参数', 'addparams', 'a', '324', '3', '100');
INSERT INTO `jshop_operation` VALUES ('330', '2', '商品属性', 'GoodsTypeSpec', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('331', '330', '商品属性', 'index', 'a', '305', '1', '50');
INSERT INTO `jshop_operation` VALUES ('332', '330', '添加属性', 'add', 'a', '331', '3', '100');
INSERT INTO `jshop_operation` VALUES ('333', '330', '编辑属性', 'edit', 'a', '331', '3', '100');
INSERT INTO `jshop_operation` VALUES ('334', '330', '删除属性', 'del', 'a', '331', '3', '100');
INSERT INTO `jshop_operation` VALUES ('335', '2', '配送方式', 'Ship', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('336', '335', '配送方式列表', 'index', 'a', '244', '1', '100');
INSERT INTO `jshop_operation` VALUES ('337', '335', '添加配送方式', 'add', 'a', '336', '2', '100');
INSERT INTO `jshop_operation` VALUES ('338', '335', '编辑配送方式', 'edit', 'a', '336', '2', '100');
INSERT INTO `jshop_operation` VALUES ('339', '335', '删除配送方式', 'del', 'a', '336', '2', '100');
INSERT INTO `jshop_operation` VALUES ('340', '335', '选择地区', 'chooseArea', 'a', '336', '3', '100');
INSERT INTO `jshop_operation` VALUES ('341', '2', '管理中心', 'Index', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('342', '341', '首页', 'index', 'a', '241', '1', '10');
INSERT INTO `jshop_operation` VALUES ('343', '2', '售后单管理', 'BillAftersales', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('344', '343', '售后单列表', 'index', 'a', '296', '1', '140');
INSERT INTO `jshop_operation` VALUES ('345', '343', '售后单审核', 'audit', 'a', '344', '2', '100');
INSERT INTO `jshop_operation` VALUES ('346', '343', '售后单查看', 'view', 'a', '344', '3', '100');
INSERT INTO `jshop_operation` VALUES ('347', '2', '退货单管理', 'BillReship', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('348', '347', '退货单列表', 'index', 'a', '296', '1', '160');
INSERT INTO `jshop_operation` VALUES ('349', '347', '退货单查看', 'view', 'a', '348', '3', '100');
INSERT INTO `jshop_operation` VALUES ('350', '347', '退货单确认收货', 'confirmReship', 'a', '348', '2', '100');
INSERT INTO `jshop_operation` VALUES ('351', '2', '退款单管理', 'BillRefund', 'c', '0', '1', '150');
INSERT INTO `jshop_operation` VALUES ('352', '351', '退款单列表', 'index', 'a', '399', '1', '110');
INSERT INTO `jshop_operation` VALUES ('353', '351', '退款单查看', 'view', 'a', '352', '3', '100');
INSERT INTO `jshop_operation` VALUES ('354', '351', '操作退款', 'refund', 'a', '252', '2', '100');
INSERT INTO `jshop_operation` VALUES ('355', '351', '再次退款', 'reaudit', 'a', '354', '3', '100');
INSERT INTO `jshop_operation` VALUES ('356', '2', '促销管理', 'Promotion', 'c', '2', '1', '150');
INSERT INTO `jshop_operation` VALUES ('357', '356', '促销列表', 'index', 'a', '356', '1', '100');
INSERT INTO `jshop_operation` VALUES ('358', '356', '添加促销', 'add', 'a', '357', '2', '100');
INSERT INTO `jshop_operation` VALUES ('359', '356', '促销编辑', 'edit', 'a', '357', '2', '100');
INSERT INTO `jshop_operation` VALUES ('360', '356', '促销删除', 'del', 'a', '357', '2', '100');
INSERT INTO `jshop_operation` VALUES ('361', '356', '优惠券列表', 'coupon', 'a', '356', '1', '100');
INSERT INTO `jshop_operation` VALUES ('362', '356', '添加优惠券规则', 'couponAdd', 'a', '361', '2', '100');
INSERT INTO `jshop_operation` VALUES ('363', '356', '优惠券规则编辑', 'couponEdit', 'a', '361', '2', '100');
INSERT INTO `jshop_operation` VALUES ('364', '356', '优惠券规则删除', 'couponDel', 'a', '361', '2', '100');
INSERT INTO `jshop_operation` VALUES ('365', '2', '门店管理', 'Store', 'c', '2', '1', '220');
INSERT INTO `jshop_operation` VALUES ('366', '365', '门店列表', 'index', 'a', '365', '1', '100');
INSERT INTO `jshop_operation` VALUES ('367', '365', '门店添加', 'add', 'a', '366', '2', '100');
INSERT INTO `jshop_operation` VALUES ('368', '365', '门店修改', 'edit', 'a', '366', '2', '100');
INSERT INTO `jshop_operation` VALUES ('369', '365', '门店删除', 'del', 'a', '366', '2', '100');
INSERT INTO `jshop_operation` VALUES ('370', '365', '门店地图展现', 'showMap', 'a', '366', '3', '100');
INSERT INTO `jshop_operation` VALUES ('390', '2', '图片列表', 'images', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('391', '390', '图片列表', 'index', 'a', '244', '1', '100');
INSERT INTO `jshop_operation` VALUES ('392', '2', '任务列表', 'ietask', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('393', '392', '计划任务', 'index', 'a', '244', '1', '100');
INSERT INTO `jshop_operation` VALUES ('394', '392', '商品导出', 'export', 'a', '306', '3', '100');
INSERT INTO `jshop_operation` VALUES ('395', '392', '商品导入', 'import', 'a', '306', '3', '100');
INSERT INTO `jshop_operation` VALUES ('396', '392', '下载导入模板', 'importTemplete', 'a', '306', '3', '100');
INSERT INTO `jshop_operation` VALUES ('397', '392', '下载文件', 'down', 'a', '393', '3', '100');
INSERT INTO `jshop_operation` VALUES ('398', '392', '执行下载文件', 'doDown', 'a', '393', '3', '100');
INSERT INTO `jshop_operation` VALUES ('399', '2', '财务管理', 'BillPayments', 'c', '2', '1', '160');
INSERT INTO `jshop_operation` VALUES ('400', '399', '支付单列表', 'index', 'a', '399', '1', '100');
INSERT INTO `jshop_operation` VALUES ('401', '399', '后台支付', 'pay', 'a', '400', '2', '100');
INSERT INTO `jshop_operation` VALUES ('402', '399', '后台支付实际操作', 'topay', 'a', '401', '3', '100');
INSERT INTO `jshop_operation` VALUES ('403', '399', '支付单查看', 'index', 'a', '400', '3', '100');
INSERT INTO `jshop_operation` VALUES ('404', '2', '发货单管理', 'BillDelivery', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('405', '404', '发货单列表', 'index', 'a', '296', '1', '100');
INSERT INTO `jshop_operation` VALUES ('406', '404', '发货单详情', 'view', 'a', '405', '3', '100');
INSERT INTO `jshop_operation` VALUES ('407', '404', '发货单明细', 'items', 'a', '405', '3', '100');
INSERT INTO `jshop_operation` VALUES ('408', '2', '消息中心', 'MessageCenter', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('409', '408', '消息配置', 'index', 'a', '244', '1', '100');
INSERT INTO `jshop_operation` VALUES ('410', '2', '角色管理', 'Role', 'c', '2', '2', '100');
INSERT INTO `jshop_operation` VALUES ('411', '410', '角色管理', 'index', 'a', '238', '1', '100');
INSERT INTO `jshop_operation` VALUES ('412', '410', '角色添加', 'add', 'a', '411', '2', '100');
INSERT INTO `jshop_operation` VALUES ('413', '410', '角色删除', 'del', 'a', '411', '2', '100');
INSERT INTO `jshop_operation` VALUES ('414', '410', '编辑权限', 'getOperation', 'a', '411', '2', '100');
INSERT INTO `jshop_operation` VALUES ('415', '410', '角色保存权限', 'savePerm', 'a', '411', '3', '100');
INSERT INTO `jshop_operation` VALUES ('416', '2', '子账号管理', 'Manage', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('417', '416', '子账号管理', 'index', 'a', '238', '1', '100');
INSERT INTO `jshop_operation` VALUES ('418', '416', '管理员添加', 'add', 'a', '417', '2', '100');
INSERT INTO `jshop_operation` VALUES ('419', '416', '管理员修改', 'edit', 'a', '417', '2', '100');
INSERT INTO `jshop_operation` VALUES ('420', '416', '管理员删除', 'del', 'a', '417', '2', '100');
INSERT INTO `jshop_operation` VALUES ('421', '2', '商品参数', 'GoodsParams', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('422', '421', '参数列表', 'index', 'a', '305', '1', '100');
INSERT INTO `jshop_operation` VALUES ('423', '421', '添加参数', 'add', 'a', '422', '2', '100');
INSERT INTO `jshop_operation` VALUES ('424', '421', '编辑参数', 'edit', 'a', '422', '2', '100');
INSERT INTO `jshop_operation` VALUES ('425', '421', '删除参数', 'del', 'a', '422', '2', '100');
INSERT INTO `jshop_operation` VALUES ('426', '2', '标签管理', 'Label', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('427', '426', '设置标签', 'setLabel', 'a', '306', '3', '100');
INSERT INTO `jshop_operation` VALUES ('428', '426', '设置标签', 'doSetLabel', 'a', '306', '3', '100');
INSERT INTO `jshop_operation` VALUES ('429', '426', '删除标签', 'delLabel', 'a', '306', '3', '100');
INSERT INTO `jshop_operation` VALUES ('430', '426', '删除标签', 'doDelLabel', 'a', '306', '3', '100');
INSERT INTO `jshop_operation` VALUES ('431', '2', '账户余额模块', 'Balance', 'c', '0', '1', '100');
INSERT INTO `jshop_operation` VALUES ('432', '431', '账户资金管理', 'index', 'a', '399', '1', '150');
INSERT INTO `jshop_operation` VALUES ('434', '2', '节点控制器', 'Operation', 'c', '2', '3', '100');
INSERT INTO `jshop_operation` VALUES ('435', '434', '节点管理', 'index', 'a', '244', '1', '255');
INSERT INTO `jshop_operation` VALUES ('436', '434', '节点删除', 'del', 'a', '435', '2', '100');
INSERT INTO `jshop_operation` VALUES ('437', '434', '节点编辑', 'add', 'a', '435', '2', '100');
INSERT INTO `jshop_operation` VALUES ('438', '2', '消息控制器', 'Message', 'c', '1', '3', '100');
INSERT INTO `jshop_operation` VALUES ('439', '438', '消息管理', 'index', 'a', '238', '1', '200');
INSERT INTO `jshop_operation` VALUES ('440', '438', '消息删除', 'del', 'a', '439', '3', '100');
INSERT INTO `jshop_operation` VALUES ('441', '2', '短信控制器', 'Sms', 'c', '1', '3', '100');
INSERT INTO `jshop_operation` VALUES ('442', '441', '短信管理', 'index', 'a', '238', '1', '255');
INSERT INTO `jshop_operation` VALUES ('443', '441', '短信删除', 'del', 'a', '442', '3', '100');
INSERT INTO `jshop_operation` VALUES ('460', '431', '提现列表', 'tocash', 'a', '399', '1', '100');
INSERT INTO `jshop_operation` VALUES ('461', '431', '提现审核', 'tocashexamine', 'a', '460', '2', '100');
INSERT INTO `jshop_operation` VALUES ('462', '2', '钩子列表', 'hooks', 'c', '1', '1', '100');
INSERT INTO `jshop_operation` VALUES ('463', '462', '钩子列表', 'index', 'a', '244', '1', '100');
INSERT INTO `jshop_operation` VALUES ('464', '2', '插件列表', 'addons', 'c', '1', '1', '100');
INSERT INTO `jshop_operation` VALUES ('465', '464', '插件列表', 'index', 'a', '244', '1', '100');
INSERT INTO `jshop_operation` VALUES ('466', '2', '地区管理', 'area', 'c', '1', '3', '100');
INSERT INTO `jshop_operation` VALUES ('467', '466', '地区管理', 'index', 'a', '244', '1', '100');
INSERT INTO `jshop_operation` VALUES ('468', '466', '添加地区', 'add', 'a', '467', '2', '100');
INSERT INTO `jshop_operation` VALUES ('469', '466', '编辑地区', 'edit', 'a', '467', '2', '100');
INSERT INTO `jshop_operation` VALUES ('470', '466', '删除地区', 'del', 'a', '467', '2', '100');
INSERT INTO `jshop_operation` VALUES ('471', '462', '添加钩子', 'add', 'a', '463', '2', '100');
INSERT INTO `jshop_operation` VALUES ('472', '462', '编辑钩子', 'edit', 'a', '463', '2', '100');
INSERT INTO `jshop_operation` VALUES ('473', '462', '删除钩子', 'del', 'a', '463', '2', '100');
INSERT INTO `jshop_operation` VALUES ('474', '464', '安装插件', 'install', 'a', '465', '2', '100');
INSERT INTO `jshop_operation` VALUES ('475', '464', '卸载插件', 'uninstall', 'a', '465', '2', '100');
INSERT INTO `jshop_operation` VALUES ('476', '464', '插件配置', 'setting', 'a', '465', '2', '100');
INSERT INTO `jshop_operation` VALUES ('477', '464', '保存配置', 'doSetting', 'a', '465', '3', '100');
INSERT INTO `jshop_operation` VALUES ('478', '464', '插件启用/停用', 'changeStatus', 'a', '465', '2', '100');
INSERT INTO `jshop_operation` VALUES ('479', '2', '微信管理', 'Wechat', 'c', '2', '1', '100');
INSERT INTO `jshop_operation` VALUES ('480', '479', '小程序配置', 'edit', 'a', '479', '1', '100');
INSERT INTO `jshop_operation` VALUES ('481', '479', '模板列表', 'template', 'a', '479', '1', '100');
INSERT INTO `jshop_operation` VALUES ('482', '479', '保存配置', 'doEdit', 'a', '479', '2', '100');
INSERT INTO `jshop_operation` VALUES ('483', '479', '公众号配置', 'official', 'a', '479', '1', '100');
INSERT INTO `jshop_operation` VALUES ('484', '365', '店员列表', 'clerklist', 'a', '366', '2', '100');
INSERT INTO `jshop_operation` VALUES ('485', '365', '添加店员', 'addClerk', 'a', '366', '2', '100');
INSERT INTO `jshop_operation` VALUES ('486', '365', '删除店员', 'delClerk', 'a', '366', '2', '100');
INSERT INTO `jshop_operation` VALUES ('487', '2', '提货单列表', 'BillLading', 'c', '2', '3', '100');
INSERT INTO `jshop_operation` VALUES ('488', '487', '提货单列表', 'index', 'a', '296', '1', '100');
INSERT INTO `jshop_operation` VALUES ('489', '487', '提货单详情', 'info', 'a', '488', '2', '100');
INSERT INTO `jshop_operation` VALUES ('490', '487', '删除提货单', 'delLading', 'a', '488', '2', '100');
INSERT INTO `jshop_operation` VALUES ('491', '356', '团购秒杀列表', 'group', 'a', '356', '1', '100');
INSERT INTO `jshop_operation` VALUES ('492', '356', '添加团购', 'groupAdd', 'a', '491', '2', '100');
INSERT INTO `jshop_operation` VALUES ('493', '356', '编辑团购', 'groupEdit', 'a', '491', '2', '100');
INSERT INTO `jshop_operation` VALUES ('494', '356', '删除团购', 'groupdel', 'a', '491', '2', '100');
INSERT INTO `jshop_operation` VALUES ('495', '479', '公众号菜单', 'officialMenu', 'a', '479', '1', '100');
INSERT INTO `jshop_operation` VALUES ('496', '479', '编辑菜单', 'editMenu', 'a', '495', '3', '100');
INSERT INTO `jshop_operation` VALUES ('497', '479', '保存菜单', 'doEditMenu', 'a', '495', '3', '100');
INSERT INTO `jshop_operation` VALUES ('498', '479', '删除菜单', 'deleteMenu', 'a', '495', '2', '100');
INSERT INTO `jshop_operation` VALUES ('499', '479', '同步菜单', 'updateMenu', 'a', '495', '2', '100');
INSERT INTO `jshop_operation` VALUES ('500', '2', '物流公司', 'Logistics', 'c', '244', '3', '100');
INSERT INTO `jshop_operation` VALUES ('501', '500', '物流公司', 'index', 'a', '244', '1', '100');
INSERT INTO `jshop_operation` VALUES ('502', '500', '添加物流公司', 'add', 'a', '501', '2', '100');
INSERT INTO `jshop_operation` VALUES ('503', '500', '编辑物流公司', 'edit', 'a', '501', '2', '100');
INSERT INTO `jshop_operation` VALUES ('504', '500', '删除物流公司', 'del', 'a', '501', '2', '100');
