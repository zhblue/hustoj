<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

////////////////////////////Common head
$cache_time = 2;
$OJ_CACHE_SHARE = false;

require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = "$MSG_STATUS";

require_once("./include/const.inc.php");

$solution_id = 0;
// check the top arg

if (isset($_GET['solution_id'])) {
  $solution_id = intval($_GET['solution_id']);
}

$sql = "select * from solution where solution_id=? LIMIT 1";
$result = pdo_query($sql,$solution_id);
		
if (!empty($result)) {
	$row = $result[0];
	  if (isset($_GET['tr'])&&($row['problem_id']==0||($row['problem_id']>0&&$OJ_SHOW_DIFF)) && 
	      (isset($_SESSION[$OJ_NAME.'_'.'user_id'])&& $_SESSION[$OJ_NAME.'_'.'user_id']== $row['user_id'] ) ) {

		$res = $row['result'];
		
		if ($res==11) {
			$sql = "SELECT `error` FROM `compileinfo` WHERE `solution_id`=?";
		}
		else {
			$spj=pdo_query("select spj from problem where problem_id=?",$row['problem_id']);
			if(!empty($spj)&&$spj[0][0]==2 && $OJ_HIDE_RIGHT_ANSWER ){
					echo $MSG_WARNING_ACCESS_DENIED;
					exit();
			}

			$sql = "SELECT `error` FROM `runtimeinfo` WHERE `solution_id`=?";
		}

		$result = pdo_query($sql,$solution_id);
		$row = $result[0];
		
		if ($row) {
			if(strpos($row['error'],"judge")!==false) echo "error1";
                        else if(strpos($row['error'],"php")!==false) echo "error2";
                        else if(strpos($row['error'],"PASS")!==false) echo "error3";
                        else echo htmlentities(str_replace("\n\r","\n",$row['error']),ENT_QUOTES,"UTF-8");
			$sql = "delete from custominput where solution_id=?";
			pdo_query($sql,$solution_id);     
		}
		//echo $sql.$res;
	}
	else {
		if (isset($_GET['q']) && "user_id"==$_GET['q']) {
			echo $row['user_id']."[".$row['nick']."]";      // ajax onmouseover show who was copycated or shared the code to him
		}
		else if(( isset($OJ_PUBLIC_STATUS) && $OJ_PUBLIC_STATUS ) ||( isset($_SESSION[$OJ_NAME.'_'.'user_id']) && $_SESSION[$OJ_NAME.'_'.'user_id']== $row['user_id']) || isset($_SESSION[$OJ_NAME.'_'.'source_browser'])  ) {
			$contest_id = $row['contest_id'];
			
			if ($contest_id>0) {
				$result = pdo_query("select title from contest where contest_id=?",$contest_id);
				$contest_title = $result[0][0];
				
				if (stripos($contest_title,$OJ_NOIP_KEYWORD)!==false) {
					echo "$OJ_NOIP_KEYWORD";
					exit(0);
				}
			}
			if($row['result']==4) $row['pass_rate']=1;  // stop students asking about why not 100 on AC
			if (isset($_GET['t']) && "json"==$_GET['t']) {
				echo json_encode($row);
			}
			else {
				if(isset($_SESSION[$OJ_NAME.'_'.'administrator']))
					echo $row['result'].",".$row['memory']." KB,".$row['time']." ms,".$row['judger'].",".($row['pass_rate']*100).",".$row['user_id'];
				else
					echo $row['result'].",".$row['memory']." KB,".$row['time']." ms,"."none,".($row['pass_rate']*100).",".$row['user_id'];
			}
		}
	}
}
else {
	echo $solution_id;
	echo "0, 0, 0,unknown,0";
}

?>
