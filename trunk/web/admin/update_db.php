<?php require("admin-header.php");

if (!(isset($_SESSION['administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}?>
<?php $tsql=Array();
$csql=Array();
$tsql[0]="select 1 from topic limit 1";
$csql[0]="
CREATE TABLE `topic` (
  `tid` int(11) NOT NULL auto_increment,
  `title` varbinary(60) NOT NULL,
  `status` int(2) NOT NULL default '0',
  `top_level` int(2) NOT NULL default '0',
  `cid` int(11) default NULL,
  `pid` int(11) NOT NULL,
  `author_id` varchar(20) NOT NULL,
  PRIMARY KEY  (`tid`),
  KEY `cid` (`cid`,`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";
$tsql[1]="select 1 from reply limit 1";
$csql[1]="
CREATE TABLE `reply` (
 `rid` int(11) NOT NULL auto_increment,
 `author_id` varchar(20) NOT NULL,
 `time` datetime NOT NULL default '0000-00-00 00:00:00',
 `content` text NOT NULL,
 `topic_id` int(11) NOT NULL,
 `status` int(2) NOT NULL default '0',
 `ip` varchar(30) NOT NULL,
 PRIMARY KEY  (`rid`),
 KEY `author_id` (`author_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";
$tsql[2]="
ALTER TABLE `problem` DROP COLUMN `sample_Program`,
 DROP COLUMN `ratio`,
 DROP COLUMN `error`,
 DROP COLUMN `difficulty`,
 DROP COLUMN `submit_user`,
 DROP COLUMN `case_time_limit`;
 ";
$csql[2]="";
$tsql[3]="select 1 from sim limit 1";
$csql[3]="
CREATE TABLE `sim` (
  `s_id` int(11) NOT NULL,
  `sim_s_id` int(11) NULL,
  `sim` int(11) NULL,
  PRIMARY KEY  (`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

$tsql[4]="select 1 from mail limit 1";
$csql[4]="
CREATE TABLE `mail` (

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000 ;";
$tsql[5]="ALTER TABLE `solution` MODIFY COLUMN `user_id` CHAR(48)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,	DROP COLUMN `className`,MODIFY COLUMN `ip` CHAR(15)  CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;";
$csql[5]="";

$tsql[6]="select langmask from contest limit 1;";
$csql[6]="ALTER TABLE `contest` ADD COLUMN `langmask` TINYINT  NOT NULL DEFAULT 0 COMMENT 'bits for LANG to mask' AFTER `private`;";

$tsql[7]="whatever;";
$csql[7]="optimize table `compileinfo`,`contest` ,`contest_problem` ,`loginlog`,`news`,`privilege`,`problem` ,`solution`,`source_code`,`users`,`topic`,`reply`,`online`,`sim`,`mail`;";

$tsql[8]="whatever;";
$csql[8]="ALTER TABLE `contest` MODIFY COLUMN `langmask` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'bits for LANG to mask';";
$tsql[9]="select 1 from runtimeinfo limit 1";
$csql[9]="CREATE TABLE  `runtimeinfo` (
  `solution_id` int(11) NOT NULL DEFAULT '0',
  `error` text,
  PRIMARY KEY (`solution_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
$tsql[10]="select pass_rate from solution";
$csql[10]="ALTER TABLE `solution` ADD COLUMN `pass_rate` DECIMAL(2,2) UNSIGNED NOT NULL DEFAULT 0 AFTER `judgetime`;";

$tsql[11]="whatever;";
$csql[11]="ALTER TABLE `users` MODIFY COLUMN `user_id` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id';";

$tsql[12]="whatever;";
$csql[12]="ALTER TABLE `topic` MODIFY COLUMN `author_id` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id';";

$tsql[13]="whatever;";
$csql[13]="ALTER TABLE `mail` MODIFY COLUMN `to_user` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id',MODIFY COLUMN `from_user` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id';";

$tsql[14]="whatever;";
$csql[14]="ALTER TABLE `reply` MODIFY COLUMN `author_id` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id';";

$tsql[15]="whatever;";
$csql[15]="ALTER TABLE `privilege` MODIFY COLUMN `user_id` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id';";

$tsql[15]="whatever;";
$csql[15]="ALTER TABLE `loginlog` MODIFY COLUMN `user_id` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id';";

$tsql[15]="whatever;";
$csql[15]="ALTER TABLE `news` MODIFY COLUMN `user_id` varchar(48) NOT NULL DEFAULT ''  COMMENT 'user_id';";
$tsql[16]="ALTER TABLE `sim` ADD INDEX `Index_sim_id`(`sim_s_id`);";
$csql[16]="";
$tsql[17]="ALTER TABLE `contest_problem` ADD INDEX `Index_contest_id`(`contest_id`);";
$csql[17]="";
$tsql[18]="ALTER TABLE `contest_problem` ADD INDEX `Index_problem_id`(`problem_id`);";
$csql[18]="";

$tsql[18]="select 1 from custominput limit 1;";
$csql[18]="CREATE TABLE  `custominput` (  `solution_id` int(11) NOT NULL DEFAULT '0',  `input_text` text,  PRIMARY KEY (`solution_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

if(isset($_POST['do'])){
	require_once("../include/check_post_key.php");
	echo "Executing...<br>";
	for($i=0;isset($tsql[$i]);$i++){
		if(!$res=mysql_query($tsql[$i])){
				echo $csql[$i]."<br>";
				mysql_query($csql[$i]);
		}else{
				echo mysql_error()."<br>";
		}
		
	}
}
?>


<html>
  <head>
    <title>OJ Administration</title>
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Content-Language" content="zh-cn">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel=stylesheet href='admin.css' type='text/css'>
  </head>
<body>



<div class="container-fluid">
  <?php require_once("admin-bar.php"); ?>
  <div class="row-fluid top-space">
    <div class="span2" >
      <div class="menu-group"  >
        <?php require_once("menu.php") ?>
      </div>
    </div>
    <div class="span10">
      <div class="database col align-center">
        <div class="news-header align-center">Update DataBase<hr/></div>
          <div style="font-size:16px;">Create New Tables ,drop useless columes.</div>
          <div style="font-size:16px;"><b>Necessary for using plagiarism detection.</b></div>

          <form action='update_db.php' method=post>
            <?php require_once("../include/set_post_key.php");?>
            <input type='hidden' name='do' value='do'>
            <input class="btn" type=submit value=Update>
          </form>
          
        <?php if (file_exists("update_pw.php")) { ?>
           <a href="update_pw.php">Upgrade all users password storage form to get more security.</a>
           * only do once !
        <?php }?>
      </div>
    </div>
  </div>
</div>

</body>
</html>
