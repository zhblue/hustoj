<?php

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

////////////////////////////Common head
$cache_time = 2;
$OJ_CACHE_SHARE = false;

require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/my_func.inc.php');
require_once('./include/memcache.php');
require_once("./include/const.inc.php");
require_once('./include/setlang.php');

if (isset($OJ_LANG)) {
  require_once("./lang/$OJ_LANG.php");
}


if (isset($_GET['cid'])) {
	$cid = intval($_GET['cid']);
	$view_cid = $cid;
	//print $cid;

	//check contest valid
	$sql = "SELECT * FROM `contest` WHERE `contest_id`=?";
	$result = pdo_query($sql,$cid);

	$rows_cnt = count($result);
	$contest_ok = true;
	$password = "";

	if (isset($_POST['password']))
		$password = $_POST['password'];

	if (false) {
		$password = stripslashes($password);
	}

	if ($rows_cnt==0) {
		$view_title = "比赛已经关闭!";
	}
	else{
		$row = $result[0];
		$view_private = $row['private'];

		if ($password!="" && $password==$row['password'])
			$_SESSION[$OJ_NAME.'_'.'c'.$cid] = true;

		if ($row['private'] && !isset($_SESSION[$OJ_NAME.'_'.'c'.$cid]))
			$contest_ok = false;

		if($row['defunct']=='Y')
			$contest_ok = false;

		if (isset($_SESSION[$OJ_NAME.'_'.'administrator']))
			$contest_ok = true;

		$now = time();
		$start_time = strtotime($row['start_time']);
		$end_time = strtotime($row['end_time']);
		$view_description = $row['description'];
		$view_title = $row['title'];
		$view_start_time = $row['start_time'];
		$view_end_time = $row['end_time'];
		$user_id = $row['user_id'];

		if (! ( isset($_SESSION[$OJ_NAME.'_'.'administrator'])|| $user_id == $_SESSION[$OJ_NAME.'_user_id']  ) ) {
			$view_errors = "<center>";
			$view_errors .= "<h3>$MSG_CONTEST_ID : $view_cid - $view_title</h3>";
			$view_errors .= "<p>$view_description</p>";
			$view_errors .= "<br>";
			$view_errors .= "<span class=text-success>$MSG_ADMIN $MSG_CONTEST_CREATOR only </span>";
			$view_errors .= "</center>";
			$view_errors .= "<br><br>";

			require("template/".$OJ_TEMPLATE."/error.php");
			exit(0);
		}
	}

	if (!$contest_ok) {
		$view_errors = "<center>";
		$view_errors .= "<h3>$MSG_CONTEST_ID : $view_cid - $view_title</h3>";
		$view_errors .= "<p>$view_description</p>";
		$view_errors .= "<span class=text-danger>$MSG_PRIVATE_WARNING</span>";

		$view_errors .= "<br><br>";

		$view_errors .= "<div class='btn-group'>";
		$view_errors .= "<a href=contestrank.php?cid=$view_cid class='btn btn-primary'>$MSG_STANDING</a>";
		$view_errors .= "<a href=contestrank-oi.php?cid=$view_cid class='btn btn-primary'>OI$MSG_STANDING</a>";
		$view_errors .= "<a href=conteststatistics.php?cid=$view_cid class='btn btn-primary'>$MSG_STATISTICS</a>";
		$view_errors .= "</div>";

		$view_errors .= "<br><br>";
		$view_errors .= "<table align=center width=80%>";
		$view_errors .= "<tr align='center'>";
		$view_errors .= "<td>";
		$view_errors .= "<form class=form-inline method=post action=contest.php?cid=$cid>";
		$view_errors .= "<input class=input-mini type=password name=password value='' placeholder=$MSG_CONTEST-$MSG_PASSWORD>";
		$view_errors .= "<button class='form-control'>$MSG_SUBMIT</button>";
		$view_errors .= "</form>";
		$view_errors .= "</td>";
		$view_errors .= "</tr>";
		$view_errors .= "</table>";
		$view_errors .= "<br>";

		require("template/".$OJ_TEMPLATE."/error.php");
		exit(0);
	}
}

//require_once("./include/set_get_key.php");
if (isset($_GET['cid'])) {
	$contest_id = intval($_GET['cid']);

	$sql = "select * from (select count(distinct user_id) c,ip from solution where contest_id=? group by ip) suspect inner join (select distinct ip, user_id, in_date from solution where contest_id=? ) u on suspect.ip=u.ip and suspect.c>1 order by c desc, u.ip, in_date, user_id";

	$result1 = pdo_query($sql,$contest_id,$contest_id);

	$start = pdo_query("select start_time from contest where contest_id=?",$contest_id)[0][0];
	$end = pdo_query("select end_time from contest where contest_id=?",$contest_id)[0][0];
	$sql = "select * from (select count(distinct ip) c,user_id from loginlog where time>=? and time<=? group by user_id) suspect inner join (select distinct user_id from solution where contest_id=? ) u on suspect.user_id=u.user_id and suspect.c>1 inner join (select distinct ip, user_id, time from loginlog where time>=? and time<=? ) ips on ips.user_id=u.user_id order by c desc, u.user_id, ips.time, ip";

	$result2 = pdo_query($sql,$start,$end,$contest_id,$start,$end);
}

/////////////////////////Template
if (isset($_GET['cid']))
  require("template/".$OJ_TEMPLATE."/suspect_list.php");

/////////////////////////Common foot
if (file_exists('./include/cache_end.php') )
  require_once('./include/cache_end.php');
?>
