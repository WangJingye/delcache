# ************************************************************
# Database: base
# Generation Time: 2019-07-05 02:21:50 +0000
# ************************************************************

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
	(1,'administrator','0629af20c616bedf91fc8f7615a6aeea','超级管理员','13646622759','wangjingye646@163.com','/upload/system/image/a72db967f87cf53194aefafc77f1cc1a.png','8391',0,1562293268,0,0,0,1);

/*!40000 ALTER TABLE `tbl_admin` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tbl_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tbl_menu`;

CREATE TABLE `tbl_menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `url` varchar(200) DEFAULT NULL COMMENT '菜单文件路径',
  `desc` varchar(255) DEFAULT NULL COMMENT '菜单描述',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级菜单ID',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '显示类型(0:导航,1:菜单,2:功能)',
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

INSERT INTO `tbl_menu` (`id`, `name`, `url`, `desc`, `parent_id`, `type`, `icon`, `sort`, `status`, `create_time`, `update_time`)
VALUES
	(1,'系统管理','','',0,0,'glyphicon glyphicon-cog',10,1,1401160833,1404723369),
	(2,'业务管理',NULL,'',0,0,'glyphicon glyphicon-briefcase',9,1,1401160847,1404723373),
	(3,'菜单权限管理',NULL,NULL,1,1,'glyphicon glyphicon-list',0,1,0,0),
	(4,'菜单列表','system/menu/index',NULL,3,2,'',0,1,0,0),
	(5,'编辑菜单','system/menu/editMenu',NULL,4,3,'',0,1,0,0),
	(6,'角色列表','system/role/index','',3,2,'',0,1,0,0),
	(7,'编辑角色','system/role/editRole','',6,3,'',0,1,0,0),
	(8,'设置角色权限','system/role/setRoleMenu','',6,3,'',0,1,0,0),
	(9,'设置角色用户','system/role/setRoleAdmin','',6,3,'',0,1,0,0),
	(10,'后台账号管理','','',1,1,'glyphicon glyphicon-user',0,1,0,0),
	(11,'账号列表','system/admin/index','',10,2,'',0,1,0,0),
	(12,'编辑账号','system/admin/editAdmin','',11,3,'',0,1,0,0);

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
	(1,'超级管理员','root',1,0,0);

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

LOCK TABLES `tbl_role_admin` WRITE;
/*!40000 ALTER TABLE `tbl_role_admin` DISABLE KEYS */;

INSERT INTO `tbl_role_admin` (`id`, `admin_id`, `role_id`, `create_time`)
VALUES
	(1,1,1,1562048387),
	(4,2,1,1562225570);

/*!40000 ALTER TABLE `tbl_role_admin` ENABLE KEYS */;
UNLOCK TABLES;


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

LOCK TABLES `tbl_role_menu` WRITE;
/*!40000 ALTER TABLE `tbl_role_menu` DISABLE KEYS */;

INSERT INTO `tbl_role_menu` (`id`, `role_id`, `menu_id`, `create_time`)
VALUES
	(8,1,2,1562031886),
	(9,1,1,1562046879),
	(10,1,3,1562046879),
	(11,1,4,1562046879),
	(12,1,5,1562046879),
	(13,1,6,1562046879),
	(14,1,7,1562046879),
	(15,1,8,1562046879),
	(16,1,9,1562046879),
	(20,1,10,1562226334),
	(21,1,11,1562226334),
	(22,1,12,1562226334);

/*!40000 ALTER TABLE `tbl_role_menu` ENABLE KEYS */;
UNLOCK TABLES;

