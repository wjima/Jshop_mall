ALTER TABLE `jshop_order_items` CHANGE `addon` `addon` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '货品明细序列号存储';
ALTER TABLE `jshop_order_items` CHANGE `costprice` `costprice` DECIMAL(10,2) UNSIGNED NULL DEFAULT '0' COMMENT '货品成本价单价';
ALTER TABLE `jshop_user` MODIFY COLUMN `pid`  int(10) UNSIGNED NULL DEFAULT 0 COMMENT '推荐人' AFTER `status`;



-- ----------------------------
-- Table structure for jshop_form
-- ----------------------------
DROP TABLE IF EXISTS `jshop_form`;
CREATE TABLE `jshop_form` (
  `id` bigint(12) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL COMMENT '表单名称',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '1订单、2留言、3反馈、4登记、5调研',
  `sort` int(5) unsigned DEFAULT '100' COMMENT '表单排序',
  `desc` varchar(255) DEFAULT NULL COMMENT '表单描述',
  `head_type` tinyint(1) unsigned DEFAULT '1' COMMENT '1图片2轮播3视频',
  `head_type_value` varchar(200) DEFAULT NULL COMMENT '表单头值',
  `head_type_video` varchar(32) DEFAULT NULL,
  `button_name` varchar(50) DEFAULT NULL COMMENT '表单提交按钮名称',
  `button_color` varchar(30) DEFAULT NULL COMMENT '表单按钮颜色',
  `is_login` tinyint(1) unsigned DEFAULT '2' COMMENT '是否需要登录1需要2不需要',
  `qrcode` varchar(200) DEFAULT NULL COMMENT '二维码图片地址',
  `return_msg` varchar(200) DEFAULT '' COMMENT '提交后提示语',
  `end_date` bigint(12) unsigned DEFAULT NULL COMMENT '到期时间',
  `ctime` bigint(12) unsigned DEFAULT '0' COMMENT '创建时间',
  `utime` bigint(12) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for jshop_form_item
-- ----------------------------
DROP TABLE IF EXISTS `jshop_form_item`;
CREATE TABLE `jshop_form_item` (
  `id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '字段名称',
  `type` varchar(30) DEFAULT NULL COMMENT '字段类型',
  `validation_type` varchar(30) DEFAULT NULL COMMENT '验证类型',
  `value` varchar(255) DEFAULT NULL COMMENT '表单值',
  `default_value` varchar(255) DEFAULT NULL COMMENT '默认值',
  `form_id` bigint(12) unsigned DEFAULT '0' COMMENT '表单id',
  `required` tinyint(1) unsigned DEFAULT '2' COMMENT '是否必填，1必填，2不必填',
  `sort` int(5) unsigned DEFAULT '100' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COMMENT='表单项表';

-- ----------------------------
-- Table structure for jshop_form_submit
-- ----------------------------
DROP TABLE IF EXISTS `jshop_form_submit`;
CREATE TABLE `jshop_form_submit` (
  `id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint(12) NOT NULL DEFAULT '0' COMMENT '表单id',
  `form_name` varchar(255) DEFAULT '' COMMENT '表单名称',
  `user_id` bigint(20) unsigned DEFAULT '0' COMMENT '会员id',
  `money` decimal(20,2) unsigned DEFAULT '0.00' COMMENT '总金额',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '2未支付，1已支付。支付状态',
  `status` tinyint(1) DEFAULT '2' COMMENT '表单状态，1已处理，2未处理',
  `feedback` varchar(255) DEFAULT NULL COMMENT '表单反馈',
  `ip` varchar(20) DEFAULT '' COMMENT '提交人ip',
  `ctime` bigint(12) unsigned DEFAULT '0' COMMENT '创建时间',
  `utime` bigint(12) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4 COMMENT='用户对表的提交记录';

-- ----------------------------
-- Table structure for jshop_form_submit_detail
-- ----------------------------
DROP TABLE IF EXISTS `jshop_form_submit_detail`;
CREATE TABLE `jshop_form_submit_detail` (
  `id` bigint(12) unsigned NOT NULL AUTO_INCREMENT,
  `submit_id` bigint(12) DEFAULT '0' COMMENT '提交表单id',
  `form_id` bigint(12) unsigned DEFAULT '0' COMMENT '表单id',
  `form_item_id` bigint(12) DEFAULT NULL COMMENT '表单项id',
  `form_item_name` varchar(200) DEFAULT '' COMMENT '表单项名称',
  `form_item_value` text COMMENT '表单项值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8mb4 COMMENT='提交表单保存大文本值表';



-- ----------------------------
-- Table structure for jshop_files
-- ----------------------------
DROP TABLE IF EXISTS `jshop_files`;
CREATE TABLE `jshop_files` (
  `id` char(32) NOT NULL COMMENT '视频ID',
  `name` varchar(50) DEFAULT NULL COMMENT '视频名称',
  `url` varchar(255) DEFAULT NULL COMMENT '绝对地址',
  `path` varchar(255) DEFAULT NULL COMMENT '物理地址',
  `type` enum('web','local') DEFAULT 'local' COMMENT '存储引擎',
  `file_type` enum('video') DEFAULT 'video' COMMENT '文件类型',
  `ctime` bigint(12) unsigned DEFAULT NULL COMMENT '创建时间',
  `isdel` bigint(12) unsigned DEFAULT NULL COMMENT '删除标志 有数据代表删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `id_2` (`id`),
  KEY `isdel` (`isdel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件表';



INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ('539', '390', '删除图片', 'del', 'a', '391', '2', '100');
INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ('540', '392', '删除任务', 'del', 'a', '393', '1', '100');
INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ('541', '2', '智能表单', 'Form', 'c', '2', '1', '300');
INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ('542', '541', '表单列表', 'index', 'a', '541', '1', '90');
INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ('543', '541', '添加表单', 'add', 'a', '542', '2', '100');
INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ('544', '541', '编辑表单', 'edit', 'a', '542', '2', '100');
INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ('545', '541', '删除表单', 'del', 'a', '542', '2', '100');
INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ('546', '541', '小程序码', 'generate', 'a', '542', '2', '100');
INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ('547', '541', '提交列表', 'formsubmit', 'a', '541', '1', '100');
INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ('548', '541', '表单报表', 'report', 'a', '542', '2', '100');
INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ('549', '541', '表单统计', 'statistics', 'a', '542', '2', '100');
INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ('550', '541', '删除提交', 'delsubmit', 'a', '547', '2', '100');
INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ('551', '541', '提交明细', 'formsubmitdetail', 'a', '547', '2', '100');
INSERT INTO `jshop_operation` (`id`, `parent_id`, `name`, `code`, `type`, `parent_menu_id`, `perm_type`, `sort`) VALUES ('552', '541', '反馈', 'editformsubmit', 'a', '547', '2', '100');
