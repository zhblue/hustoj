<?php 
// 设置缓存时间为10秒
$cache_time = 10;
// 设置OJ缓存共享为false
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
// 设置页面标题为"Welcome To Online Judge"
$view_title = "Welcome To Online Judge";

// 检查用户是否已登录，如果未登录则跳转到登录页面
if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $view_errors = "<a href=./loginpage.php>$MSG_Login</a>";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 从数据库中查询当前用户的学校、昵称和邮箱信息
$sql = "SELECT `school`,`nick`,`email` FROM `users` WHERE `user_id`=?";
$result = pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id']);
$row = $result[0];


/////////////////////////Template
// 加载修改页面模板
require("template/" . $OJ_TEMPLATE . "/modifypage.php");
/////////////////////////Common foot
// 如果存在缓存结束文件则包含该文件
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');


