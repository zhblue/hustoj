set names utf8mb4; 
create database if not exists jol ;
use jol;
CREATE TABLE IF NOT EXISTS `compileinfo` (
  `solution_id` int(11) NOT NULL DEFAULT 0 COMMENT '提交ID(主键)',
  `error` text COMMENT '编译错误信息',
  PRIMARY KEY (`solution_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `contest` (
  `contest_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '比赛ID',
  `title` varchar(255) DEFAULT NULL COMMENT '比赛标题',
  `start_time` datetime DEFAULT NULL COMMENT '比赛开始时间',
  `end_time` datetime DEFAULT NULL COMMENT '比赛结束时间',
  `defunct` char(1) NOT NULL DEFAULT 'N' COMMENT '已停用标记(N=正常 Y=停用)',
  `description` text COMMENT '比赛说明',
  `private` tinyint(4) NOT NULL DEFAULT '0' COMMENT '私有标记(0=公开 1=私有)',
  `langmask` int NOT NULL DEFAULT '0' COMMENT 'bits for LANG to mask',
  `password` CHAR( 16 ) NOT NULL DEFAULT '' COMMENT '比赛进入密码',
  `contest_type` smallint unsigned NOT NULL DEFAULT '0' COMMENT '比赛类型(0=ACM 1=OI ...)',
  `subnet` varchar(255) NOT NULL DEFAULT '' COMMENT '允许参加的IP段',
  `user_id` varchar(48) NOT NULL DEFAULT 'admin' COMMENT '创建者用户名',
  PRIMARY KEY (`contest_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `contest_problem` (
  `problem_id` int(11) NOT NULL DEFAULT '0' COMMENT '题号',
  `contest_id` int(11) DEFAULT NULL COMMENT '所属比赛ID',
  `title` char(200) NOT NULL DEFAULT '' COMMENT '题目标题(比赛显示)',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '比赛内编号(A/B/C...)',
  `c_accepted` int(11) NOT NULL DEFAULT '0' COMMENT '比赛内通过次数',
  `c_submit` int(11) NOT NULL DEFAULT '0' COMMENT '比赛内提交次数',
  KEY `Index_contest_id` (`contest_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

 CREATE TABLE `loginlog` (
  `log_id` int NOT NULL AUTO_INCREMENT COMMENT '登录日志ID',
  `user_id` varchar(48) NOT NULL DEFAULT '' COMMENT '登录用户名',
  `password` varchar(40) DEFAULT NULL COMMENT '登录尝试密码(明文,用于审计)',
  `ip` varchar(46) DEFAULT NULL COMMENT '登录IP',
  `time` datetime DEFAULT NULL COMMENT '登录时间',
  PRIMARY KEY (`log_id`),
  KEY `user_log_index` (`user_id`,`time`),
  KEY `user_time_index` (`user_id`,`time`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 ;

CREATE TABLE IF NOT EXISTS `mail` (
  `mail_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '邮件ID',
  `to_user` varchar(48) NOT NULL DEFAULT '' COMMENT '收件人',
  `from_user` varchar(48) NOT NULL DEFAULT '' COMMENT '发件人',
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '邮件标题',
  `content` text COMMENT '邮件正文',
  `new_mail` tinyint(1) NOT NULL DEFAULT '1' COMMENT '未读标记(1=未读 0=已读)',
  `reply` tinyint(4) DEFAULT '0' COMMENT '回复ID(指向被回复邮件)',
  `in_date` datetime DEFAULT NULL COMMENT '发送时间',
  `defunct` char(1) NOT NULL DEFAULT 'N' COMMENT '已删除标记(N=正常 Y=已删)',
  PRIMARY KEY (`mail_id`),
  KEY `uid` (`to_user`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '公告ID',
  `user_id` varchar(48) NOT NULL DEFAULT '' COMMENT '发布者',
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '公告标题',
  `content` mediumtext NOT NULL COMMENT '公告正文',
  `time` datetime NOT NULL DEFAULT '2016-05-13 19:24:00' COMMENT '发布时间',
  `importance` tinyint(4) NOT NULL DEFAULT '0' COMMENT '重要级别(0=普通,越大越重要)',
  `menu` int(11) NOT NULL DEFAULT '0' COMMENT '菜单显示标记',
  `defunct` char(1) NOT NULL DEFAULT 'N' COMMENT '已停用标记(N=显示 Y=隐藏)',
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1004 DEFAULT CHARSET=utf8mb4;

INSERT INTO `news`(user_id,title,content,time,importance,menu,defunct) VALUES ('zhblue','HelloWorld!','\r\n    这是一个新安装的HUSTOJ系统，有关它的使用和配置，请看以下链接：\r\n<br />\r\n\r\n        <ul>\r\n                <li>\r\n                        <a href=\"http://hustoj.com\" target=\"_blank\">hustoj.com</a>&nbsp;最新的常见问题\r\n          </li>\r\n               <li>\r\n                        <a href=\"https://gitee.com/zhblue/hustoj\" target=\"_blank\">gitee.com</a>&nbsp;国内的镜像站，不定期同步github的源码\r\n               </li>\r\n               <li>\r\n                        <a href=\"http://github.com/zhblue/hustoj\" target=\"_blank\">github.com</a>&nbsp;国外的主站，最新源码在这里\r\n                </li>\r\n               <li>\r\n                        <a href=\"https://zhblue.github.io/hustoj/\" target=\"_blank\">https://zhblue.github.io/hustoj/</a>&nbsp;中文基础操作文档\r\n           </li>\r\n               <li>\r\n                        <a href=\"https://gitee.com/zhblue/hustoj/tree/master/wiki\" target=\"_blank\">wiki</a>&nbsp;英文高阶文档\r\n           </li>\r\n       </ul>\r\n<br />\r\n\r\n 如果需要题目，可以访问：\r\n<br />\r\n\r\n      <ul>\r\n                <li>\r\n                        <a href=\"http://tk.hustoj.com\" target=\"_blank\">tk.hustoj.com</a>&nbsp;注册即可下载免费专区的1000多道题目，使用购物车可以批量下载，下载到的xm<x>l文件可以直接导入系统。\r\n          </li>\r\n               <li>\r\n                        <a href=\"https://github.com/zhblue/freeproblemset/tree/master/fps-examples\" target=\"_blank\">FPS sample</a>&nbsp;FPS主站样例有部分题目可用。\r\n            </li>\r\n               <li>\r\n                        <a href=\"https://github.com/Azure99/EasyFPSViewer/releases\" target=\"_blank\">EasyFPSViewer</a>&nbsp;是一个Windows下的FPS/xm<x>l编辑查看工具&#44;可以查看、分割、提取xm<x>l中的题目。\r\n             </li><li>\r\n<a href=\"http://royqh.net/redpandacpp/download/\" target=\"_blank\">小熊猫C++</a>&nbsp;是一个跨平台的IDE, 是DEV-C++的精神继承者。\r\n</li>\r\n       </ul>\r\n<br />\r\n\r\n <br />\r\n<br />\r\n\r\n        当你已经熟练使用本系统，可以在后台公告列表编辑本页内容或者隐藏它。\r\n<br />','2009-06-13 18:00:00',0,0,'N');
INSERT INTO `news`(user_id,title,content,time,importance,menu,defunct) VALUES ('zhblue','题单模板','下面是一个题单模板，注意其中plist=后面的题号需要自己整理。\r\n<div class=\"panel panel-success panel-heading panel-title \" control=\'simple\' style=\"cursor:pointer\" >\r\n       初级操作\r\n</div>\r\n<div id=\"simple\" class=\"container\" style=\"display:none;\">\r\n       [plist=1001,1002,1003,1004,1005,1006,1007,1008]1.高精度运算基础[/plist]\r\n[plist=1009,1010,1011,1012,1013]2.排序[/plist]\r\n[plist=1014,1015,1016,1017,1018,1019,1020,1021,1022,1023,1024]3.递推[/plist] \r\n[plist=1025,1026,1027,1028,1029,1030,1031,1032,1033,1034]4.递归[/plist]\r\n</div>\r\n<hr />\r\n<div class=\"panel panel-success panel-heading panel-title \" control=\'middle\' style=\"cursor:pointer\" >\r\n     中级操作\r\n</div>\r\n<div id=\"middle\" class=\"container\" style=\"display:none;\">\r\n       [plist=1035,1036,1037,1038,1039,1040]5.搜索[/plist]\r\n [plist=1032,1035,1041,1042,1043,1044]6.回溯[/plist]\r\n[plist=1045,1046,1047,1048,1049,1050]7.贪心[/plist] \r\n[plist=1054,1055,1056,1058,1059]8.分治[/plist]\r\n</div>\r\n<hr />\r\n<div class=\"panel panel-success panel-heading panel-title \" control=\'dp\' style=\"cursor:pointer\" >\r\n 高级操作\r\n</div>\r\n<div id=\"dp\" class=\"container\" style=\"display:none;\">\r\n   [plist=1076,1077,1078,1079,1080,1081,1082,1083,1084,1085,1086,1087,1088,1089,1090]9.动态规划1[/plist]\r\n[plist=1076,1077,1078,1079,1080,1081,1082,1083,1084,1085,1086,1087,1088,1089,1090]10.动态规划2[/plist]\r\n</div>\r\n                        ','2024-08-06 06:54:43',0,1,'N');
CREATE TABLE IF NOT EXISTS `privilege` (
  `user_id` char(48) NOT NULL DEFAULT '' COMMENT '用户名',
  `rightstr` char(30) NOT NULL DEFAULT '' COMMENT '权限标识(如 administrator/source_browser)',
  `valuestr` char(11) NOT NULL DEFAULT 'true' COMMENT '权限值',
  `defunct` char(1) NOT NULL DEFAULT 'N' COMMENT '已停用标记',
  KEY `user_id_index` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `problem` (
  `problem_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '题号',
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '题目标题',
  `description` mediumtext COMMENT '题目描述',
  `input` mediumtext COMMENT '输入说明',
  `output` mediumtext COMMENT '输出说明',
  `sample_input` text COMMENT '样例输入',
  `sample_output` text COMMENT '样例输出',
  `spj` char(1) NOT NULL DEFAULT '0' COMMENT '特判标记(0=否 1=是)',
  `hint` mediumtext COMMENT '提示',
  `source` varchar(100) DEFAULT NULL COMMENT '题目来源',
  `in_date` datetime DEFAULT NULL COMMENT '添加时间',
  `time_limit` DECIMAL(10,3) NOT NULL DEFAULT 0 COMMENT '时间限制(秒)',
  `memory_limit` int(11) NOT NULL DEFAULT 0 COMMENT '内存限制(KB)',
  `defunct` char(1) NOT NULL DEFAULT 'N' COMMENT '已停用标记(N=显示 Y=隐藏)',
  `accepted` int(11) DEFAULT '0' COMMENT '通过次数',
  `submit` int(11) DEFAULT '0' COMMENT '提交次数',
  `solved` int(11) DEFAULT '0' COMMENT '通过人数',
  `coin` int(11) NOT NULL DEFAULT '1' COMMENT '题目的积分分值',
  `remote_oj` varchar(16) DEFAULT NULL COMMENT '远端OJ名称(如 POJ/Codeforces)',
  `remote_id` varchar(32) DEFAULT NULL COMMENT '远端OJ的原始题号',
  PRIMARY KEY (`problem_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `reply` (
  `rid` int(11) NOT NULL AUTO_INCREMENT COMMENT '回复ID',
  `author_id` varchar(48) NOT NULL COMMENT '回复作者',
  `time` datetime NOT NULL DEFAULT '2016-05-13 19:24:00' COMMENT '回复时间',
  `content` text NOT NULL COMMENT '回复内容',
  `topic_id` int(11) NOT NULL COMMENT '所属主题ID',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '状态(0=可见)',
  `ip` varchar(46) NOT NULL COMMENT '作者IP',
  PRIMARY KEY (`rid`),
  KEY `author_id` (`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `sim` (
  `s_id` int(11) NOT NULL COMMENT '被比对的提交ID',
  `sim_s_id` int(11) DEFAULT NULL COMMENT '疑似重复的提交ID',
  `sim` int(11) DEFAULT NULL COMMENT '相似度(0-100)',
  PRIMARY KEY (`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `solution` (
  `solution_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '提交ID',
  `problem_id` int(11) NOT NULL DEFAULT 0 COMMENT '题号',
  `user_id` char(48) NOT NULL COMMENT '提交者用户名',
  `nick` char(20) NOT NULL DEFAULT '' COMMENT '提交者昵称(快照)', 
  `time` int(11) NOT NULL DEFAULT 0 COMMENT '耗时(毫秒)',
  `memory` int(11) NOT NULL DEFAULT 0 COMMENT '占用内存(KB)',
  `in_date` datetime NOT NULL DEFAULT '2016-05-13 19:24:00' COMMENT '提交时间',
  `result` smallint(6) NOT NULL DEFAULT '0' COMMENT '判题结果(详见 OJ 常量, 4=AC)',
  `language` INT UNSIGNED NOT NULL DEFAULT '0' COMMENT '使用的语言',
  `ip` char(46) NOT NULL COMMENT '提交IP',
  `contest_id` int(11) DEFAULT 0 COMMENT '所属比赛ID(0=非比赛提交)',
  `valid` tinyint(4) NOT NULL DEFAULT '1' COMMENT '有效提交标记',
  `num` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '比赛内提交序号',
  `code_length` int(11) NOT NULL DEFAULT 0 COMMENT '代码字节长度',
  `judgetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '判题完成时间',
  `pass_rate` DECIMAL(4,3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '通过率(0.000-1.000)',
  `first_time` tinyint(1) not null default 0 COMMENT '一血标记(该用户该题首次AC)',
  `lint_error` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '静态检查错误数',
  `judger` CHAR(16) NOT NULL DEFAULT 'LOCAL' COMMENT '判题机标识',
  `remote_oj` char(16) not NULL DEFAULT '' COMMENT '远端OJ名称(跨OJ提交时记录来源)',
  `remote_id` char(32) not NULL DEFAULT '' COMMENT '远端提交ID',
  PRIMARY KEY (`solution_id`),
  KEY `uid` (`user_id`),
  KEY `pid` (`problem_id`),
  KEY `res` (`result`),
  KEY `cid` (`contest_id`),
  KEY `idx_uid_pid` (`user_id`,`problem_id`),
  KEY `idx_uid_pid_res` (`user_id`,`problem_id`,`result`),
  KEY `idx_contest_result` (`contest_id`,`result`),
  KEY `idx_contest_num` (`contest_id`,`num`,`result`),
  KEY `idx_contest_user_id` (`contest_id`,`user_id`,`solution_id`),
  KEY `idx_cid_result_num_sid` (`contest_id`,`result`,`num`,`solution_id`),
  KEY `fst` (`first_time`),
  KEY `idx_solution_in_date` (`in_date`)
) ENGINE=MyISAM AUTO_INCREMENT=1001 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `source_code` (
  `solution_id` int(11) NOT NULL COMMENT '提交ID',
  `source` text NOT NULL COMMENT '源代码',
  PRIMARY KEY (`solution_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS source_code_user like source_code;

CREATE TABLE IF NOT EXISTS `topic` (
  `tid` int(11) NOT NULL AUTO_INCREMENT COMMENT '主题ID',
  `title` varbinary(60) NOT NULL COMMENT '主题标题',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '状态(0=可见 1=隐藏 ...)',
  `top_level` int(2) NOT NULL DEFAULT '0' COMMENT '置顶级别(0=不置顶)',
  `cid` int(11) DEFAULT NULL COMMENT '所属比赛ID',
  `pid` int(11) NOT NULL COMMENT '所属题目ID',
  `author_id` varchar(48) NOT NULL COMMENT '主题作者',
  PRIMARY KEY (`tid`),
  KEY `cid` (`cid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` varchar(48) NOT NULL DEFAULT '' COMMENT '用户名',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `submit` int(11) DEFAULT '0' COMMENT '提交总数',
  `solved` int(11) DEFAULT '0' COMMENT '通过总数',
  `defunct` char(1) NOT NULL DEFAULT 'N' COMMENT '已封禁标记(N=正常 Y=封禁)',
  `ip` varchar(46) NOT NULL DEFAULT '' COMMENT '注册IP',
  `accesstime` datetime DEFAULT NULL COMMENT '最后访问时间',
  `volume` int(11) NOT NULL DEFAULT '1' COMMENT '默认题册',
  `language` int(11) NOT NULL DEFAULT '1' COMMENT '默认语言',
  `password` varchar(32) DEFAULT NULL COMMENT '密码哈希',
  `reg_time` datetime DEFAULT NULL COMMENT '注册时间',
  `expiry_date` date NOT NULL DEFAULT '2099-01-01' COMMENT '账号到期日期',	
  `nick` varchar(20) NOT NULL DEFAULT '' COMMENT '昵称',
  `school` varchar(20) NOT NULL DEFAULT '' COMMENT '学校',
  `parent_phone` varchar(20) NOT NULL DEFAULT '' COMMENT '家长手机号',
  `group_name` varchar(16) NOT NULL DEFAULT '' COMMENT '班级',
  `activecode` varchar(16) NOT NULL DEFAULT '' COMMENT '激活码',
  `starred` int(11) NOT NULL DEFAULT '0' COMMENT '收藏题目数',
  `coin_earned` int(11) NOT NULL DEFAULT '0' COMMENT '做题获得的累计积分',
  `coin_bonus` int(11) NOT NULL DEFAULT '0' COMMENT '老师奖励的累计积分',
  `coin_spent` int(11) NOT NULL DEFAULT '0' COMMENT '已消耗的累计积分',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `online` (
  `hash` varchar(32) NOT NULL COMMENT '会话哈希(主键)',
  `ip` varchar(46)  NOT NULL default '' COMMENT '访客IP',
  `ua` varchar(255)  NOT NULL default '' COMMENT '浏览器User-Agent',
  `refer` varchar(4096)  default NULL COMMENT '来源URL',
  `lastmove` int(10) NOT NULL COMMENT '最近活动时间(Unix秒)',
  `firsttime` int(10) default NULL COMMENT '首次访问时间(Unix秒)',
  `uri` varchar(255) default NULL COMMENT '请求URI',
  PRIMARY KEY  (`hash`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4 ;

CREATE TABLE IF NOT EXISTS `runtimeinfo` (
  `solution_id` int(11) NOT NULL DEFAULT 0 COMMENT '提交ID',
  `error` text COMMENT '运行时错误信息',
  PRIMARY KEY (`solution_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `custominput` (
  `solution_id` int(11) NOT NULL DEFAULT 0 COMMENT '提交ID',
  `input_text` text COMMENT '自定义输入数据',
  PRIMARY KEY (`solution_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `printer` (
  `printer_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '打印任务ID',
  `user_id` char(48) NOT NULL COMMENT '申请人',
  `in_date` datetime NOT NULL DEFAULT '2018-03-13 19:38:00' COMMENT '申请时间',
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT '状态',
  `worktime` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '完成时间',
  `printer` CHAR(16) NOT NULL DEFAULT 'LOCAL' COMMENT '打印机名称',
  `content` text NOT NULL COMMENT '打印内容' ,
  PRIMARY KEY (`printer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `balloon` (
  `balloon_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '气球ID',
  `user_id` char(48) NOT NULL COMMENT '收气球的用户',
  `sid` int(11) NOT NULL COMMENT '提交ID' ,
  `cid` int(11) NOT NULL COMMENT '比赛ID' ,
  `pid` int(11) NOT NULL COMMENT '题号' ,
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT '状态(0=待发 1=已发 ...)',
  PRIMARY KEY (`balloon_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `share_code` (
  `share_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分享ID',
  `user_id` varchar(48)  DEFAULT NULL COMMENT '分享者',
  `title` varchar(32) DEFAULT NULL COMMENT '分享标题',
  `share_code` text DEFAULT NULL COMMENT '分享的代码',
  `language` varchar(32) DEFAULT NULL COMMENT '代码语言',
  `share_time` datetime DEFAULT NULL COMMENT '分享时间',
  PRIMARY KEY (`share_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS solution_ai_answer ( solution_id int not null default 0 COMMENT '提交ID', answer mediumtext COMMENT 'AI生成的题解' ,primary key (solution_id)) charset utf8mb4;
CREATE TABLE `openai_task_queue` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `user_id` varchar(40) NOT NULL DEFAULT '' COMMENT '提交任务的用户',
  `task_type` varchar(24) NOT NULL DEFAULT '' COMMENT '任务类型(如 ai_hint/ai_analyze)',
  `solution_id` bigint DEFAULT '0' COMMENT '关联提交ID',
  `problem_id` bigint not null default 0 COMMENT '关联题号',
  `request_body` mediumtext NOT NULL COMMENT '请求参数(JSON格式字符串)',
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '状态：0:待处理, 1:处理中, 2:已完成, 3:失败',
  `response_body` mediumtext COMMENT '返回结果',
  `error_message` text COMMENT '错误信息',
  `create_date` datetime NOT NULL COMMENT '创建时间',
  `update_date` datetime NOT NULL COMMENT '最后更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_status_create` (`status`,`create_date`),
  KEY `idx_user_status` (`user_id`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='异步任务队列-MyISAM版';

delimiter //

drop trigger if exists firstAC//
UPDATE solution s JOIN (SELECT user_id,problem_id, MIN(solution_id) AS first_solution_id FROM solution WHERE result = 4 GROUP BY user_id, problem_id ) t ON s.solution_id = t.first_solution_id SET s.first_time = 1 //
create trigger firstAC
before update on solution
for each row
begin
 declare acTimes int;
 declare acCoin int;
 if new.result=4 then
    select count(1) from solution where problem_id=new.problem_id and result=4 and first_time=1 and user_id=new.user_id into acTimes;
    select ifnull(coin, 1) from problem where  problem_id=new.problem_id into acCoin;
    if acTimes=0 then
        set new.first_time=1;
        if old.first_time=0 then
            update users 
                set coin_earned = coin_earned + acCoin 
                where user_id = new.user_id;
        end if;
    end if;
end if;
end;//

	
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
