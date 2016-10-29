<?php
$OJ_CACHE_SHARE = false;
$cache_time = 30;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = $MSG_RANKLIST;

$scope = "";
if (isset($_GET['scope'])) {
  $scope = $_GET['scope'];
}
if ($scope != "" && $scope != 'd' && $scope != 'w' && $scope != 'm') {
  $scope = 'y';
}

$rank = 0;
if (isset($_GET['start'])) {
  $rank = intval($_GET['start']);
}

if (isset($OJ_LANG)) {
  require_once("./lang/$OJ_LANG.php");
}
$page_size = 50;
//$rank = intval ( $_GET ['start'] );
if ($rank < 0) {
  $rank = 0;
}

$sql = "SELECT `user_id`,`nick`,`solved`,`submit` FROM `users` WHERE `submit` > 1 ORDER BY `solved` DESC, `submit`, `reg_time` LIMIT ".strval($rank).",$page_size";
$view_total_sql = "SELECT count(1) as `mycount` FROM `users` WHERE `submit` >= 1";
if ($scope) {
  $s = "";
  switch ($scope) {
  case 'd':
    $s = date('Y').'-'.date('m').'-'.date('d');
    break;
  case 'w':
    $monday = mktime(0, 0, 0, date("m"),date("d")-(date("w")+7)%8+1, date("Y"));
    $s = strftime("%Y-%m-%d",$monday);
    break;
  case 'm':
    $s = date('Y').'-'.date('m').'-01';
    break;
  default :
    $s = date('Y').'-01-01';
  }
  $sql = "SELECT users.`user_id`,`nick`,s.`solved`,t.`submit` FROM `users`
    right join
    (select count(distinct problem_id) `solved`, `user_id` from solution where in_date > str_to_date('$s','%Y-%m-%d') and result = 4 group by user_id order by solved desc limit ".strval($rank).", $page_size) s on users.user_id = s.user_id
    left join
    (select count(problem_id) `submit`, `user_id` from solution where in_date > str_to_date('$s','%Y-%m-%d') group by user_id order by submit desc limit ".strval($rank).", ".($page_size*2).") t on users.user_id = t.user_id
    ORDER BY s.`solved` DESC, t.submit, reg_time LIMIT 0,50";

  $view_total_sql = "SELECT count(1) as `mycount` FROM `users`
    right join
    (select count(distinct problem_id) `solved`, `user_id` from solution where in_date > str_to_date('$s','%Y-%m-%d') and result = 4 group by user_id) s on users.user_id = s.user_id
    left join
    (select count(problem_id) `submit`, `user_id` from solution where in_date > str_to_date('$s','%Y-%m-%d') group by user_id) t on users.user_id = t.user_id";
}

if ($OJ_MEMCACHE) {
  require("./include/memcache.php");
  $result = mysql_query_cache($sql) ;//or die("Error! ".mysql_error());
  if ($result) {
    $rows_cnt = count($result);
  } else {
    $rows_cnt = 0;
  }
} else {
  $result = mysql_query($sql) or die("Error! ".mysql_error());
  if ($result) $rows_cnt = mysql_num_rows($result);
  else $rows_cnt = 0;
}
$view_rank = Array();
$i = 0;
for ($i = 0; $i < $rows_cnt; $i++) {
  if ($OJ_MEMCACHE) {
    $row = $result[$i];
  } else {
    $row = mysql_fetch_array($result);
  }
  $rank++;

  $view_rank[$i][0] = $rank;
  $view_rank[$i][1] = "<div class=center><a href='userinfo.php?user=".$row['user_id']."'>".$row['user_id']."</a>"."</div>";
  $view_rank[$i][2] = "<div class=center>".htmlspecialchars($row['nick'])."</div>";
  $view_rank[$i][3] = "<div class=center><a href='status.php?user_id=".$row['user_id']."&jresult=4'>".$row['solved']."</a>"."</div>";
  $view_rank[$i][4] = "<div class=center><a href='status.php?user_id=".$row['user_id']."'>".$row['submit']."</a>"."</div>";

  if ($row['submit'] == 0) {
    $view_rank[$i][5] = "0.000%";
  } else {
    $view_rank[$i][5] = sprintf ( "%.03lf%%", 100 * $row['solved'] / $row['submit'] );
  }
}

if (!$OJ_MEMCACHE) mysql_free_result($result);

if ($OJ_MEMCACHE) {
  // require("./include/memcache.php");
  $result = mysql_query_cache($view_total_sql);// or die("Error! ".mysql_error());
  if ($result) {
    $rows_cnt = count($result);
  } else {
    $rows_cnt = 0;
  }
} else {
  $result = mysql_query($view_total_sql);// or die("Error! ".mysql_error());
  if ($result) {
    $rows_cnt = mysql_num_rows($result);
  } else {
    $rows_cnt = 0;
  }
}

if ($OJ_MEMCACHE) {
  $row = $result[0];
} else {
  $row = mysql_fetch_array($result);
}

echo mysql_error();
$view_total = $row['mycount'];

// mysql_free_result ( $result );
if (!$OJ_MEMCACHE) {
  mysql_free_result($result);
}

/////////////////////////Template
require("template/".$OJ_TEMPLATE."/ranklist.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php')) {
  require_once('./include/cache_end.php');
}
?>
