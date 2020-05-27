SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;



--
-- 表的结构 `jshop_welfarepro_hb`
--

CREATE TABLE `jshop_welfarepro_hb` (
  `id` int(11) unsigned NOT NULL,
  `money_all` int(11) unsigned NOT NULL COMMENT '总金额',
  `money_start` int(8) unsigned NOT NULL COMMENT '开始的钱',
  `money_end` int(8) unsigned NOT NULL COMMENT '结束的钱',
  `date_start` bigint(12) unsigned NOT NULL COMMENT '开始时间',
  `date_end` bigint(12) unsigned NOT NULL COMMENT '结束时间',
  `type` tinyint(2) unsigned NOT NULL COMMENT '1，全部用户，2新用户',
  `ctime` bigint(12) unsigned DEFAULT NULL,
  `utime` bigint(12) unsigned DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='红包表';

-- --------------------------------------------------------

--
-- 表的结构 `jshop_welfarepro_hblog`
--

CREATE TABLE `jshop_welfarepro_hblog` (
  `id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL COMMENT '扫码人',
  `tj_user_id` int(11) unsigned NOT NULL COMMENT '推荐人',
  `money` int(10) unsigned NOT NULL COMMENT '红包金额',
  `hb_id` int(11) unsigned NOT NULL COMMENT '批次，红包活动id',
  `ctime` bigint(12) unsigned DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='红包表';

-- --------------------------------------------------------

--
-- 表的结构 `jshop_welfarepro_hbuser`
--

CREATE TABLE `jshop_welfarepro_hbuser` (
  `hb_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='红包表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jshop_welfarepro_hb`
--
ALTER TABLE `jshop_welfarepro_hb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jshop_welfarepro_hblog`
--
ALTER TABLE `jshop_welfarepro_hblog`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jshop_welfarepro_hb`
--
ALTER TABLE `jshop_welfarepro_hb`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `jshop_welfarepro_hblog`
--
ALTER TABLE `jshop_welfarepro_hblog`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;




-- ----------------------------
-- Table structure for jshop_welfarepro_coupon
-- ----------------------------
DROP TABLE IF EXISTS `jshop_welfarepro_coupon`;
CREATE TABLE `jshop_welfarepro_coupon`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '名称',
  `date_start` bigint(12) UNSIGNED NOT NULL COMMENT '生效时间',
  `date_end` bigint(12) UNSIGNED NOT NULL COMMENT '失效时间',
  `type` tinyint(2) UNSIGNED NOT NULL COMMENT '1，全部用户，2新用户',
  `sendnum` int(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '数量',
  `ctime` bigint(12) UNSIGNED NULL DEFAULT NULL,
  `utime` bigint(12) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '扫码领优惠券表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for jshop_welfarepro_coupon_rel
-- ----------------------------
DROP TABLE IF EXISTS `jshop_welfarepro_coupon_rel`;
CREATE TABLE `jshop_welfarepro_coupon_rel`  (
  `c_id` int(11) UNSIGNED NOT NULL COMMENT '扫码领优惠券ID',
  `coupon_id` int(11) UNSIGNED NOT NULL COMMENT '优惠券ID',
  `num` int(8) UNSIGNED NOT NULL DEFAULT 0 COMMENT '数量'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '扫码领优惠券与优惠券关联表' ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;

Alter TABLE jshop_welfarepro_hbuser add `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1 红包 2优惠券';

DROP TABLE IF EXISTS `jshop_welfarepro_coupon_log`;
CREATE TABLE `jshop_welfarepro_coupon_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `c_id` int(11) unsigned NOT NULL,
  `tj_user_id` int(11) NOT NULL COMMENT '推荐人',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '扫码人',
  `coupon_id` int(11) unsigned NOT NULL COMMENT '优惠券ID',
  `ctime` bigint(12) unsigned DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='优惠券领取记录表';
