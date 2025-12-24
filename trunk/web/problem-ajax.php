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

$view_title = "$MSG_STATUS";
$pid = 0;
// check the top arg

if (isset($_GET['pid'])) {
    $pid = intval($_GET['pid']);
}

// 查询具有指定权限的用户ID，根据是否启用memcache选择不同的查询方式
if ($OJ_MEMCACHE) {
    $sql = "select user_id from privilege where rightstr='p$pid'  LIMIT 1";
    $result = mysql_query_cache($sql);

} else {
    $sql = "select user_id from privilege where rightstr=?  LIMIT 1";
    $result = pdo_query($sql, "p" . $pid);

}

// 输出查询结果，如果找到用户则显示用户信息链接，否则显示导入消息
if ($result) {
    $row = $result[0];
    echo "<a href='userinfo.php?user=" . htmlentities($row['user_id'], ENT_QUOTES, 'utf-8') . "'>" . htmlentities($row['user_id'], ENT_QUOTES, 'utf-8') . "</a>";
} else {
    echo "$MSG_IMPORTED";
}


