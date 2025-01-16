<?php
$OJ_CACHE_SHARE = true;
$cache_time = 10;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = $MSG_CONTEST . $MSG_RANKLIST;
$title = "";
require_once("./include/const.inc.php");
require_once("./include/my_func.inc.php");
require_once("./include/memcache.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])||isset($_SESSION[$OJ_NAME.'_'.'password_setter']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
/************************  数据库  *******************************/
/**
 * 获得该比赛的提交记录
 * @param $OJ_MEMCACHE
 * @param $cid
 * @return mixed
 */
function getSubmitByCid($OJ_MEMCACHE,$cid){
    if ($OJ_MEMCACHE) {
        $sql = "SELECT `solution_id`,`user_id`,`problem_id`,`in_date`,`result` FROM solution WHERE `contest_id`='$cid' and num>=0 and problem_id>0";
        $subList = mysql_query_cache($sql);
    } else {
        $sql = "SELECT `solution_id`,`user_id`,`problem_id`,`in_date`,`result` FROM solution WHERE `contest_id`= ? and num>=0 and problem_id>0";
        $subList = pdo_query($sql, $cid);
    }
    return $subList;
}

/**
 * 获得提交过该比赛的账号
 * @param $OJ_MEMCACHE
 * @param $cid
 * @return mixed
 */
function getTeamByCid($OJ_MEMCACHE,$cid){
    if ($OJ_MEMCACHE) {
        $sql = "SELECT a.user_id,nick FROM (SELECT distinct user_id FROM solution WHERE contest_id = '$cid')AS a INNER JOIN users WHERE users.user_id = a.user_id";
        $teamList = mysql_query_cache($sql);
    } else {
        $sql = "SELECT a.user_id,nick FROM (SELECT distinct user_id FROM solution WHERE contest_id = ?)AS a INNER JOIN users WHERE users.user_id = a.user_id";
        $teamList = pdo_query($sql, $cid);
    }
    return $teamList;
}

/**
 * 获得题目ID与 比赛中序号的Map
 * @param $OJ_MEMCACHE
 * @param $cid
 * @return array
 */
function getProblemMapByCid($OJ_MEMCACHE,$cid) {
    if ($OJ_MEMCACHE) {
        $sql = "SELECT `problem_id`,`num` FROM contest_problem WHERE `contest_id` = '$cid'";
        $proList = mysql_query_cache($sql);
    } else {
        $sql = "SELECT `problem_id`,`num` FROM contest_problem WHERE `contest_id` = ?";
        $proList = pdo_query($sql, $cid);
    }
    $arr = array();
    for($i=0;$i<count($proList);$i++) {
        $row = $proList[$i];
        $arr[$row['problem_id']] = chr(65+$row['num']);
    }
    //print_r($arr);
    return $arr;
}

/*****************************  工具 *******************************/
/**
 * 前端界面的判题结果与oj不同，所以需要转换
 * @param $result
 * @return int
 */
function changeResult($result){
    switch($result) {
        case 4 : $resultID = 0;break;
        case 5 : $resultID = 1;break;
        case 6 : $resultID = 4;break;
        case 7 : $resultID = 2;break;
        case 8 : $resultID = 3;break;
        case 9 : $resultID = 6;break;
        case 10 : $resultID = 5;break;
        case 11 : $resultID = 7;break;
        default : $resultID = -1;break;
    }
    return $resultID;
}

/**
 * 转换一条提交记录成json
 * @param $submit
 * @param $problemMap
 */
function submit2Array($submit,$problemMap){
    //`solution_id`,`user_id`,`problem_id`,`in_date`,`result`
    $arr = array();
    $arr["submitID"] = $submit["solution_id"];
    $arr["userID"] = $submit["user_id"];
    $arr["alphabetID"] = $problemMap[$submit["problem_id"]];
    $arr["subTime"] = $submit["in_date"];
    $arr["resultID"] = changeResult($submit["result"]);
    return $arr;
}

/**
 * 转换一个队伍信息成json
 * @param $team
 */
function team2Array($team) {
    // a.user_id,nick
    $arr = array();
    $arr["userID"] = $team["user_id"];
    $arr["nickName"] = $team["nick"];
    $arr["realName"] = "";
    $arr["official"] = 1;
    return $arr;
}



// 请求未带参数
if (!isset($_GET['cid'])) {
    $view_errors = "No Such Contest";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// contest_id
$cid = intval($_GET['cid']);
if(isset($OJ_NO_CONTEST_WATCHER)&&$OJ_NO_CONTEST_WATCHER) require_once("contest-check.php");

// 非管理员不能访问
/*if (!isset($_SESSION[$OJ_NAME.'_'.'administrator'])) {
    $view_errors = "Access Deny";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit();
}
*/
if(isset($_GET['lock_percent'])){
   $OJ_RANK_LOCK_PERCENT=floatval($_GET['lock_percent']);
   $_SESSION[$OJ_NAME."_lock_percent"]=$OJ_RANK_LOCK_PERCENT;
}
if(isset($_SESSION[$OJ_NAME."_lock_percent"])){
   $OJ_RANK_LOCK_PERCENT=$_SESSION[$OJ_NAME."_lock_percent"];
}
// 不封榜，就不能滚榜
if (!isset($OJ_RANK_LOCK_PERCENT)||$OJ_RANK_LOCK_PERCENT==0) {
    $view_errors = "The Ranking is Unlocked";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

//  查询比赛是否存在
if ($OJ_MEMCACHE) {
    $sql = "SELECT `start_time`,`title`,`end_time` FROM `contest` WHERE `contest_id`='$cid'";
    $result = mysql_query_cache($sql);
} else {
    $sql = "SELECT `start_time`,`title`,`end_time` FROM `contest` WHERE `contest_id`=?";
    $result = pdo_query($sql, $cid);
}

// 统计查询到的个数
if ($result) {
    $rows_cnt = count($result);
}else {
    $rows_cnt = 0;
}

// 比赛开始时间和比赛结束时间
$start_time = 0;
$end_time = 0;

// 如果比赛存在
if ($rows_cnt > 0) {
    $row = $result[0];
    $start_time = strtotime($row['start_time']);
    $end_time = strtotime($row['end_time']);
    $title = $row['title'];
}


if (!$OJ_MEMCACHE) {
    // 开始时间仍然为零 说明 比赛不存在
    if ($start_time == 0) {
        $view_errors = "No Such Contest";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }
}

// 还未开始
if ($start_time > time()) {
    $view_errors = "$MSG_CONTEST $MSG_Contest_Pending!";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 还未结束
if ($end_time > time()) {
    $view_errors = "Contest Not Finished!";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

	$noip = (time()<$end_time) && (stripos($title,$OJ_NOIP_KEYWORD)!==false);
	if(isset($_SESSION[$OJ_NAME.'_'."administrator"])||
		isset($_SESSION[$OJ_NAME.'_'."m$cid"])||
		isset($_SESSION[$OJ_NAME.'_'."source_browser"])||
		isset($_SESSION[$OJ_NAME.'_'."contest_creator"])
	   ) $noip=false;
if($noip||contest_locked($cid,20)){
      $view_errors =  "<h2>$MSG_NOIP_WARNING</h2>";
      require("template/".$OJ_TEMPLATE."/error.php");
      exit(0);
}
// json 请求时
if (isset($_GET['type'])&&$_GET['type']=='json') {
    header('Content-Type:application/json');

    if(isset($_GET['list'])&&$_GET['list']=='submit'){

        $subList = getSubmitByCid($OJ_MEMCACHE,$cid);
		$problemMap = getProblemMapByCid($OJ_MEMCACHE,$cid);

		$arr = array();
		for($i=0;$i<count($subList);$i++){
		    $arr[$i] = submit2Array($subList[$i],$problemMap);
		}
		echo json_encode($arr);

	    }else if(isset($_GET['list'])&&$_GET['list']=='team'){
		$teamList = getTeamByCid($OJ_MEMCACHE,$cid);
		$arr = array();
		for($i=0;$i<count($teamList);$i++){
		    $arr[$i] = team2Array($teamList[$i]);
		}
		echo json_encode($arr);
	    }
	    exit(0);
	}

	//普通请求时
	$lock = $end_time - ($end_time - $start_time) * $OJ_RANK_LOCK_PERCENT;
	$start_time_str = date("Y-m-d H:i:s",$start_time);
	$lock_time_str = date("Y-m-d H:i:s",$lock);
$problem_num = pdo_query("select count(distinct problem_id ) from contest_problem where contest_id=?",$cid)[0][0];
if($OJ_ON_SITE_TEAM_TOTAL!=0)
 $team_num=$OJ_ON_SITE_TEAM_TOTAL;
else
 $team_num=pdo_query("select count(distinct user_id ) from solution where contest_id=?",$cid)[0][0];
$gold_num=intval($team_num*0.05);
$silver_num=intval($team_num*0.15);
$bronze_num=intval($team_num*0.20);

/////////////////////////Template
require("template/".$OJ_TEMPLATE."/contestrank3.php");

/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
?>


