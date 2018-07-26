-- phpMyAdmin SQL Dump
-- version 4.3.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: 2018-07-26 19:01:57
-- 服务器版本： 5.5.42
-- PHP Version: 5.6.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `jshop_b2c`
--

-- --------------------------------------------------------

--
-- 表的结构 `payments`
--

CREATE TABLE `payments` (
  `id` smallint(5) unsigned NOT NULL,
  `code` varchar(50) DEFAULT NULL COMMENT '支付类型编码',
  `name` varchar(50) DEFAULT NULL COMMENT '支付类型名称',
  `is_online` tinyint(1) unsigned DEFAULT '1' COMMENT '是否线上支付 1=线上支付 2=线下支付',
  `params` text NOT NULL COMMENT '参数',
  `sort` smallint(5) unsigned NOT NULL DEFAULT '100' COMMENT '排序',
  `memo` varchar(200) NOT NULL COMMENT '支付方式描述',
  `status` smallint(1) unsigned NOT NULL DEFAULT '2' COMMENT '启用状态 1=启用 2=停用'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='支付方式表';

--
-- 转存表中的数据 `payments`
--

INSERT INTO `payments` (`id`, `code`, `name`, `is_online`, `params`, `sort`, `memo`, `status`) VALUES
(1, 'wechatpay', '微信支付', 1, '{"appid":"a","mch_id":"a","key":"a","sslcert":"","sslkey":""}', 222, '点击去微信支付', 1),
(2, 'alipay', '支付宝支付', 1, '', 100, '点击去支付宝支付', 1),
(3, 'offline', '线下支付', 2, '', 100, '联系客服进行线下付款', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`), ADD KEY `status` (`status`), ADD KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;

  DROP TABLE payments_seller_rel;