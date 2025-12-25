<?php
$cache_time = 10;
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
require_once('./include/my_func.inc.php');

$view_title = "Welcome To Online Judge";
if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    header("location:loginpage.php");
    exit(0);
}
require_once("./include/const.inc.php");

// 检查是否提供了解决方案ID参数
if (!isset($_GET['sid'])) {
    $view_errors = "No such code!\n";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);

}

/**
 * 验证字符串是否有效的函数
 * 通过检查数字字符的比例来判断字符串是否为有效的错误信息
 *
 * @param string $str2 待验证的字符串
 * @return bool 如果字符串中非数字字符比例超过3/4则返回true，否则返回false
 */
function is_valid($str2)
{
    $n = strlen($str2);
    $str = str_split($str2);
    $m = 1;
    for ($i = 0; $i < $n; $i++) {
        if (is_numeric($str[$i])) $m++;
    }
    return $n / $m > 3;
}

// 检查用户登录状态
if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $view_errors = $MSG_WARNING_ACCESS_DENIED;
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

$ok = false;
$id = intval($_GET['sid']);

// 查询解决方案信息
$sql = "SELECT * FROM `solution` WHERE `solution_id`=?";
$result = pdo_query($sql, $id);
$row = $result[0];

// 检查比赛是否被锁定
if (contest_locked($row['contest_id'], 256))
    $OJ_AI_API_URL = false;
$lang = $row["language"];

// 检查用户权限：用户是解决方案的所有者或具有源码浏览权限
if ($row && $row['user_id'] == $_SESSION[$OJ_NAME . '_' . 'user_id']) $ok = true;
if (isset($_SESSION[$OJ_NAME . '_' . 'source_browser'])) $ok = true;
$view_reinfo = "";

// 如果用户有权限查看编译错误信息
if ($ok == true) {
    if ($row['user_id'] != $_SESSION[$OJ_NAME . '_' . 'user_id'])
        $view_mail_link = "<a href='mail.php?to_user={$row['user_id']}&title=$MSG_SUBMIT $id'>Mail the auther</a>";

    // 获取编译错误信息
    $sql = "SELECT `error` FROM `compileinfo` WHERE `solution_id`=?";
    $result = pdo_query($sql, $id);
    $row = $result[0];
    if ($row && is_valid($row['error']))
        $view_reinfo = htmlentities(str_replace("\n\r", "\n", $row['error']), ENT_QUOTES, "UTF-8");


} else {

    $view_errors = $MSG_WARNING_ACCESS_DENIED;
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);

}

/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/ceinfo.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
