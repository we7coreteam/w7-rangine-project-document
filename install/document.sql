-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- 主机： db
-- 生成日期： 2019-09-25 07:19:25
-- 服务器版本： 8.0.16
-- PHP 版本： 7.2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `document`
--

-- --------------------------------------------------------

--
-- 表的结构 `ims_cache`
--

CREATE TABLE `ims_cache` (
  `id` int(11) NOT NULL,
  `key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `expired_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `ims_document`
--

CREATE TABLE `ims_document` (
  `id` int(11) NOT NULL,
  `name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '描述',
  `creator_id` int(11) NOT NULL DEFAULT '0' COMMENT '创建者id',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  `is_show` tinyint(1) NOT NULL DEFAULT '2' COMMENT '是否显示 1:显示,2:不显示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `ims_document_chapter`
--

CREATE TABLE `ims_document_chapter` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文档名称',
  `document_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '壳id',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序，越大越靠前',
  `levels` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `ims_document_chapter_content`
--

CREATE TABLE `ims_document_chapter_content` (
  `id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `layout` tinyint(1) NOT NULL COMMENT '章节格式 1 markdown 2 富文本'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `ims_permission_document`
--

CREATE TABLE `ims_permission_document` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `document_id` int(10) NOT NULL DEFAULT '0' COMMENT '文档壳id',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `ims_setting`
--

CREATE TABLE `ims_setting` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `value` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `ims_user`
--

CREATE TABLE `ims_user` (
  `id` int(11) NOT NULL,
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `is_ban` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1:禁止,0:正常',
  `userpass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '登录密码',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `has_privilege` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否具有特权 0:无,1:有',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转储表的索引
--

--
-- 表的索引 `ims_cache`
--
ALTER TABLE `ims_cache`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- 表的索引 `ims_document`
--
ALTER TABLE `ims_document`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator_id` (`creator_id`),
  ADD KEY `updated_at` (`updated_at`);

--
-- 表的索引 `ims_document_chapter`
--
ALTER TABLE `ims_document_chapter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`,`document_id`);

--
-- 表的索引 `ims_document_chapter_content`
--
ALTER TABLE `ims_document_chapter_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapter_id` (`chapter_id`);

--
-- 表的索引 `ims_permission_document`
--
ALTER TABLE `ims_permission_document`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `ims_setting`
--
ALTER TABLE `ims_setting`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `ims_user`
--
ALTER TABLE `ims_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_username` (`username`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `ims_cache`
--
ALTER TABLE `ims_cache`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `ims_document`
--
ALTER TABLE `ims_document`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `ims_document_chapter`
--
ALTER TABLE `ims_document_chapter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `ims_document_chapter_content`
--
ALTER TABLE `ims_document_chapter_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `ims_permission_document`
--
ALTER TABLE `ims_permission_document`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `ims_setting`
--
ALTER TABLE `ims_setting`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `ims_user`
--
ALTER TABLE `ims_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
