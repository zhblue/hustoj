<?php
$cache_time = 90;
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = "Source Code";

require_once("./include/const.inc.php");

// 检查是否提供了代码ID参数，如果没有则显示错误信息并退出
if (!isset($_GET['id'])) {
    $view_errors = "No such code!\n";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 检查用户是否具有查看权限，如果没有则显示权限错误并退出
if (!isset($_SESSION[$OJ_NAME . '_' . 'balloon'])) {

    $view_errors = "Not privileged!\n";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);

}

// 获取并验证代码ID，从数据库中查询气球信息
$id = strval(intval($_GET['id']));
$sql = "SELECT user_id,pid,cid FROM `balloon` WHERE `balloon_id`=?";
$result = pdo_query($sql, $id);
if ($row = $result[0]) {
    $view_user = $row['user_id'];
    $view_pid = $row['pid'];
    $cid = $row['cid'];

    // 根据用户ID查询用户详细信息（昵称和学校）
    $result = pdo_query("select * from users where user_id=?", $view_user);
    if ($row = $result[0]) {
        $view_nick = $row['nick'];
        $view_school = $row['school'];
    }

    // 查询座位地图信息，如果存在SEAT+竞赛ID的新闻则使用其内容作为地图
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

