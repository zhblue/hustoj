set names utf8mb4; 
create database if not exists jol ;
use jol;

CREATE TABLE IF NOT EXISTS `compileinfo` (
  `solution_id` int(11) NOT NULL DEFAULT 0,
  `error` text,
  PRIMARY KEY (`solution_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `contest` (
  `contest_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `defunct` char(1) NOT NULL DEFAULT 'N',
  `description` text,
  `private` tinyint(4) NOT NULL DEFAULT '0',
  `langmask` int NOT NULL DEFAULT '0' COMMENT 'bits for LANG to mask',
  `password` CHAR( 16 ) NOT NULL DEFAULT '',
  `user_id` varchar(48) NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`contest_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `contest_problem` (
  `problem_id` int(11) NOT NULL DEFAULT '0',
  `contest_id` int(11) DEFAULT NULL,
  `title` char(200) NOT NULL DEFAULT '',
  `num` int(11) NOT NULL DEFAULT '0',
  `c_accepted` int(11) NOT NULL DEFAULT '0',
  `c_submit` int(11) NOT NULL DEFAULT '0',
  KEY `Index_contest_id` (`contest_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

 CREATE TABLE `loginlog` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `user_id` varchar(48) NOT NULL DEFAULT '',
  `password` varchar(40) DEFAULT NULL,
  `ip` varchar(46) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `user_log_index` (`user_id`,`time`),
  KEY `user_time_index` (`user_id`,`time`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ;

CREATE TABLE IF NOT EXISTS `mail` (
  `mail_id` int(11) NOT NULL AUTO_INCREMENT,
  `to_user` varchar(48) NOT NULL DEFAULT '',
  `from_user` varchar(48) NOT NULL DEFAULT '',
  `title` varchar(200) NOT NULL DEFAULT '',
  `content` text,
  `new_mail` tinyint(1) NOT NULL DEFAULT '1',
  `reply` tinyint(4) DEFAULT '0',
  `in_date` datetime DEFAULT NULL,
  `defunct` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`mail_id`),
  KEY `uid` (`to_user`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(48) NOT NULL DEFAULT '',
  `title` varchar(200) NOT NULL DEFAULT '',
  `content` mediumtext NOT NULL,
  `time` datetime NOT NULL DEFAULT '2016-05-13 19:24:00',
  `importance` tinyint(4) NOT NULL DEFAULT '0',
  `menu` int(11) NOT NULL DEFAULT '0',
  `defunct` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1004 DEFAULT CHARSET=utf8mb4;

INSERT INTO `news`(user_id,title,content,time,importance,menu,defunct) VALUES ('zhblue','HelloWorld!','\r\n    这是一个新安装的HUSTOJ系统，有关它的使用和配置，请看以下链接：\r\n<br />\r\n\r\n        <ul>\r\n                <li>\r\n                        <a href=\"http://hustoj.com\" target=\"_blank\">hustoj.com</a>&nbsp;最新的常见问题\r\n          </li>\r\n               <li>\r\n                        <a href=\"https://gitee.com/zhblue/hustoj\" target=\"_blank\">gitee.com</a>&nbsp;国内的镜像站，不定期同步github的源码\r\n               </li>\r\n               <li>\r\n                        <a href=\"http://github.com/zhblue/hustoj\" target=\"_blank\">github.com</a>&nbsp;国外的主站，最新源码在这里\r\n                </li>\r\n               <li>\r\n                        <a href=\"https://zhblue.github.io/hustoj/\" target=\"_blank\">https://zhblue.github.io/hustoj/</a>&nbsp;中文基础操作文档\r\n           </li>\r\n               <li>\r\n                        <a href=\"https://gitee.com/zhblue/hustoj/tree/master/wiki\" target=\"_blank\">wiki</a>&nbsp;英文高阶文档\r\n           </li>\r\n       </ul>\r\n<br />\r\n\r\n 如果需要题目，可以访问：\r\n<br />\r\n\r\n      <ul>\r\n                <li>\r\n                        <a href=\"http://tk.hustoj.com\" target=\"_blank\">tk.hustoj.com</a>&nbsp;注册即可下载免费专区的1000多道题目，使用购物车可以批量下载，下载到的xm<x>l文件可以直接导入系统。\r\n          </li>\r\n               <li>\r\n                        <a href=\"https://github.com/zhblue/freeproblemset/tree/master/fps-examples\" target=\"_blank\">FPS sample</a>&nbsp;FPS主站样例有部分题目可用。\r\n            </li>\r\n               <li>\r\n                        <a href=\"https://github.com/Azure99/EasyFPSViewer/releases\" target=\"_blank\">EasyFPSViewer</a>&nbsp;是一个Windows下的FPS/xm<x>l编辑查看工具&#44;可以查看、分割、提取xm<x>l中的题目。\r\n             </li><li>\r\n<a href=\"http://royqh.net/redpandacpp/download/\" target=\"_blank\">小熊猫C++</a>&nbsp;是一个跨平台的IDE, 是DEV-C++的精神继承者。\r\n</li>\r\n       </ul>\r\n<br />\r\n\r\n <br />\r\n<br />\r\n\r\n        当你已经熟练使用本系统，可以在后台公告列表编辑本页内容或者隐藏它。\r\n<br />','2009-06-13 18:00:00',0,0,'N');
INSERT INTO `news`(user_id,title,content,time,importance,menu,defunct) VALUES ('zhblue','题单模板','下面是一个题单模板，注意其中plist=后面的题号需要自己整理。\r\n<div class=\"panel panel-success panel-heading panel-title \" onclick=\"$(\'#simple\').toggle()\" style=\"cursor:pointer\" >\r\n       初级操作\r\n</div>\r\n<div id=\"simple\" class=\"container\" style=\"display:none;\">\r\n       [plist=1001,1002,1003,1004,1005,1006,1007,1008]1.高精度运算基础[/plist]\r\n[plist=1009,1010,1011,1012,1013]2.排序[/plist]\r\n[plist=1014,1015,1016,1017,1018,1019,1020,1021,1022,1023,1024]3.递推[/plist] \r\n[plist=1025,1026,1027,1028,1029,1030,1031,1032,1033,1034]4.递归[/plist]\r\n</div>\r\n<hr />\r\n<div class=\"panel panel-success panel-heading panel-title \" onclick=\"$(\'#middle\').toggle()\" style=\"cursor:pointer\" >\r\n     中级操作\r\n</div>\r\n<div id=\"middle\" class=\"container\" style=\"display:none;\">\r\n       [plist=1035,1036,1037,1038,1039,1040]5.搜索[/plist]\r\n [plist=1032,1035,1041,1042,1043,1044]6.回溯[/plist]\r\n[plist=1045,1046,1047,1048,1049,1050]7.贪心[/plist] \r\n[plist=1054,1055,1056,1058,1059]8.分治[/plist]\r\n</div>\r\n<hr />\r\n<div class=\"panel panel-success panel-heading panel-title \" onclick=\"$(\'#dp\').toggle()\"  style=\"cursor:pointer\" >\r\n 高级操作\r\n</div>\r\n<div id=\"dp\" class=\"container\" style=\"display:none;\">\r\n   [plist=1076,1077,1078,1079,1080,1081,1082,1083,1084,1085,1086,1087,1088,1089,1090]9.动态规划1[/plist]\r\n[plist=1076,1077,1078,1079,1080,1081,1082,1083,1084,1085,1086,1087,1088,1089,1090]10.动态规划2[/plist]\r\n</div>\r\n                        ','2024-08-06 06:54:43',0,1,'N');
CREATE TABLE IF NOT EXISTS `privilege` (
  `user_id` char(48) NOT NULL DEFAULT '',
  `rightstr` char(30) NOT NULL DEFAULT '',
  `valuestr` char(11) NOT NULL DEFAULT 'true',
  `defunct` char(1) NOT NULL DEFAULT 'N',
  KEY `user_id_index` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `problem` (
  `problem_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL DEFAULT '',
  `description` mediumtext,
  `input` mediumtext,
  `output` mediumtext,
  `sample_input` text,
  `sample_output` text,
  `spj` char(1) NOT NULL DEFAULT '0',
  `hint` mediumtext,
  `source` varchar(100) DEFAULT NULL,
  `in_date` datetime DEFAULT NULL,
  `time_limit` DECIMAL(10,3) NOT NULL DEFAULT 0,
  `memory_limit` int(11) NOT NULL DEFAULT 0,
  `defunct` char(1) NOT NULL DEFAULT 'N',
  `accepted` int(11) DEFAULT '0',
  `submit` int(11) DEFAULT '0',
  `solved` int(11) DEFAULT '0',
  `remote_oj` varchar(16) DEFAULT NULL,
  `remote_id` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`problem_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `reply` (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `author_id` varchar(48) NOT NULL,
  `time` datetime NOT NULL DEFAULT '2016-05-13 19:24:00',
  `content` text NOT NULL,
  `topic_id` int(11) NOT NULL,
  `status` int(2) NOT NULL DEFAULT '0',
  `ip` varchar(46) NOT NULL,
  PRIMARY KEY (`rid`),
  KEY `author_id` (`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `sim` (
  `s_id` int(11) NOT NULL,
  `sim_s_id` int(11) DEFAULT NULL,
  `sim` int(11) DEFAULT NULL,
  PRIMARY KEY (`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `solution` (
  `solution_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `problem_id` int(11) NOT NULL DEFAULT 0,
  `user_id` char(48) NOT NULL,
  `nick` char(20) NOT NULL DEFAULT '', 
  `time` int(11) NOT NULL DEFAULT 0,
  `memory` int(11) NOT NULL DEFAULT 0,
  `in_date` datetime NOT NULL DEFAULT '2016-05-13 19:24:00',
  `result` smallint(6) NOT NULL DEFAULT '0',
  `language` INT UNSIGNED NOT NULL DEFAULT '0',
  `ip` char(46) NOT NULL,
  `contest_id` int(11) DEFAULT 0,
  `valid` tinyint(4) NOT NULL DEFAULT '1',
  `num` tinyint(4) NOT NULL DEFAULT '-1',
  `code_length` int(11) NOT NULL DEFAULT 0,
  `judgetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `pass_rate` DECIMAL(4,3) UNSIGNED NOT NULL DEFAULT 0,
  `lint_error` int UNSIGNED NOT NULL DEFAULT 0,
  `judger` CHAR(16) NOT NULL DEFAULT 'LOCAL',
  `remote_oj` char(16) not NULL DEFAULT '',
  `remote_id` char(32) not NULL DEFAULT '',
  PRIMARY KEY (`solution_id`),
  KEY `uid` (`user_id`),
  KEY `pid` (`problem_id`),
  KEY `res` (`result`),
  KEY `cid` (`contest_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1001 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `source_code` (
  `solution_id` int(11) NOT NULL,
  `source` text NOT NULL,
  PRIMARY KEY (`solution_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS source_code_user like source_code;

CREATE TABLE IF NOT EXISTS `topic` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varbinary(60) NOT NULL,
  `status` int(2) NOT NULL DEFAULT '0',
  `top_level` int(2) NOT NULL DEFAULT '0',
  `cid` int(11) DEFAULT NULL,
  `pid` int(11) NOT NULL,
  `author_id` varchar(48) NOT NULL,
  PRIMARY KEY (`tid`),
  KEY `cid` (`cid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` varchar(48) NOT NULL DEFAULT '',
  `email` varchar(100) DEFAULT NULL,
  `submit` int(11) DEFAULT '0',
  `solved` int(11) DEFAULT '0',
  `defunct` char(1) NOT NULL DEFAULT 'N',
  `ip` varchar(46) NOT NULL DEFAULT '',
  `accesstime` datetime DEFAULT NULL,
  `volume` int(11) NOT NULL DEFAULT '1',
  `language` int(11) NOT NULL DEFAULT '1',
  `password` varchar(32) DEFAULT NULL,
  `reg_time` datetime DEFAULT NULL,
  `nick` varchar(20) NOT NULL DEFAULT '',
  `school` varchar(20) NOT NULL DEFAULT '',
  `group_name` varchar(16) not null default '',
  `activecode` varchar(16) not null default '',
  `starred` int(11) not null default '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `online` (
  `hash` varchar(32) NOT NULL,
  `ip` varchar(46)  NOT NULL default '',
  `ua` varchar(255)  NOT NULL default '',
  `refer` varchar(255)  default NULL,
  `lastmove` int(10) NOT NULL,
  `firsttime` int(10) default NULL,
  `uri` varchar(255) default NULL,
  PRIMARY KEY  (`hash`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4 ;

CREATE TABLE IF NOT EXISTS `runtimeinfo` (
  `solution_id` int(11) NOT NULL DEFAULT 0,
  `error` text,
  PRIMARY KEY (`solution_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `custominput` (
  `solution_id` int(11) NOT NULL DEFAULT 0,
  `input_text` text,
  PRIMARY KEY (`solution_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `printer` (
  `printer_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` char(48) NOT NULL,
  `in_date` datetime NOT NULL DEFAULT '2018-03-13 19:38:00',
  `status` smallint(6) NOT NULL DEFAULT '0',
  `worktime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `printer` CHAR(16) NOT NULL DEFAULT 'LOCAL',
  `content` text NOT NULL ,
  PRIMARY KEY (`printer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `balloon` (
  `balloon_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` char(48) NOT NULL,
  `sid` int(11) NOT NULL ,
  `cid` int(11) NOT NULL ,
  `pid` int(11) NOT NULL ,
  `status` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`balloon_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `share_code` (
  `share_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(48)  DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `share_code` text DEFAULT NULL,
  `language` varchar(32) DEFAULT NULL,
  `share_time` datetime DEFAULT NULL,
  PRIMARY KEY (`share_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb4;

delimiter //
drop trigger if exists simfilter//
create trigger simfilter
before insert on sim
for each row
begin
 declare new_user_id varchar(64);
 declare old_user_id varchar(64);
 select user_id from solution where solution_id=new.s_id into new_user_id;
 select user_id from solution where solution_id=new.sim_s_id into old_user_id;
 if old_user_id=new_user_id then
	set new.s_id=0;
 end if;
 
end//

CREATE PROCEDURE DEFAULT_ADMINISTRATOR(user_name VARCHAR(48))
BEGIN
    DECLARE privileged_count INT DEFAULT 0;
    SET privileged_count=(SELECT COUNT(1) FROM `privilege`);
    IF privileged_count=0 THEN
        INSERT INTO privilege values(user_name, 'administrator', 'true', 'N');
    end if;
end//
 
delimiter ;
