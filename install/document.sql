/*
Navicat MySQL Data Transfer

Source Server         : test
Source Server Version : 50562
Source Host           : 212.64.83.243:3306
Source Database       : document_test4

Target Server Type    : MYSQL
Target Server Version : 50562
File Encoding         : 65001

Date: 2020-07-15 11:02:17
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ims_app
-- ----------------------------
DROP TABLE IF EXISTS `ims_app`;
CREATE TABLE `ims_app` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `appid` varchar(18) NOT NULL,
  `appsecret` varchar(64) NOT NULL,
  `user_id` int(11) DEFAULT '0' COMMENT '用户id,一个appid对应一个用户',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ims_app
-- ----------------------------

-- ----------------------------
-- Table structure for ims_cache
-- ----------------------------
DROP TABLE IF EXISTS `ims_cache`;
CREATE TABLE `ims_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `expired_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ims_cache
-- ----------------------------

-- ----------------------------
-- Table structure for ims_document
-- ----------------------------
DROP TABLE IF EXISTS `ims_document`;
CREATE TABLE `ims_document` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT '' COMMENT '描述',
  `creator_id` int(11) NOT NULL DEFAULT '0' COMMENT '创建者id',
  `cover` varchar(120) DEFAULT '' COMMENT '文档封面',
  `is_public` tinyint(1) NOT NULL DEFAULT '2' COMMENT '文档是否为公有文档 1:公有,2:私有',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `creator_id` (`creator_id`),
  KEY `updated_at` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ims_document
-- ----------------------------

-- ----------------------------
-- Table structure for ims_document_chapter
-- ----------------------------
DROP TABLE IF EXISTS `ims_document_chapter`;
CREATE TABLE `ims_document_chapter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '文档名称',
  `document_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '壳id',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序，越大越靠前',
  `is_dir` tinyint(1) DEFAULT '0' COMMENT '当前章节是否是目录(1：是，0：否)',
  `levels` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `default_show_chapter_id` int(11) DEFAULT '0' COMMENT '默认显示的章节',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`,`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ims_document_chapter
-- ----------------------------

-- ----------------------------
-- Table structure for ims_document_chapter_api
-- ----------------------------
DROP TABLE IF EXISTS `ims_document_chapter_api`;
CREATE TABLE `ims_document_chapter_api` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `chapter_id` int(11) NOT NULL DEFAULT '0' COMMENT '章节ID',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '接口地址',
  `method` tinyint(4) NOT NULL COMMENT '请求方法:1、GET,2、POST,3、PUT,4、OPTIONS,5、DELETE',
  `status_code` int(11) NOT NULL DEFAULT '200' COMMENT '状态码',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '接口描述',
  `body_param_location` tinyint(4) NOT NULL DEFAULT '3' COMMENT 'body_param默认请求方式',
  PRIMARY KEY (`id`),
  KEY `document_chapter_api_chapter_id_index` (`chapter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ims_document_chapter_api
-- ----------------------------

-- ----------------------------
-- Table structure for ims_document_chapter_api_extend
-- ----------------------------
DROP TABLE IF EXISTS `ims_document_chapter_api_extend`;
CREATE TABLE `ims_document_chapter_api_extend` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `chapter_id` int(11) NOT NULL DEFAULT '0' COMMENT '章节ID',
  `extend` longtext NOT NULL COMMENT '扩展markdown格式数据',
  PRIMARY KEY (`id`),
  KEY `document_chapter_api_extend_chapter_id_index` (`chapter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ims_document_chapter_api_extend
-- ----------------------------

-- ----------------------------
-- Table structure for ims_document_chapter_api_param
-- ----------------------------
DROP TABLE IF EXISTS `ims_document_chapter_api_param`;
CREATE TABLE `ims_document_chapter_api_param` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `chapter_id` int(11) NOT NULL DEFAULT '0' COMMENT '章节ID',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '上级ID',
  `location` tinyint(4) NOT NULL COMMENT '请求类型：1request.header,2request.query3requeset.body.form-data4requeset.body.x-www-form-urlencoded5requeset.body.raw6requeset.body.binary 7reponse.header8、reponse.body.form9、requeset.body.x-www-form-urlencoded10、requeset.body.raw11、requeset.body.binary',
  `reponse_id` int(11) NOT NULL DEFAULT '0' COMMENT '响应数据ID',
  `type` tinyint(4) NOT NULL COMMENT '数据类型:1、int2、string...',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '数据键值',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '数据键值描述',
  `enabled` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否必填1否2是',
  `rule` varchar(255) NOT NULL DEFAULT '' COMMENT 'moke规则',
  `default_value` varchar(255) NOT NULL DEFAULT '' COMMENT '初始值',
  PRIMARY KEY (`id`),
  KEY `document_chapter_api_param_chapter_id_index` (`chapter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ims_document_chapter_api_param
-- ----------------------------

-- ----------------------------
-- Table structure for ims_document_chapter_api_reponse
-- ----------------------------
DROP TABLE IF EXISTS `ims_document_chapter_api_reponse`;
CREATE TABLE `ims_document_chapter_api_reponse` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chapter_id` int(11) NOT NULL DEFAULT '0' COMMENT '章节ID',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '响应描述',
  PRIMARY KEY (`id`),
  KEY `document_chapter_api_reponse_chapter_id_index` (`chapter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ims_document_chapter_api_reponse
-- ----------------------------

-- ----------------------------
-- Table structure for ims_document_chapter_content
-- ----------------------------
DROP TABLE IF EXISTS `ims_document_chapter_content`;
CREATE TABLE `ims_document_chapter_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chapter_id` int(11) NOT NULL,
  `content` longtext,
  `layout` tinyint(1) NOT NULL COMMENT '章节格式 1 markdown 2 富文本',
  PRIMARY KEY (`id`),
  KEY `chapter_id` (`chapter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ims_document_chapter_content
-- ----------------------------

-- ----------------------------
-- Table structure for ims_document_permission
-- ----------------------------
DROP TABLE IF EXISTS `ims_document_permission`;
CREATE TABLE `ims_document_permission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL COMMENT '文档id',
  `permission` tinyint(4) NOT NULL COMMENT '用户在文档中的权限(1: 管理员,2:操作员,3:阅读者)',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of ims_document_permission
-- ----------------------------

-- ----------------------------
-- Table structure for ims_migration
-- ----------------------------
DROP TABLE IF EXISTS `ims_migration`;
CREATE TABLE `ims_migration` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ims_migration
-- ----------------------------
INSERT INTO `ims_migration` VALUES ('1', '2020_01_02_194246_create_user_third_party', '1');
INSERT INTO `ims_migration` VALUES ('2', '2020_02_17_121335_alter_setting', '1');
INSERT INTO `ims_migration` VALUES ('3', '2020_03_02_153429_create_app', '1');
INSERT INTO `ims_migration` VALUES ('4', '2020_03_03_152902_alter_star', '1');
INSERT INTO `ims_migration` VALUES ('5', '2020_03_03_154818_alter_document', '1');
INSERT INTO `ims_migration` VALUES ('6', '2020_03_23_114510_table_operate_log', '1');
INSERT INTO `ims_migration` VALUES ('7', '2020_04_07_145338_create_document_chapter_api', '1');
INSERT INTO `ims_migration` VALUES ('8', '2020_04_07_145420_create_document_chapter_api_param', '1');
INSERT INTO `ims_migration` VALUES ('9', '2020_04_07_151451_create_document_chapter_api_extend', '1');
INSERT INTO `ims_migration` VALUES ('10', '2020_05_27_092518_create_document_chapter_api_reponse_table', '1');
INSERT INTO `ims_migration` VALUES ('11', '2020_06_11_145556_add_rule_to_document_chapter_api_param_table', '1');

-- ----------------------------
-- Table structure for ims_session
-- ----------------------------
DROP TABLE IF EXISTS `ims_session`;
CREATE TABLE `ims_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(50) NOT NULL,
  `data` varchar(1000) NOT NULL,
  `expired_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ims_session
-- ----------------------------

-- ----------------------------
-- Table structure for ims_setting
-- ----------------------------
DROP TABLE IF EXISTS `ims_setting`;
CREATE TABLE `ims_setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(60) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ims_setting
-- ----------------------------

-- ----------------------------
-- Table structure for ims_user
-- ----------------------------
DROP TABLE IF EXISTS `ims_user`;
CREATE TABLE `ims_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `userpass` varchar(255) NOT NULL DEFAULT '' COMMENT '登录密码',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `is_ban` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '1:禁止,0:正常',
  `has_privilege` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否具有特权 0:无,1:有',
  `group_id` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `updated_at` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ims_user
-- ----------------------------

-- ----------------------------
-- Table structure for ims_user_operate_log
-- ----------------------------
DROP TABLE IF EXISTS `ims_user_operate_log`;
CREATE TABLE `ims_user_operate_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '操作人id',
  `document_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL COMMENT '章节id',
  `target_user_id` int(11) DEFAULT '0' COMMENT '目标用户id,比如文档转让的目标用户id',
  `operate` tinyint(4) NOT NULL COMMENT '操作（1：新增，2：编辑，3：删除）',
  `remark` varchar(120) DEFAULT '',
  `created_at` int(11) NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ims_user_operate_log
-- ----------------------------

-- ----------------------------
-- Table structure for ims_user_star
-- ----------------------------
DROP TABLE IF EXISTS `ims_user_star`;
CREATE TABLE `ims_user_star` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `chapter_id` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_document_chapter` (`user_id`,`document_id`,`chapter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ims_user_star
-- ----------------------------

-- ----------------------------
-- Table structure for ims_user_third_party
-- ----------------------------
DROP TABLE IF EXISTS `ims_user_third_party`;
CREATE TABLE `ims_user_third_party` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `openid` varchar(200) NOT NULL,
  `username` varchar(100) NOT NULL,
  `source` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of ims_user_third_party
-- ----------------------------
