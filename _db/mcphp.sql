-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- 主机: 127.0.0.1
-- 生成日期: 2014 年 06 月 13 日 19:39
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
-- 表的结构 `mc_split0`
--

CREATE TABLE IF NOT EXISTS `mc_split0` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `v` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `mc_split0`
--

INSERT INTO `mc_split0` (`id`, `v`) VALUES
(1, '11'),
(2, '22');

-- --------------------------------------------------------

--
-- 表的结构 `mc_split1`
--

CREATE TABLE IF NOT EXISTS `mc_split1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `v` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `mc_split1`
--

INSERT INTO `mc_split1` (`id`, `v`) VALUES
(1, '33'),
(2, '44');

-- --------------------------------------------------------

--
-- 表的结构 `mc_split2`
--

CREATE TABLE IF NOT EXISTS `mc_split2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `v` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `mc_split2`
--

INSERT INTO `mc_split2` (`id`, `v`) VALUES
(1, '55'),
(2, '66');

-- --------------------------------------------------------

--
-- 表的结构 `mc_split3`
--

CREATE TABLE IF NOT EXISTS `mc_split3` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `v` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `mc_split3`
--

INSERT INTO `mc_split3` (`id`, `v`) VALUES
(1, '77'),
(2, '88');

-- --------------------------------------------------------

--
-- 表的结构 `mc_split4`
--

CREATE TABLE IF NOT EXISTS `mc_split4` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `v` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `mc_split4`
--

INSERT INTO `mc_split4` (`id`, `v`) VALUES
(1, '99'),
(2, '100');

-- --------------------------------------------------------

--
-- 表的结构 `mc_split5`
--

CREATE TABLE IF NOT EXISTS `mc_split5` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `v` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `mc_split5`
--

INSERT INTO `mc_split5` (`id`, `v`) VALUES
(1, '101'),
(2, '102');

-- --------------------------------------------------------

--
-- 表的结构 `mc_split6`
--

CREATE TABLE IF NOT EXISTS `mc_split6` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `v` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `mc_split6`
--

INSERT INTO `mc_split6` (`id`, `v`) VALUES
(1, '103'),
(2, '104');

-- --------------------------------------------------------

--
-- 表的结构 `mc_split7`
--

CREATE TABLE IF NOT EXISTS `mc_split7` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `v` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `mc_split7`
--

INSERT INTO `mc_split7` (`id`, `v`) VALUES
(1, '105'),
(2, '106');

-- --------------------------------------------------------

--
-- 表的结构 `mc_split8`
--

CREATE TABLE IF NOT EXISTS `mc_split8` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `v` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `mc_split8`
--

INSERT INTO `mc_split8` (`id`, `v`) VALUES
(1, '107'),
(2, '108');

-- --------------------------------------------------------

--
-- 表的结构 `mc_split9`
--

CREATE TABLE IF NOT EXISTS `mc_split9` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `v` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `mc_split9`
--

INSERT INTO `mc_split9` (`id`, `v`) VALUES
(1, '109'),
(2, '110');

-- --------------------------------------------------------

--
-- 表的结构 `mc_test`
--

CREATE TABLE IF NOT EXISTS `mc_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `a` int(11) DEFAULT '1',
  `b` int(11) NOT NULL,
  `c` varchar(255) DEFAULT 'Hello MC!~',
  `d` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- 转存表中的数据 `mc_test`
--

INSERT INTO `mc_test` (`id`, `a`, `b`, `c`, `d`) VALUES
(1, 888, 999, 'abc', '我是字符串'),
(2, 888, 999, 'efg', '时间戳:1402630166'),
(3, 1, 1, 'aa', 'aa'),
(4, 2, 2, 'bb', 'bb'),
(6, 33, 33, 'cc', 'cc'),
(7, 33, 33, 'cc', 'cc');
