[![Php Version](https://img.shields.io/badge/php-%3E=7.1-brightgreen.svg)](https://secure.php.net/)
[![Swoole Version](https://img.shields.io/badge/swoole-%3E=4.3.0-brightgreen.svg)](https://github.com/swoole/swoole-src)
[![Rangine Framework Version](https://img.shields.io/badge/rangine-%3E=0.0.1-brightgreen.svg)](https://gitee.com/we7coreteam/w7swoole)
[![Illuminate Database Version](https://img.shields.io/badge/illuminate/database-%3E=5.6.0-brightgreen.svg)](https://github.com/illuminate/database)
[![Rangine Doc](https://img.shields.io/badge/docs-passing-green.svg?maxAge=2592000)](https://s.we7.cc/index.php?c=wiki&do=view&id=317)
# document-api

一款基于软擎框架文档管理系统。

##### 软擎框架
Github : https://github.com/we7coreteam/w7swoole_empty.git

Gitee : https://gitee.com/we7coreteam/w7swoole_empty.git

# 文档系统代码

Gitee : https://gitee.com/we7coreteam/document-apiserver.git

# 安装

1、composer install 前更改 composer 源，防止报错。

```
composer config -g repo.packagist composer https://packagist.laravel-china.org

git clone https://gitee.com/we7coreteam/w7swoole_empty ./document

cd document

sudo composer install
```

2、导入数据库

<details>
<summary>数据库结构</summary>

**<summary>--
-- 数据库： `document`
--

-- --------------------------------------------------------

--
-- 表的结构 `ims_cache`
--

CREATE TABLE `ims_cache` (
  `id` int(11) NOT NULL,
  `key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `value` text COLLATE utf8mb4_general_ci,
  `expired_at` int(11) DEFAULT '0'
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
  ADD UNIQUE KEY `unique` (`key`) USING BTREE;

--
-- 表的索引 `ims_document`
--
ALTER TABLE `ims_document`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator_id` (`creator_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniqid_idx` (`user_id`,`document_id`);

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
-- 使用表AUTO_INCREMENT `ims_permission_document`
--
ALTER TABLE `ims_permission_document`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `ims_user`
--
ALTER TABLE `ims_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
**
</details>


3、导入后台admin账号 (账号:amdin 密码:123456)

```
INSERT INTO ims_user ( username, userpass, remark, has_privilege) VALUES ('admin','d7c6c07a0a04ba4e65921e2f90726384','超管',1);
```

4、修改配置文件

```
配置根目录下的config/app.php文件，数据库(database)和缓存(cache)。

'cache' => [
    'default' => [
        'driver' => ienv('CACHE_DEFAULT_DRIVER', 'redis'),
        'host' => ienv('CACHE_DEFAULT_HOST', 'redis'),
        'port' => ienv('CACHE_DEFAULT_PORT', '6379'),
        'timeout' => ienv('CACHE_DEFAULT_TIMEOUT', 30),
        'password' => ienv('CACHE_DEFAULT_PASSWORD', ''),
        'database' => ienv('CACHE_DEFAULT_DATABASE', '0'),
    ],
],
'database' => [
    'default' => [
        'driver' => ienv('DATABASE_DEFAULT_DRIVER', 'mysql'),
        'database' => ienv('DATABASE_DEFAULT_DATABASE', 'document'),
        'host' => ienv('DATABASE_DEFAULT_HOST', '127.0.0.1'),
        'username' => ienv('DATABASE_DEFAULT_USERNAME', 'root'),
        'password' => ienv('DATABASE_DEFAULT_PASSWORD', ''),
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => 'ims_',
        'port' =>'3306',
    ],
]
```
5、启动

```
cd ./文档根目录

执行: php bin/server.php http start
```

# 文档

https://s.w7.cc/index.php?c=wiki&do=view&id=527&list=3081








