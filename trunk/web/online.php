<?php
$cache_time = 30;
$OJ_CACHE_SHARE = false;
$debug = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
ini_set("display_errors", "Off");
require_once('./include/setlang.php');
require_once('./include/online.php');

// 创建在线用户对象
$on = new online();
$view_title = "Welcome To Online Judge";
require_once('./include/iplocation.php');

// 创建IP位置对象
$location = new IpLocation();
$users = $on->getAll();

$view_online = array();

// 检查是否为管理员，根据权限级别获取不同的登录日志数据
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
    $sql = "SELECT user_id,ip,time FROM `loginlog`";
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    if ($search != '') {
        // 验证搜索参数，防止SQL注入
        $sql = $sql . " WHERE ip LIKE ? ";
        $search = "%" . $search . "%";
    } else {
        $sql = $sql . " WHERE user_id<>? ";
        $search = $_SESSION[$OJ_NAME . '_' . 'user_id'];
    }
    $sql = $sql . " ORDER BY `log_id` DESC LIMIT 0,50";

    $result = pdo_query($sql, $search);
    if($result === false) {
        $result = array(); // 查询失败时返回空数组
    }
    $i = 0;
} else {
    $sql = "SELECT user_id,ip,time FROM `loginlog` ORDER BY log_id DESC LIMIT 20";
    $result = pdo_query($sql);
    if($result === false) {
        $result = array(); // 查询失败时返回空数组
    }
    $i = 0; // 初始化计数器
}

// 处理查询结果，将用户信息进行HTML转义并存储到视图数组中
foreach ($result as $row) {
    $view_online[$i][0] = "<a href='userinfo.php?user=" . htmlentities($row[0], ENT_QUOTES, "UTF-8") . "'>" . htmlentities($row[0], ENT_QUOTES, "UTF-8") . "</a>";
    $view_online[$i][1] = htmlentities($row[1], ENT_QUOTES, "UTF-8"); // IP地址
    $view_online[$i][2] = htmlentities($row[2], ENT_QUOTES, "UTF-8"); // 时间

    $i++;
}

/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/online.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
