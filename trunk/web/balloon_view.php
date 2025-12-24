<?php
$cache_time = 90;
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = "Source Code";

require_once("./include/const.inc.php");
if (!isset($_GET['id'])) {
    $view_errors = "No such code!\n";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}
if (!isset($_SESSION[$OJ_NAME . '_' . 'balloon'])) {

    $view_errors = "Not privileged!\n";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);

}
$id = strval(intval($_GET['id']));
$sql = "SELECT user_id,pid,cid FROM `balloon` WHERE `balloon_id`=?";
$result = pdo_query($sql, $id);
if ($row = $result[0]) {
    $view_user = $row['user_id'];
    $view_pid = $row['pid'];
    $cid = $row['cid'];
    $result = pdo_query("select * from users where user_id=?", $view_user);
    if ($row = $result[0]) {
        $view_nick = $row['nick'];
        $view_school = $row['school'];
    }
    $map = pdo_query("select * from news where title=?", "SEAT$cid");
    $view_map = "add map by adding news titled SEAT$cid";
    if (count($map) == 1) {
        $view_map = $map[0]['content'];
    }
}
/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/balloon_view.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
?>

