-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2023-02-25 22:32:03
-- 服务器版本： 5.6.50-log
-- PHP 版本： 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `jol`
--

-- --------------------------------------------------------

--
-- 表的结构 `problem_source`
--

CREATE TABLE `problem_source` (
  `id` int(11) NOT NULL COMMENT '主键',
  `source` varchar(100) NOT NULL,
  `summary` varchar(1000) DEFAULT NULL,
  `in_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='问题分类';

--
-- 转储表的索引
--

--
-- 表的索引 `problem_source`
--
ALTER TABLE `problem_source`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `problem_source`
--
ALTER TABLE `problem_source`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
