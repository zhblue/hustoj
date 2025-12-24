<?php
$cache_time = 90;
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = "Source Code";

require_once("./include/const.inc.php");

// 检查会话是否已启动
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 检查是否提供了代码ID参数，如果没有则显示错误信息并退出
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] <= 0) {
    $view_errors = "No such code!\n";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

$id = intval($_GET['id']);

// 检查用户是否有打印机权限，如果没有则显示权限错误并退出
if (!isset($_SESSION[$OJ_NAME . '_' . 'printer'])) {
    $view_errors = "Not privileged!\n";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 初始化变量
$view_user = null;
$view_content = null;
$view_nick = null;
$view_school = null;

// 查询打印机表获取用户ID和内容信息
$sql = "SELECT user_id, content FROM `printer` WHERE `printer_id` = ?";
$result = pdo_query($sql, $id);

if ($result && count($result) > 0 && ($row = $result[0])) {
    $view_user = $row['user_id'];
    $view_content = $row['content'];
    
    // 根据用户ID查询用户详细信息（昵称和学校）
    $user_sql = "SELECT nick, school FROM users WHERE user_id = ?";
    $user_result = pdo_query($user_sql, $view_user);
    
    if ($user_result && count($user_result) > 0 && ($user_row = $user_result[0])) {
        $view_nick = $user_row['nick'];
        $view_school = $user_row['school'];
    }
} else {
    // 如果没有找到对应的打印机记录，显示错误
    $view_errors = "No such code!\n";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/printer_view.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
