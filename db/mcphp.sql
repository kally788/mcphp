-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- 主机: 127.0.0.1
-- 生成日期: 2014 年 06 月 12 日 11:48
-- 服务器版本: 5.1.53
-- PHP 版本: 5.3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `mcphp`
--

-- --------------------------------------------------------

--
-- 表的结构 `mc_test`
--

CREATE TABLE IF NOT EXISTS `mc_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `b` int(11) DEFAULT '1',
  `c` int(11) NOT NULL,
  `d` varchar(255) DEFAULT 'Hello MC!~',
  `e` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `mc_test`
--

