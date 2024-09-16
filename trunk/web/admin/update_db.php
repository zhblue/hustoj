<?php require("admin-header.php");

if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}?>
<?php $tsql=Array();
$csql=Array();
$tsql[0]="use ".$DB_NAME.";";
$csql[0]="
CREATE TABLE $DB_NAME.`topic` (
  `tid` int(11) NOT NULL auto_increment,
  `title` varbinary(60) NOT NULL,
  `status` int(2) NOT NULL default '0',
  `top_level` int(2) NOT NULL default '0',
  `cid` int(11) default NULL,
  `pid` int(11) NOT NULL,
  `author_id` varchar(20) NOT NULL,
  PRIMARY KEY  (`tid`),
  KEY `cid` (`cid`,`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
";
$tsql[1]="select 1 from $DB_NAME.reply limit 1";
$csql[1]="
CREATE TABLE $DB_NAME.`reply` (
 `rid` int(11) NOT NULL auto_increment,
 `author_id` varchar(20) NOT NULL,
 `time` datetime NOT NULL default '2000-01-01 00:00:01',
 `content` text NOT NULL,
 `topic_id` int(11) NOT NULL,
 `status` int(2) NOT NULL default '0',
 `ip` varchar(30) NOT NULL,
 PRIMARY KEY  (`rid`),
 KEY `author_id` (`author_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;
";
$tsql[2]="
ALTER TABLE $DB_NAME.`problem` DROP COLUMN `sample_Program`,
 DROP COLUMN `ratio`,
 DROP COLUMN `error`,
 DROP COLUMN `difficulty`,
 DROP COLUMN `submit_user`,
 DROP COLUMN `case_time_limit`;
 ";
$csql[2]="";
$tsql[3]="select 1 from $DB_NAME.sim limit 1";
$csql[3]="
CREATE TABLE $DB_NAME.`sim` (
  `s_id` int(11) NOT NULL,
  `sim_s_id` int(11) NULL,
  `sim` int(11) NULL,
  PRIMARY KEY  (`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";

$tsql[4]="select 1 from $DB_NAME.mail limit 1";
$csql[4]="
CREATE TABLE $DB_NAME.`mail` (

  `mail_id` int(11) NOT NULL auto_increment,
  `to_user` varchar(20) NOT NULL default '',
  `from_user` varchar(20) NOT NULL default '',
  `title` varchar(200) NOT NULL default '',
  `content` text,
  `new_mail` tinyint(1) NOT NULL default '1',
  `reply` tinyint(4) default '0',
  `in_date` datetime default NULL,
  `defunct` char(1) NOT NULL default 'N',
  PRIMARY KEY  (`mail_id`),
  KEY `uid` (`to_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1000 ;";   
 
$tsql[5]="ALTER TABLE $DB_NAME.`solution` MODIFY COLUMN `pass_rate` DECIMAL(4,3) UNSIGNED NOT NULL DEFAULT 0,MODIFY COLUMN in_date datetime not null default '2009-06-13 19:00:00', MODIFY COLUMN `user_id` CHAR(48)  CHARACTER SET utf8mb4 NOT NULL,MODIFY COLUMN `ip` CHAR(15)  CHARACTER SET utf8mb4 NOT NULL;";
$csql[5]="";

$tsql[6]="select langmask from $DB_NAME.contest limit 1;";
$csql[6]="ALTER TABLE $DB_NAME.`contest` ADD COLUMN `langmask` TINYINT  NOT NULL DEFAULT 0 COMMENT 'bits for LANG to mask' AFTER `private`;";

$tsql[7]="repair table $DB_NAME.`compileinfo`,$DB_NAME.`contest` ,$DB_NAME.`contest_problem` ,$DB_NAME.`loginlog`,$DB_NAME.`news`,$DB_NAME.`privilege`,$DB_NAME.`problem` ,$DB_NAME.`solution`,$DB_NAME.`source_code`,$DB_NAME.`users`,$DB_NAME.`topic`,$DB_NAME.`reply`,$DB_NAME.`sim`,$DB_NAME.`mail`;";
$csql[7]="optimize table $DB_NAME.`compileinfo`,$DB_NAME.`contest` ,$DB_NAME.`contest_problem` ,$DB_NAME.`loginlog`,$DB_NAME.`news`,$DB_NAME.`privilege`,$DB_NAME.`problem` ,$DB_NAME.`solution`,$DB_NAME.`source_code`,$DB_NAME.`users`,$DB_NAME.`topic`,$DB_NAME.`reply`,$DB_NAME.`sim`,$DB_NAME.`mail`;";

$csql[8]="";
$tsql[8]="ALTER TABLE $DB_NAME.`contest` MODIFY COLUMN `langmask` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'bits for LANG to mask';";
$tsql[9]="select 1 from $DB_NAME.runtimeinfo limit 1";
$csql[9]="CREATE TABLE $DB_NAME. `runtimeinfo` (
  `solution_id` int(11) NOT NULL DEFAULT '0',
  `error` text,
  PRIMARY KEY (`solution_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
";
$tsql[10]="select pass_rate from $DB_NAME.solution limit 1";
$csql[10]="ALTER TABLE $DB_NAME.`solution` ADD COLUMN `pass_rate` DECIMAL(4,3) UNSIGNED NOT NULL DEFAULT 0 AFTER `judgetime`;";

$csql[11]="";
$tsql[11]="ALTER TABLE $DB_NAME.`users` MODIFY COLUMN `user_id` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id';";

$csql[12]="";
$tsql[12]="ALTER TABLE $DB_NAME.`topic` MODIFY COLUMN `author_id` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id';";

$csql[13]="";
$tsql[13]="ALTER TABLE $DB_NAME.`mail` MODIFY COLUMN `to_user` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id',MODIFY COLUMN `from_user` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id';";

$csql[14]="";
$tsql[14]="ALTER TABLE $DB_NAME.`reply` MODIFY COLUMN `author_id` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id';";

$csql[15]="";
$tsql[15]="ALTER TABLE $DB_NAME.`privilege` MODIFY COLUMN `user_id` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id';";

$csql[15]="";
$tsql[15]="ALTER TABLE $DB_NAME.`loginlog` MODIFY COLUMN `user_id` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id';";

$csql[15]="";
$tsql[15]="ALTER TABLE $DB_NAME.`news` MODIFY COLUMN `user_id` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id';";
$tsql[16]="ALTER TABLE $DB_NAME.`sim` ADD INDEX `Index_sim_id`(`sim_s_id`);";
$csql[16]="";
$tsql[17]="ALTER TABLE $DB_NAME.`contest_problem` ADD INDEX `Index_contest_id`(`contest_id`);";
$csql[17]="";
$tsql[18]="ALTER TABLE $DB_NAME.`contest_problem` ADD INDEX `Index_problem_id`(`problem_id`);";
$csql[18]="";

$tsql[18]="select 1 from $DB_NAME.custominput limit 1;";
$csql[18]="CREATE TABLE $DB_NAME. `custominput` (  `solution_id` int(11) NOT NULL DEFAULT '0',  `input_text` text,  PRIMARY KEY (`solution_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";

$tsql[19]="ALTER TABLE $DB_NAME.`loginlog` ADD INDEX `user_time_index`(`user_id`, `time`);";
$csql[19]="";
$tsql[20]="ALTER TABLE $DB_NAME.`contest` ADD `password` CHAR( 16 ) NOT NULL DEFAULT '' AFTER `langmask` ";
$csql[20]="";
$tsql[21]="CREATE TABLE $DB_NAME.`source_code_user` like source_code";
$csql[21]="";

$tsql[22]="insert into $DB_NAME.source_code_user select * from $DB_NAME.source_code where solution_id not in (select solution_id from $DB_NAME.source_code_user)  ";
$csql[22]="";

$tsql[23]="select judger from $DB_NAME.solution limit 1 ";
$csql[23]="ALTER TABLE $DB_NAME.`solution` ADD `judger` CHAR(16) NOT NULL DEFAULT 'LOCAL' ;  ";

$tsql[24]="alter table $DB_NAME.solution modify column pass_rate decimal(4,3) NOT NULL DEFAULT 0;";
$csql[24]="";

$csql[25]="";
$tsql[25]="ALTER TABLE $DB_NAME. `solution` CHANGE  `ip`  `ip` CHAR( 46 ) CHARACTER SET utf8mb4 NOT NULL DEFAULT  '';";
$csql[26]="select 1 from $DB_NAME.printer limit 1";
$tsql[26]="CREATE TABLE $DB_NAME. `printer` (
  `printer_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` char(48) NOT NULL,
  `in_date` datetime NOT NULL DEFAULT '2018-03-13 19:38:00',
  `status` smallint(6) NOT NULL DEFAULT '0',
  `worktime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `printer` CHAR(16) NOT NULL DEFAULT 'LOCAL',
  `content` text NOT NULL ,
  PRIMARY KEY (`printer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;";
$csql[27]="select 1 from $DB_NAME.balloon limit 1";
$tsql[27]="CREATE TABLE $DB_NAME. `balloon` (
  `balloon_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` char(48) NOT NULL,
  `sid` int(11) NOT NULL ,
  `cid` int(11) NOT NULL ,
  `pid` int(11) NOT NULL ,
  `status` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`balloon_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;";
$csql[28]="select 1 from $DB_NAME.share_code limit 1";
$tsql[28]="CREATE TABLE $DB_NAME.`share_code` (
  `share_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(48) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `share_code` text ,
  `language` varchar(32) DEFAULT NULL,
  `share_time` datetime DEFAULT NULL,
  PRIMARY KEY (`share_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb4;";
$tsql[29]="alter TABLE $DB_NAME.`contest` ADD `user_id` CHAR( 48 ) NOT NULL DEFAULT 'admin' AFTER `password` ";
$csql[29]="update $DB_NAME.contest c inner JOIN (SELECT * FROM privilege WHERE rightstr LIKE 'm%') p ON concat('m',contest_id)=rightstr set c.user_id=p.user_id";
$tsql[30]="alter TABLE $DB_NAME. `contest_problem` ADD  `c_accepted` INT NOT NULL DEFAULT  '0' AFTER  `num` ,ADD  `c_submit` INT NOT NULL DEFAULT  '0' AFTER  `c_accepted`";
$csql[30]="";
$tsql[31]="update $DB_NAME.contest_problem cp inner join (select count(1) submit,contest_id cid,num from $DB_NAME.solution where contest_id>0 group by contest_id,num) sb on cp.contest_id=sb.cid and cp.num=sb.num set cp.c_submit=sb.submit";
$csql[31]="update $DB_NAME.contest_problem cp inner join (select count(1) ac,contest_id cid,num from $DB_NAME.solution where contest_id>0 and result=4 group by contest_id,num) sb on cp.contest_id=sb.cid and cp.num=sb.num set cp.c_accepted=sb.ac";
$tsql[32]="alter table $DB_NAME.solution add column nick char(20) not null default '' after user_id ";
$csql[32]="update $DB_NAME.solution s inner join users u on s.user_id=u.user_id set s.nick=u.nick";
$tsql[33]="update $DB_NAME.problem p inner join (select problem_id pid ,count(1) submit from $DB_NAME.solution group by problem_id) s on p.problem_id=s.pid set p.submit=s.submit;";
$csql[33]="update $DB_NAME.problem p inner JOIN ( SELECT count(1) as c,problem_id from solution where result=4 group by problem_id) s on p.problem_id=s.problem_id set p.accepted=s.c;";
$tsql[34]="alter table $DB_NAME.privilege add index user_id_index(user_id);";
$csql[34]="";
$tsql[35]="ALTER TABLE $DB_NAME.`problem` CHANGE `time_limit` `time_limit` DECIMAL(10,3) NOT NULL DEFAULT '0';";
$csql[35]="";
$tsql[36]="alter table $DB_NAME.privilege add column valuestr char(11) not null default 'true' after rightstr; ";
$csql[36]="";

$tsql[37]="alter table $DB_NAME.news modify column `time` datetime NOT NULL DEFAULT '2016-05-13 19:24:00';";
$csql[37]="ALTER TABLE $DB_NAME.`news` ADD COLUMN `menu` int(11) NOT NULL DEFAULT 0 AFTER `importance`;";

$tsql[38]="delete from $DB_NAME.source_code where solution_id in (select solution_id from $DB_NAME.solution where problem_id=0 and result>4);";
$csql[38]="";
$tsql[39]="delete from $DB_NAME.source_code_user where solution_id in (select solution_id from $DB_NAME.solution where problem_id=0 and result>4);";
$csql[39]="";
$tsql[40]="delete from $DB_NAME.runtimeinfo where solution_id in (select solution_id from $DB_NAME.solution where problem_id=0 and result>4);";
$csql[40]="delete from $DB_NAME.runtimeinfo where solution_id not in (select solution_id from  $DB_NAME.solution);";
$tsql[41]="delete from $DB_NAME.compileinfo where solution_id in (select solution_id from $DB_NAME.solution where problem_id=0 and result>4);";
$csql[41]="delete from $DB_NAME.compileinfo where solution_id not in (select solution_id from  $DB_NAME.solution);";
$tsql[42]="";
$csql[42]="delete from $DB_NAME.solution where problem_id=0 and result>4;";
$tsql[43]="alter table $DB_NAME.problem add column remote_oj varchar(16) default NULL after solved;";
$csql[43]="alter table $DB_NAME.problem add column remote_id varchar(16) default NULL after remote_oj;";
$tsql[44]="alter table $DB_NAME.solution add column remote_oj char(16) not null default '' after judger;";
$csql[44]="alter table $DB_NAME.solution add column remote_id char(16) not null default '' after remote_oj;";
$tsql[45]="alter table $DB_NAME.news modify content mediumtext not null;";
$csql[45]="alter table $DB_NAME.problem modify description mediumtext not null, modify input  mediumtext not null, modify output mediumtext not null, modify hint mediumtext not null;";
$tsql[46]="alter table $DB_NAME.users add column activecode varchar(16) not null default '' after school;";
$csql[46]="";
$tsql[47]="alter table $DB_NAME.users add column group_name varchar(16) not null default '' after school;";
$csql[47]="";
$tsql[48]="alter table $DB_NAME.loginlog add column log_id int not null auto_increment primary key first;";
$csql[48]="delete from $DB_NAME.loginlog where time<curdate()-interval 6 month;";

$tsql[49]="alter table $DB_NAME.problem add index key_p_def(defunct);";
$csql[49]="alter table $DB_NAME.contest add index key_c_def(defunct);";
$tsql[50]="alter table $DB_NAME.contest add index key_c_end(end_time);";
$csql[50]="alter table $DB_NAME.contest add index key_c_dend(defunct,end_time);";
$tsql[51]="alter table $DB_NAME.users add column starred int default 0 after activecode ;";
$csql[51]="";
// 删除6个月以前的非正确源码，优化数据库空间。
// delete from source_code  where solution_id in (select solution_id from solution where result>4 and  in_date<date_sub(now(),interval 6 month) ); //
if(isset($_POST['do'])){
	require_once("../include/check_post_key.php");
	echo "Executing...<br>";
	set_time_limit(60);
	for($i=0;isset($tsql[$i]);$i++){
		if($tsql[$i]){
			echo $tsql[$i]."<br>";
			echo "=".pdo_query($tsql[$i])."<hr>";
		}
		if($csql[$i]){
			echo $csql[$i]."<br>";
			echo "=".pdo_query($csql[$i])."<hr>";
		}
	}
}
?>
<div class="container">
<b>Update DataBase</b>
	Create New Tables ,drop useless columes.
	<b>Necessary for using plagiarism detection.</b>
	<form action='update_db.php' method=post>
		<?php require_once("../include/set_post_key.php");?>
		<input type='hidden' name='do' value='do'>
		<input type=submit value=Update>
	</form>
	
<?php if (file_exists("update_pw.php")) {	?>
   <a href="update_pw.php">Upgrade all users password storage form to get more security.</a>
   * only do once !
<?php }?>
</div>
