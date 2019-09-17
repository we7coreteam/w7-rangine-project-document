### 文档系统代码

Gitee : https://gitee.com/we7coreteam/document-apiserver.git

### 环境要求

  * PHP > 7.0.0
  
  * PHP Swoole 扩展 >= 4.3.0
  
  * PHP Mbstring 扩展
  
  * PHP Pdo MySql 扩展
  
  * Compoer

### 安装

> 首先[下载源码](https://gitee.com/we7coreteam/document-apiserver/releases), 下载完成后上传到你的服务器, 解压并初始化项目

```
# 解压项目
$ cd work_dir
$ unzip /tmp/document.zip -d ./document
$ cd document

# 项目初始化
$ php bin/gerent.php install:init

# 项目启动
$ php bin/server.php http start
```










