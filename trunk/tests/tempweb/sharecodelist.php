<?php
$OJ_CACHE_SHARE = true;
$cache_time = 10;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = $MSG_CONTEST . $MSG_RANKLIST;
$title = "";
require_once("./include/const.inc.php");
require_once("./include/my_func.inc.php");

$user_id = $_SESSION[$OJ_NAME . '_' . 'user_id'];


/************************  数据库  *******************************/
function getShareCodeListByPage($user_id)
{
    $sql = "SELECT `share_id`, `title`, `language`, `share_time` FROM share_code WHERE `user_id` = ? ORDER BY share_time DESC";
    $share_list = pdo_query($sql, $user_id);
    return $share_list;
}


/*****************************  路由 *******************************/
// 如果有比赛正在进行，则不可用
if (
    (isset($OJ_EXAM_CONTEST_ID) && $OJ_EXAM_CONTEST_ID > 0) ||
    (isset($OJ_ON_SITE_CONTEST_ID) && $OJ_ON_SITE_CONTEST_ID > 0) ||
    (isset($OJ_SHARE_CODE) && !$OJ_SHARE_CODE)
) {
    $view_errors = "代码分享功能当前不可用！";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit (0);
}

if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $view_errors = "<a href=loginpage.php>$MSG_Login</a>";
    require("template/" . $OJ_TEMPLATE . "/error.php");
} else {
    // page
    if (!isset($_GET['page'])) {
        $page = 1;
    } else {
        $page = intval($_GET['page']);
        if ($page <= 0) {
            $page = 1;
        }
    }

    $pageSize = 50;
    $first = ($page - 1) * $pageSize;
    $last = $first + $pageSize;
    $share_list = getShareCodeListByPage($user_id, $page);
    $pageNum = ceil(count($share_list) / $pageSize);
    $share_list = array_slice($share_list, $first, $last);
    //print_r($share_list);
    require("template/" . $OJ_TEMPLATE . "/sharecodelist.php");
}


/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
?>