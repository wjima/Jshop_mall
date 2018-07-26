-- phpMyAdmin SQL Dump
-- version 4.3.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: 2018-07-26 16:22:43
-- 服务器版本： 5.5.42
-- PHP Version: 5.6.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `jshop_b2c`
--

-- --------------------------------------------------------

--
-- 表的结构 `setting`
--

CREATE TABLE `setting` (
  `skey` varchar(50) NOT NULL DEFAULT '' COMMENT '键',
  `value` text COMMENT '值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='店铺设置表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`skey`), ADD KEY `skey` (`skey`);

DROP TABLE seller_setting;