-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- 主机： 10.0.0.17:3306
-- 生成日期： 2019-12-27 08:01:49
-- 服务器版本： 5.6.28-cdb2016-log
-- PHP 版本： 7.2.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- 数据库： `we7_document_test`
--

-- --------------------------------------------------------

--
-- 表的结构 `ims_cache`
--

DROP TABLE IF EXISTS `ims_cache`;
CREATE TABLE `ims_cache` (
  `id` int(11) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `expired_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `ims_document`
--

DROP TABLE IF EXISTS `ims_document`;
CREATE TABLE `ims_document` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT '' COMMENT '描述',
  `creator_id` int(11) NOT NULL DEFAULT '0' COMMENT '创建者id',
  `is_public` tinyint(1) NOT NULL DEFAULT '2' COMMENT '文档是否为公有文档 1:公有,2:私有',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `ims_document_chapter`
--

DROP TABLE IF EXISTS `ims_document_chapter`;
CREATE TABLE `ims_document_chapter` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '文档名称',
  `document_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '壳id',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序，越大越靠前',
  `is_dir` tinyint(1) DEFAULT '0' COMMENT '当前章节是否是目录(1：是，0：否)',
  `levels` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `default_show_chapter_id` int(11) DEFAULT '0' COMMENT '默认显示的章节',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `ims_document_chapter_content`
--

DROP TABLE IF EXISTS `ims_document_chapter_content`;
CREATE TABLE `ims_document_chapter_content` (
  `id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `content` longtext,
  `layout` tinyint(1) NOT NULL COMMENT '章节格式 1 markdown 2 富文本'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `ims_document_permission`
--

DROP TABLE IF EXISTS `ims_document_permission`;
CREATE TABLE `ims_document_permission` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL COMMENT '文档id',
  `permission` tinyint(4) NOT NULL COMMENT '用户在文档中的权限(1: 管理员,2:操作员,3:阅读者)',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `ims_session`
--

DROP TABLE IF EXISTS `ims_session`;
CREATE TABLE `ims_session` (
  `id` int(11) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `data` varchar(1000) NOT NULL,
  `expired_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `ims_setting`
--

DROP TABLE IF EXISTS `ims_setting`;
CREATE TABLE `ims_setting` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(60) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `ims_user`
--

DROP TABLE IF EXISTS `ims_user`;
CREATE TABLE `ims_user` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `userpass` varchar(255) NOT NULL DEFAULT '' COMMENT '登录密码',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `is_ban` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1:禁止,0:正常',
  `has_privilege` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否具有特权 0:无,1:有',
  `group_id` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `ims_user_operate_log`
--

DROP TABLE IF EXISTS `ims_user_operate_log`;
CREATE TABLE `ims_user_operate_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT '操作人id',
  `document_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL COMMENT '章节id',
  `operate` tinyint(4) NOT NULL COMMENT '操作（1：新增，2：编辑，3：删除）',
  `remark` varchar(120) DEFAULT '',
  `created_at` int(11) NOT NULL COMMENT '操作时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `ims_user_star`
--

DROP TABLE IF EXISTS `ims_user_star`;
CREATE TABLE `ims_user_star` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- 表的索引 `ims_document_permission`
--
ALTER TABLE `ims_document_permission`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `ims_session`
--
ALTER TABLE `ims_session`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`);

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
-- 表的索引 `ims_user_operate_log`
--
ALTER TABLE `ims_user_operate_log`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `ims_user_star`
--
ALTER TABLE `ims_user_star`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`document_id`);

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
-- 使用表AUTO_INCREMENT `ims_document_permission`
--
ALTER TABLE `ims_document_permission`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `ims_session`
--
ALTER TABLE `ims_session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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

--
-- 使用表AUTO_INCREMENT `ims_user_operate_log`
--
ALTER TABLE `ims_user_operate_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `ims_user_star`
--
ALTER TABLE `ims_user_star`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
