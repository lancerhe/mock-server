/*
Navicat MySQL Data Transfer

Source Server         : 192.168.156.124
Source Server Version : 50170
Source Host           : 192.168.156.124:3306
Source Database       : mock

Target Server Type    : MYSQL
Target Server Version : 50170
File Encoding         : 65001

Date: 2015-02-26 16:44:16
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `mock`
-- ----------------------------
DROP TABLE IF EXISTS `mock`;
CREATE TABLE `mock` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uri_id` int(11) unsigned NOT NULL,
  `request_query` varchar(2000) NOT NULL,
  `request_post` varchar(2000) NOT NULL,
  `response_status_code` smallint(4) unsigned NOT NULL DEFAULT '200',
  `response_header` varchar(2000) NOT NULL,
  `response_body` varchar(2000) NOT NULL,
  `timeout` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of mock
-- ----------------------------
INSERT INTO `mock` VALUES ('4', '6', '{\"charset\":\"utf-8\"}', '[]', '200', '{\"Content-Type\":\"text\\/html;charset=utf-8\",\"Set-Cookie\":[\"a=1; expires=Tue, 24-Feb-15 09:32:13 GMT; domain=www.baidu.com; path=\\/\",\"b=17; path=\\/\",\"PHPSESSID=9kdmhcn8vi2aaujc99l7o5poi3; path=\\/\"]}', '{\"Content-Encoding\":\"gzip\",\"Content-Type\":\"text/html;charset=utf-8\"}', '0');
INSERT INTO `mock` VALUES ('5', '7', '{\"username\":\"LancerHe\"}', '{\"pass\":\"Key\"}', '302', '{\"Location\":\"http:\\/\\/192.168.156.124\\/?login={$request.query.username}&pass={$request.post.pass}\"}', '', '0');
INSERT INTO `mock` VALUES ('6', '5', '[]', '{\"username\":\"LancerHe\",\"avatar\":[\"1.jpg\",\"2.jpg\"]}', '200', '[]', 'Success', '500');
INSERT INTO `mock` VALUES ('7', '4', '{\"username\":\"LancerHe\",\"pid\":[\"66559\",\"16162\"]}', '[]', '200', '[]', 'Failure', '200');

-- ----------------------------
-- Table structure for `uri`
-- ----------------------------
DROP TABLE IF EXISTS `uri`;
CREATE TABLE `uri` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uri` varchar(2000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of uri
-- ----------------------------
INSERT INTO `uri` VALUES ('4', '/request/query');
INSERT INTO `uri` VALUES ('5', '/request/post');
INSERT INTO `uri` VALUES ('6', '/response/cookie');
INSERT INTO `uri` VALUES ('7', '/response/location');
