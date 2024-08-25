<?php
 $cache_time=120; 
 $OJ_CACHE_SHARE=true;
	require_once('./include/cache_start.php');
        require_once('./include/db_info.inc.php');
	require_once('./include/setlang.php');
	require_once("./include/const.inc.php");
	require_once("./include/my_func.inc.php");
	require_once("include/memcache.php");
$now =  date('Y-m-d H:i', time());
	if(isset($OJ_OI_MODE)&&$OJ_OI_MODE&&!isset($_SESSION[$OJ_NAME."_administrator"])){
               
                $sql="select count(contest_id) from contest where start_time<'$now' and end_time>'$now' and title like '%$OJ_NOIP_KEYWORD%'";
                $row=pdo_query($sql);
                $cols=$row[0];
                //echo $sql;
                //echo $cols[0];
                if($cols[0]>0) {
                      $view_errors =  "<h2> $MSG_NOIP_WARNING </h2>";
                      require("template/".$OJ_TEMPLATE."/error.php");
                      exit(0);
                }
        }

 // check user
$user=$_GET['user'];
if (!is_valid_user_name($user)){
	echo "No such User!";
	exit(0);
}

//检查用户当前是否在参加NOIP模式比赛，如果是则不显示用户信息以防看到提交结果 2020.7.25

$sql = "select 1 from `solution` where  `user_id`=? and  problem_id>0 and `contest_id` IN (select `contest_id` from `contest` where `start_time` < ? and `end_time` > ? and `title` like ?)";
$rrs = pdo_query($sql, $user ,$now , $now , "%$OJ_NOIP_KEYWORD%");
$flag = count($rrs) > 0 ;
if($flag&&!isset($_SESSION[$OJ_NAME.'_'.'administrator'])) // administrator need to view userinfo 
{	
	$view_errors =  "<h2> $MSG_NOIP_WARNING </h2>";
	require("template/".$OJ_TEMPLATE."/error.php");
	exit(0);
}

$view_title=$user ."@".$OJ_NAME;
if(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || (isset($_SESSION[$OJ_NAME.'_'.'user_id']) && $_SESSION[$OJ_NAME.'_'.'user_id']==$user) )
    $sql="SELECT `school`,`email`,`nick` FROM `users` WHERE `user_id`=? ";
else 
    $sql="SELECT `school`,`email`,`nick` FROM `users` WHERE `user_id`=? and user_id not in ($OJ_RANK_HIDDEN) ";
$result=pdo_query($sql,$user);
$row_cnt=count($result);
if ($row_cnt==0){ 
	$view_errors= "No such User!";
	require("template/".$OJ_TEMPLATE."/error.php");
	exit(0);
}

 $row=$result[0];
$school=$row['school'];
$email=$row['email'];
$nick=$row['nick'];

// count solved
$sql="SELECT count(DISTINCT problem_id) as `ac` FROM `solution` WHERE `user_id`=? AND `result`=4";
$result=pdo_query($sql,$user) ;
 $row=$result[0];
$AC=$row['ac'];

// count submission
$sql="SELECT count(solution_id) as `Submit` FROM `solution` WHERE `user_id`=? and  problem_id>0";
$result=pdo_query($sql,$user) ;
 $row=$result[0];
$Submit=$row['Submit'];

// update solved 
$sql="UPDATE `users` SET `solved`='".strval($AC)."',`submit`='".strval($Submit)."' WHERE `user_id`=?";
$result=pdo_query($sql,$user);
$sql="SELECT count(*) as `Rank` FROM `users` WHERE `solved`>? and defunct='N' and user_id not in (".$OJ_RANK_HIDDEN.") ";
$result=pdo_query($sql,$AC);
$row=$result[0];
$Rank=intval($row[0])+1;

if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])){
	$sql="SELECT user_id,password,ip,`time` FROM `loginlog` WHERE `user_id`=? order by `time` desc LIMIT 0,10";
	$view_userinfo=pdo_query($sql,$user) ;
//echo "</table>";

}
$sql="SELECT result,count(1) FROM solution WHERE `user_id`=? AND result>=4 group by result order by result";
	$result=pdo_query($sql,$user);
	$view_userstat=array();
	$i=0;
	 foreach($result as $row){
		$view_userstat[$i++]=$row;
	}
	

$sql=	"SELECT UNIX_TIMESTAMP(date(in_date))*1000 md,count(1) c FROM `solution` where  `user_id`=?  group by md order by md desc ";
	$result=pdo_query($sql,$user);//mysql_escape_string($sql));
	$chart_data_all= array();
//echo $sql;
    
	 foreach($result as $row){
		$chart_data_all[$row['md']]=$row['c'];
    }
    
$sql=	"SELECT UNIX_TIMESTAMP(date(in_date))*1000 md,count(1) c FROM `solution` where  `user_id`=? and result=4 group by md order by md desc ";
	$result=pdo_query($sql,$user);//mysql_escape_string($sql));
	$chart_data_ac= array();
//echo $sql;
    
	 foreach($result as $row){
		$chart_data_ac[$row['md']]=$row['c'];
    }
  
$acc_arr=Array();
if (isset($_SESSION[$OJ_NAME.'_'.'user_id'])) {
        $sql = "SELECT distinct `problem_id` FROM `solution` WHERE `user_id`=? AND `result`=4 ";
        if(isset($pids)&&$pids!="") $sql.=" and problem_id in ($pids)";
        $result = mysql_query_cache($sql,$_SESSION[$OJ_NAME.'_'.'user_id']);
        foreach ($result as $row)
                $acc_arr[$row[0]] = true;
}

    
/////////////////////////Template
require("template/".$OJ_TEMPLATE."/userinfo.php");
/////////////////////////Common foot
if(file_exists('./include/cache_end.php'))
	require_once('./include/cache_end.php');
?>

