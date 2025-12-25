<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

////////////////////////////Common head
$cache_time = 2;
$OJ_CACHE_SHARE = false;
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
require_once("./include/const.inc.php");
require_once("./include/memcache.php");

$pid = 0;
// check the top arg

if (isset($_GET['pid'])) {
    $pid = intval($_GET['pid']);
}

// 查询特权表中指定pid的用户信息
$sql = "select user_id from privilege where rightstr=?  LIMIT 1";
$result = mysql_query_cache($sql, "p" . $pid);


if ($result) {
    $row = $result[0];
    echo "<a href='userinfo.php?user=" . htmlentities($row['user_id']) . "'>" . htmlentities($row['user_id']) . "</a>";
} else {
    echo "$MSG_IMPORTED";
}

