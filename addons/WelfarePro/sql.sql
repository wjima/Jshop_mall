-- phpMyAdmin SQL Dump
-- version 4.3.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: 2020-04-22 13:46:56
-- 服务器版本： 5.5.42
-- PHP Version: 5.6.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `hbsw`
--

-- --------------------------------------------------------

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

--
-- 转存表中的数据 `jshop_welfarepro_hb`
--

INSERT INTO `jshop_welfarepro_hb` (`id`, `money_all`, `money_start`, `money_end`, `date_start`, `date_end`, `type`, `ctime`, `utime`) VALUES
(4, 1000, 888, 8888, 1586361600, 1589385600, 1, 1587469406, 1587469406);

-- --------------------------------------------------------

--
-- 表的结构 `jshop_welfarepro_hblog`
--

CREATE TABLE `jshop_welfarepro_hblog` (
  `id` int(11) unsigned NOT NULL,
  `money_all` int(11) unsigned NOT NULL COMMENT '总金额',
  `money_start` int(8) unsigned NOT NULL COMMENT '开始的钱',
  `money_end` int(8) unsigned NOT NULL COMMENT '结束的钱',
  `date_start` bigint(12) unsigned NOT NULL COMMENT '开始时间',
  `date_end` bigint(12) unsigned NOT NULL COMMENT '结束时间',
  `type` tinyint(2) unsigned NOT NULL COMMENT '1，全部用户，2新用户',
  `ctime` bigint(12) unsigned DEFAULT NULL,
  `utime` bigint(12) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='红包表';

--
-- 转存表中的数据 `jshop_welfarepro_hblog`
--

INSERT INTO `jshop_welfarepro_hblog` (`id`, `money_all`, `money_start`, `money_end`, `date_start`, `date_end`, `type`, `ctime`, `utime`) VALUES
(0, 0, 0, 0, 2020, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `jshop_welfarepro_hbuser`
--

CREATE TABLE `jshop_welfarepro_hbuser` (
  `hb_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='红包表';

--
-- 转存表中的数据 `jshop_welfarepro_hbuser`
--

INSERT INTO `jshop_welfarepro_hbuser` (`hb_id`, `user_id`) VALUES
(4, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `jshop_welfarepro_hb`
--
ALTER TABLE `jshop_welfarepro_hb`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `jshop_welfarepro_hb`
--
ALTER TABLE `jshop_welfarepro_hb`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;