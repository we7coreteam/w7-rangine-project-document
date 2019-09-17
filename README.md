### 简介

一款基于软擎框架（https://www.rangine.com/）的开源Markdown文档系统。

常驻内存，不依赖传统的 Nginx/Apache 和 PHP-FPM，全异步非阻塞、协程实现。

### 环境要求

  * PHP > 7.0.0
  * PHP Swoole 扩展 >= 4.3.0
  * PHP Mbstring 扩展
  * PHP Pdo MySql 扩展

### 安装

#### 下载安装包

https://gitee.com/we7coreteam/document-apiserver/releases 下载最新版的文档系统源码

#### 初始化系统

解压源码后，进入目录执行以下，按照提示完成初始化操作

```
$ php bin/gerent.php install:init
```

#### 运行系统

> 如果您的80端口被其它应用占用，您需要手动修改.env文件中的 SERVER_HTTP_PORT 选项

```
$ php bin/server.php http start
```









