### 简介

一款基于软擎框架（[https://www.rangine.com/](https://www.rangine.com/)）的开源Markdown文档系统。

常驻内存，不依赖传统的 Nginx/Apache 和 PHP-FPM，全异步非阻塞、协程实现。

### 环境要求

  * PHP > 7.2.0
  * PHP Swoole 扩展 >= 4.3.0
  * PHP Mbstring 扩展
  * PHP Pdo MySql 扩展

### 安装

#### 下载安装包

https://github.com/we7coreteam/w7-rangine-project-document/releases/ 下载最新版的文档系统源码

#### 解压文件

下载源码后，解压到服务器目录（如：/home/wwwroot）

进入文档系统源码目录，```cd /home/wwwroot/w7-rangine-project-document```

#### 安装扩展包

```php
composer install --no-dev
```

#### 运行系统

> 如果您的80端口被其他应用占用，您需要手动修改.env文件中的 SERVER_HTTP_PORT 选项

```
bin/server start
```
#### 安装系统

访问根目录/install.html,按照页面提示进行安装
安装完成后重启服务

关闭服务
```
bin/server stop
```
开启服务
```
bin/server start
```






