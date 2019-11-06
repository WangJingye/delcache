/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table tbl_admin
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_admin`;

CREATE TABLE `tbl_admin` (
  `admin_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名称',
  `password` char(64) DEFAULT '' COMMENT '密码',
  `realname` varchar(30) DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(20) DEFAULT '' COMMENT '联系电话',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `avatar` varchar(255) DEFAULT '' COMMENT '头像地址',
  `salt` char(4) DEFAULT '',
  `identity` tinyint(4) DEFAULT '0' COMMENT '0:普通用户 1:管理员',
  `last_login_time` int(11) DEFAULT '0' COMMENT '最后登录时间',
  `passwd_modify_time` int(11) DEFAULT '0' COMMENT '密码最后修改日期',
  `create_time` int(11) unsigned DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '信息修改时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '用户账号状态 0:删除,1:锁定（不可登陆）[2-8保留] 9 正常 ',
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='后台用户表';

LOCK TABLES `tbl_admin` WRITE;
/*!40000 ALTER TABLE `tbl_admin` DISABLE KEYS */;

INSERT INTO `tbl_admin` (`admin_id`, `username`, `password`, `realname`, `mobile`, `email`, `avatar`, `salt`, `identity`, `last_login_time`, `passwd_modify_time`, `create_time`, `update_time`, `status`)
VALUES
	(1,'admin','de5adcf92bd1be1f221e3bad88f97f6e','超级管理员','','11@qq.com','','6544',1,1572937618,0,1566546983,1572591787,1);

/*!40000 ALTER TABLE `tbl_admin` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tbl_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_menu`;

CREATE TABLE `tbl_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `url` varchar(200) DEFAULT '' COMMENT '菜单文件路径',
  `desc` varchar(255) DEFAULT '' COMMENT '菜单描述',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级菜单ID',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '菜单icon样式',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '菜单权重排序号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '菜单状态 1 有效 0 无效',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '创建菜单时间',
  `update_time` int(10) unsigned DEFAULT '0' COMMENT '修改菜单时间',
  PRIMARY KEY (`id`),
  KEY `pid` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='后台菜单数据表';

LOCK TABLES `tbl_menu` WRITE;
/*!40000 ALTER TABLE `tbl_menu` DISABLE KEYS */;

INSERT INTO `tbl_menu` (`id`, `name`, `url`, `desc`, `parent_id`, `icon`, `sort`, `status`, `create_time`, `update_time`)
VALUES
	(1,'系统管理','','root',0,'glyphicon glyphicon-cog',10,1,1562982765,1562982765),
	(2,'业务管理','','',0,'glyphicon glyphicon-briefcase',9,1,1562982765,1562982765),
	(3,'菜单权限管理','','',1,'glyphicon glyphicon-list',0,1,1562982765,1562982765),
	(4,'菜单列表','system/menu/index','',3,'',0,1,1562982765,1562982765),
	(5,'编辑菜单','system/menu/edit-menu','',4,'',0,1,1562982765,1562982765),
	(6,'角色列表','system/role/index','',3,'',0,1,1562982765,1562982765),
	(7,'编辑角色','system/role/edit-role','',6,'',0,1,1562982765,1562982765),
	(8,'设置角色权限','system/role/set-role-menu','',6,'',0,1,1562982765,1562982765),
	(9,'设置角色用户','system/role/set-role-admin','',6,'',0,1,1562982765,1562982765),
	(10,'后台账号管理','','',1,'glyphicon glyphicon-user',0,1,1562982765,1562982765),
	(11,'账号列表','system/admin/index','',10,'',0,1,1562982765,1562982765),
	(12,'编辑账号','system/admin/edit-admin','',11,'',0,1,1562982765,1562982765),
	(13,'菜单启用/禁用','system/menu/set-status','',4,'',0,1,1562982765,1562982765),
	(14,'账号启用/禁用','system/admin/set-status','',11,'',0,1,1562982765,1562982765),
	(15,'重置密码','system/admin/reset-password','',11,'',0,1,1563005512,1564936542),
	(16,'个人信息','system/admin/profile','',11,'',0,1,1563005512,1564936542),
	(17,'内容管理','','',2,'glyphicon glyphicon-bookmark',0,1,1572591987,1572591987),
	(18,'网站信息','erp/site-info/edit','',17,'glyphicon glyphicon-bookmark',0,1,1573021780,1573021780);

/*!40000 ALTER TABLE `tbl_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tbl_role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_role`;

CREATE TABLE `tbl_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '角色名称',
  `desc` varchar(255) DEFAULT '' COMMENT '描述',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '角色状态 0 无效 1 有效',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='角色表';

LOCK TABLES `tbl_role` WRITE;
/*!40000 ALTER TABLE `tbl_role` DISABLE KEYS */;

INSERT INTO `tbl_role` (`id`, `name`, `desc`, `status`, `create_time`, `update_time`)
VALUES
	(1,'管理员','root',1,1562982778,1562982993);

/*!40000 ALTER TABLE `tbl_role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tbl_role_admin
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_role_admin`;

CREATE TABLE `tbl_role_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL COMMENT '用户ID',
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `create_time` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;



# Dump of table tbl_role_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_role_menu`;

CREATE TABLE `tbl_role_menu` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `menu_id` int(11) NOT NULL COMMENT '菜单ID',
  `create_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `role-menu` (`role_id`,`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;



# Dump of table tbl_site_info
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_site_info`;

CREATE TABLE `tbl_site_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wechat_app_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `wechat_app_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `wechat_mch_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `wechat_pay_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `web_host` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `web_ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `tbl_site_info` WRITE;
/*!40000 ALTER TABLE `tbl_site_info` DISABLE KEYS */;

INSERT INTO `tbl_site_info` (`id`, `wechat_app_id`, `wechat_app_secret`, `wechat_mch_id`, `wechat_pay_key`, `web_host`, `web_ip`, `create_time`, `update_time`)
VALUES
	(1,'','','','','https://api.delcache.com','121.40.224.59',1573022804,1573022829);

/*!40000 ALTER TABLE `tbl_site_info` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
