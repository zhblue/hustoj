<?php
$OJ_CACHE_SHARE = true;
$cache_time = 10;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
require_once("./include/const.inc.php");
require_once("./include/my_func.inc.php");
require_once("./include/memcache.php");

$view_title = $MSG_CONTEST.$MSG_RANKLIST;

$title = "";

class TM {
	var $solved = 0;
	var $time = 0;
	var $p_wa_num;
	var $p_ac_sec;
	var $user_id;
	var $nick;

	function __construct() {
		$this->solved = 0;
		$this->time = 0;
		$this->p_wa_num = array(0);
		$this->p_ac_sec = array(0);
	}

	function Add($pid,$sec,$res) {
		global $OJ_CE_PENALTY;
		//echo "Add $pid $sec $res<br>";
		if ($sec<0) return;  // restarted contest ignore previous submission
		
		if (isset($this->p_ac_sec[$pid]) && $this->p_ac_sec[$pid]>0)
			return;

		if ($res!=4) {
			if (isset($OJ_CE_PENALTY)&&!$OJ_CE_PENALTY&&$res==11)
				return;  // ACM WF punish no ce 

			if (isset($this->p_wa_num[$pid])) {
				$this->p_wa_num[$pid]++;
			}
			else {
				$this->p_wa_num[$pid] = 1;
			}
		}
		else {
			$this->p_ac_sec[$pid] = $sec;
			$this->solved++;

			if (!isset($this->p_wa_num[$pid]))
				$this->p_wa_num[$pid] = 0;

			$this->time += $sec+$this->p_wa_num[$pid]*1200;
			//echo "Time:".$this->time."<br>";
			//echo "Solved:".$this->solved."<br>";
		}
	}
}

function s_cmp($A,$B) {
	//echo "Cmp....<br>";
	if ($A->solved!=$B->solved)
		return $A->solved<$B->solved;
	else
		return $A->time>$B->time;
}

// contest start time
if (!isset($_GET['cid']))
	die("No Such Contest!");

$cid = intval($_GET['cid']);

if ($OJ_MEMCACHE) {
	$sql = "SELECT `start_time`,`title`,`end_time` FROM `contest` WHERE `contest_id`=$cid";
	require("./include/memcache.php");
	$result = mysql_query_cache($sql);

	if ($result)
		$rows_cnt = count($result);
	else
		$rows_cnt = 0;
}
else {
	$sql = "SELECT `start_time`,`title`,`end_time` FROM `contest` WHERE `contest_id`=?";
	$result = pdo_query($sql,$cid);

	if($result)
		$rows_cnt = count($result);
	else
		$rows_cnt = 0;
}

$start_time = 0;
$end_time = 0;

if ($rows_cnt>0) {
	//$row=$result[0];

	if($OJ_MEMCACHE)
		$row = $result[0];
	else
		$row = $result[0];

	$start_time = strtotime($row['start_time']);
	$end_time = strtotime($row['end_time']);
	$title = $row['title'];
	$view_title = $title;
}

if (!$OJ_MEMCACHE)
	if ($start_time==0) {
		$view_errors = "No Such Contest";
		require("template/".$OJ_TEMPLATE."/error.php");
		exit(0);
	}

if ($start_time>time()) {
	$view_errors = "Contest Not Started!";
	require("template/".$OJ_TEMPLATE."/error.php");
	exit(0);
}

	$noip = (time()<$end_time) && (stripos($title,$OJ_NOIP_KEYWORD)!==false);
	if(isset($_SESSION[$OJ_NAME.'_'."administrator"])||
		isset($_SESSION[$OJ_NAME.'_'."m$cid"])||
		isset($_SESSION[$OJ_NAME.'_'."source_browser"])||
		isset($_SESSION[$OJ_NAME.'_'."contest_creator"])
	   ) $noip=false;
if ($noip) {
	$view_errors =  "<h2>$MSG_NOIP_WARNING</h2>";
	require("template/".$OJ_TEMPLATE."/error.php");
	exit(0);
}

if (!isset($OJ_RANK_LOCK_PERCENT))
	$OJ_RANK_LOCK_PERCENT = 0;

$lock = $end_time-($end_time-$start_time)*$OJ_RANK_LOCK_PERCENT;

//echo $lock.'-'.date("Y-m-d H:i:s",$lock);
$view_lock_time = $start_time+($end_time-$start_time)*(1-$OJ_RANK_LOCK_PERCENT);
$locked_msg = "";

if (time()>$view_lock_time && time()<$end_time+$OJ_RANK_LOCK_DELAY) {
	$locked_msg = "The board has been locked.";
}

if ($OJ_MEMCACHE) {
	$sql = "SELECT count(1) as pbc FROM `contest_problem` WHERE `contest_id`='$cid'";
	
	$result = mysql_query_cache($sql);
	
	if ($result)
		$rows_cnt = count($result);
	else
		$rows_cnt = 0;
}
else {
	$sql = "SELECT count(1) as pbc FROM `contest_problem` WHERE `contest_id`=?";
	$result = pdo_query($sql,$cid);

	if ($result)
		$rows_cnt = count($result);
	else
		$rows_cnt = 0;
}

if ($OJ_MEMCACHE)
	$row = $result[0];
else
	$row = $result[0];

//$row=$result[0];
$pid_cnt = intval($row['pbc']);

require("./include/contest_solutions.php");

$user_cnt = 0;
$user_name = '';
$U = array();

//$U[$user_cnt]=new TM();
for ($i=0; $i<$rows_cnt; $i++) {
	$row = $result[$i];
	$n_user = $row['user_id'];

	if (strcmp($user_name,$n_user)) {
		$user_cnt++;
		$U[$user_cnt] = new TM();

		$U[$user_cnt]->user_id = $row['user_id'];
		$U[$user_cnt]->nick = $row['nick'];

		$user_name = $n_user;
	}

	if (time()<$end_time+$OJ_RANK_LOCK_DELAY && $lock<strtotime($row['in_date']))
		$U[$user_cnt]->Add($row['num'],strtotime($row['in_date'])-$start_time,0);
	else
		$U[$user_cnt]->Add($row['num'],strtotime($row['in_date'])-$start_time,intval($row['result']));
}

usort($U,"s_cmp");

////firstblood
$first_blood = array();
for ($i=0; $i<$pid_cnt; $i++) {
	$first_blood[$i] = "";
}

if ($OJ_MEMCACHE) {
	$sql = "select s.num,s.user_id from solution s ,
	(select num,min(solution_id) minId from solution where contest_id=$cid and result=4 GROUP BY num ) c where s.solution_id = c.minId";
	$fb = mysql_query_cache($sql);

	if ($fb)
		$rows_cnt = count($fb);
	else
		$rows_cnt = 0;
}
else {
	$sql = "select s.num,s.user_id from solution s ,
	(select num,min(solution_id) minId from solution where contest_id=? and result=4 GROUP BY num ) c where s.solution_id = c.minId";
	$fb = pdo_query($sql,$cid);

	if ($fb)
		$rows_cnt = count($fb);
	else
		$rows_cnt = 0;
}

for ($i=0; $i<$rows_cnt; $i++) {
	$row = $fb[$i];
	$first_blood[$row['num']] = $row['user_id'];
}

/////////////////////////Template
require("template/".$OJ_TEMPLATE."/contestrank.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
	require_once('./include/cache_end.php');
?>
