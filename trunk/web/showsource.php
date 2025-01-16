<?php
 $cache_time=10;
	$OJ_CACHE_SHARE=false;
	require_once('./include/cache_start.php');
        require_once('./include/db_info.inc.php');
	require_once('./include/setlang.php');
        require_once("./include/const.inc.php");
        require_once("./include/my_func.inc.php");
	$view_title= "Source Code";
if (!isset($_GET['id'])){
	$view_errors= "No such code!\n";
	require("template/".$OJ_TEMPLATE."/error.php");
	exit(0);
}
$ok=false;
$id=intval($_GET['id']);
$sql="SELECT * FROM `solution` WHERE `solution_id`=?";
$result=pdo_query($sql,$id);
$row=$result[0];
$slanguage=$row['language'];
$sresult=$row['result'];
$stime=$row['time'];
$owner=$row['user_id'];
$nick=$row['nick'];
$smemory=$row['memory'];
$sproblem_id=$row['problem_id'];
$view_user_id=$suser_id=$row['user_id'];
$contest_id=intval($row['contest_id']);
$need_check_using=true;
if(!(isset($_SESSION[$OJ_NAME."_source_browser"])||isset($_SESSION[$OJ_NAME."_administrator"]))){
	if ( $contest_id > 0 ){
		$sql="select start_time,end_time from contest where contest_id=?";
		$result=pdo_query($sql,$contest_id);
		if($result){
			$row=$result[0];
			$start_time = strtotime($row['start_time']);
			$end_time = strtotime($row['end_time']);
			$now=time();
			if( $end_time < $now ){ // 当前提交，属于已经结束的比赛，考察是否有进行中的比赛在使用。
				//echo $now."-".$end_time;
				$need_check_using=true;
				
			}else{			// 属于进行中的比赛，可以看
						
				$need_check_using=false;
			
			}
		}

	}else{ //非比赛提交.考察是否有进行中的比赛在使用

				$need_check_using=true;
	}
	// 检查是否使用中
	if($need_check_using){
		//$sql="select contest_id from contest where contest_id in (select contest_id from contest_problem where problem_id=?) 
		//							and start_time < '$now' and end_time > '$now' ";   // and title like '%$OJ_NOIP_KEYWORD%' 
		//echo $sql;
		//$result=pdo_query($sql,$sproblem_id);
		$lockid=problem_locked($sproblem_id);
		if($lockid){
				$view_errors = "<center>";
				$view_errors .= "<h3>$MSG_CONTEST_ID : ".$result[0][0]."</h3>";
				$view_errors .= "<p> $MSG_SOURCE_NOT_ALLOWED_FOR_EXAM </p>";
				$view_errors .= "<br>";
				$view_errors .= "</center>";
				$view_errors .= "<br><br>";
				require("template/".$OJ_TEMPLATE."/error.php");
				exit(0);
		}

	}
}


if(isset($OJ_EXAM_CONTEST_ID)){
	if($contest_id<$OJ_EXAM_CONTEST_ID&&!isset($_SESSION[$OJ_NAME.'_'.'source_browser'])){
					$view_errors = "<center>";
					$view_errors .= "<h3>$MSG_CONTEST_ID : ".$OJ_EXAM_CONTEST_ID."+ </h3>";
					$view_errors .= "<p> $MSG_SOURCE_NOT_ALLOWED_FOR_EXAM </p>";
					$view_errors .= "<br>";
					$view_errors .= "</center>";
					$view_errors .= "<br><br>";
					require("template/".$OJ_TEMPLATE."/error.php");
					exit(0);
	}
}

if (isset($OJ_AUTO_SHARE)&&$OJ_AUTO_SHARE&&isset($_SESSION[$OJ_NAME.'_'.'user_id'])){
	$sql="SELECT 1 FROM solution where 
			result=4 and problem_id=$sproblem_id and user_id=?";
	$rrs=pdo_query($sql,$_SESSION[$OJ_NAME.'_'.'user_id']);
	if(count($rrs)>0) $ok=true;
}

//check whether user has the right of view solutions of this problem
//echo "checking...";
if(isset($_SESSION[$OJ_NAME.'_'.'s'.$sproblem_id])){
	$ok=true;
//	echo "Yes";
}else{
	$sql="select count(1) from privilege where user_id=? and rightstr=?";
	$count=pdo_query($sql,$_SESSION[$OJ_NAME.'_'.'user_id'],"s".$sproblem_id);
	if($count&&$count[0][0]>0){
		$_SESSION[$OJ_NAME.'_'.'s'.$sproblem_id]=true;
		$ok=true;
	}else{
		//echo "not right";
	}

}
$view_source="No source code available!";
if (isset($_SESSION[$OJ_NAME.'_'.'user_id'])&&$row && $owner ==$_SESSION[$OJ_NAME.'_'.'user_id']) $ok=true;
if (isset($_SESSION[$OJ_NAME.'_'.'source_browser'])) $ok=true;

		$sql="SELECT `source` FROM `source_code_user` WHERE `solution_id`=?";
		$result=pdo_query($sql,$id);
		 $row=$result[0];
		if($row)
			$view_source=$row['source'];

/////////////////////////Template
require("template/".$OJ_TEMPLATE."/showsource.php");
/////////////////////////Common foot
if(file_exists('./include/cache_end.php'))
	require_once('./include/cache_end.php');
?>

