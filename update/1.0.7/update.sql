
--  修复微信登录默认值

ALTER TABLE `jshop_user_wx` CHANGE `unionid` `unionid` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
ALTER TABLE `jshop_user_wx` CHANGE `type` `type` TINYINT(1) UNSIGNED NULL DEFAULT '1' COMMENT '第三方登录类型，1微信小程序，2微信公众号';
ALTER TABLE `jshop_user_wx` CHANGE `openid` `openid` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL;
ALTER TABLE `jshop_user_wx` CHANGE `session_key` `session_key` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
ALTER TABLE `jshop_user_wx` CHANGE `gender` `gender` SMALLINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '性别 0：未知、1：男、2：女';
ALTER TABLE `jshop_user_wx` CHANGE `language` `language` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '语言';
ALTER TABLE `jshop_user_wx` CHANGE `city` `city` VARCHAR(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '城市';
ALTER TABLE `jshop_user_wx` CHANGE `province` `province` VARCHAR(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '省';
ALTER TABLE `jshop_user_wx` CHANGE `country` `country` VARCHAR(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '国家';
ALTER TABLE `jshop_user_wx` CHANGE `country_code` `country_code` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '手机号码国家编码';
ALTER TABLE `jshop_user_wx` CHANGE `mobile` `mobile` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '手机号码';


INSERT INTO `jshop_payments` (`id`, `code`, `name`, `is_online`, `params`, `sort`, `memo`, `status`) VALUES
(4, 'balancepay', '余额支付', 1, '', 100, '账户余额支付', 1);


UPDATE  jshop_area SET  `id` =  '920001' WHERE  name ="富阳市";
UPDATE  jshop_area SET  `id` =  '920000' WHERE  name ="静海县";
UPDATE  jshop_area SET  `id` =  '920002' WHERE  name ="郑东新区";
UPDATE  jshop_area SET  `id` =  '920003' WHERE  name ="萝岗区";
ALTER TABLE `jshop_area` ADD PRIMARY KEY(`id`);


ALTER TABLE  `jshop_goods_images` ADD  `sort` INT( 10 ) NULL DEFAULT  '100' COMMENT  '图片排序' AFTER  `image_id` ;



--
-- 表的结构 `微信图文消息表`
--

CREATE TABLE IF NOT EXISTS `jshop_weixin_media_message` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL COMMENT '标题',
  `author` varchar(100) DEFAULT NULL COMMENT '作者',
  `brief` varchar(255) DEFAULT NULL COMMENT '摘要',
  `image` char(32) DEFAULT NULL COMMENT '封面',
  `content` text COMMENT '文章详情',
  `url` varchar(255) DEFAULT NULL COMMENT '原文地址',
  `ctime` bigint(12) DEFAULT '0' COMMENT '创建时间',
  `utime` bigint(12) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='微信图文消息表' AUTO_INCREMENT=8 ;



ALTER TABLE  `jshop_weixin_message` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


ALTER TABLE  `jshop_weixin_message` ADD  `enable` TINYINT( 1 ) NULL DEFAULT  '1' COMMENT  '1启用，2禁用' AFTER  `is_default` ;


--  地区添加索引
ALTER TABLE `jshop_area` ADD INDEX( `id`, `parent_id`, `name`);
