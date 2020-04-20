/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : wxald

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2017-06-07 17:00:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wd_plugin_guestbook
-- ----------------------------
DROP TABLE IF EXISTS `cmf_plugin_guestbook`;
CREATE TABLE `cmf_plugin_guestbook` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '留言ID号',
  `name` varchar(50) DEFAULT NULL COMMENT '留言者姓名',
  `tel` varchar(15) DEFAULT NULL COMMENT '留言者电话',
  `fax` varchar(15) DEFAULT NULL COMMENT '留言者传真',
  `email` varchar(50) DEFAULT NULL COMMENT '留言者邮箱',
  `qq` varchar(15) DEFAULT NULL COMMENT '留言者QQ号',
  `subject` varchar(255) DEFAULT NULL COMMENT '留言标题',
  `message` text COMMENT '留言信息',
  `isread` tinyint(2) DEFAULT '1' COMMENT '是否查看过',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
SET FOREIGN_KEY_CHECKS=1;
