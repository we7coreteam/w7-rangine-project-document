### 简介

一款基于软擎框架（[https://www.rangine.com/](https://www.rangine.com/)）的开源Markdown文档系统。

常驻内存，不依赖传统的 Nginx/Apache 和 PHP-FPM，全异步非阻塞、协程实现。

### 环境要求

  * PHP > 7.2.0
  * PHP Swoole 扩展 >= 4.3.0
  * PHP Mbstring 扩展
  * PHP Pdo MySql 扩展
  * PHP Redis 扩展

### 安装

#### 下载安装包

https://github.com/we7coreteam/w7-rangine-project-document/releases/ 下载最新版的文档系统源码

#### 解压文件

下载源码后，解压到服务器目录（如：/home/wwwroot）

进入文档系统源码目录，```cd /home/wwwroot/w7-rangine-project-document```

#### 安装扩展包

```
composer install
```

#### 运行系统

> 如果您的99端口被其他应用占用，您需要手动修改config/server文件中的 SERVER_HTTP_PORT 默认端口号

项目根目录下执行命令
```
bin/server start
```
#### 安装系统

访问根目录/install,按照页面提示进行安装
安装完成后重启服务

重启服务：项目根目录下执行命令
```
sh restart.sh
```

如需重新安装
请手动删除 runtime/install.lock 与.env文件 并且重启服务






