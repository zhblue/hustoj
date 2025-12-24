<?php
$cache_time = 90;
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = "Source Code";

require_once("./include/const.inc.php");

// 验证输入参数
if (!isset($_GET['left']) || !is_numeric($_GET['left']) || intval($_GET['left']) <= 0) {
    $view_errors = "No such code!\n";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

$ok = false;
$id = intval($_GET['left']);

// 查询solution表获取提交信息
$sql = "SELECT * FROM `solution` WHERE `solution_id`=?";
$result = pdo_query($sql, $id);

// 检查查询结果
if (!$result || count($result) == 0) {
    $view_errors = "No such code!\n";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

$row = $result[0];
$slanguage = $row['language'];
$sresult = $row['result'];
$stime = $row['time'];
$smemory = $row['memory'];
$sproblem_id = $row['problem_id'];
$view_user_id = $suser_id = $row['user_id'];

// 检查用户是否已通过该题目，用于自动分享权限判断
if (isset($OJ_AUTO_SHARE) && $OJ_AUTO_SHARE && isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $sql = "SELECT 1 FROM solution WHERE result=4 AND problem_id=? AND user_id=?";
    $rrs = pdo_query($sql, $sproblem_id, $_SESSION[$OJ_NAME . '_' . 'user_id']);
    if ($rrs && count($rrs) > 0) {
        $ok = true;
    }
}

$view_source = "No source code available!";

// 检查用户权限：用户可以查看自己的代码或具有源码浏览器权限
if (isset($_SESSION[$OJ_NAME . '_' . 'user_id']) && $row && $row['user_id'] == $_SESSION[$OJ_NAME . '_' . 'user_id']) {
    $ok = true;
}
if (isset($_SESSION[$OJ_NAME . '_' . 'source_browser'])) {
    $ok = true;
}

// 查询源码表获取源代码内容
$sql = "SELECT `source` FROM `source_code` WHERE `solution_id`=?";
$result = pdo_query($sql, $id);

// 检查源码查询结果
if ($result && count($result) > 0) {
    $row = $result[0];
    if ($row && isset($row['source'])) {
        $view_source = $row['source'];
    }
}

// 如果没有权限且不是共享代码，则显示错误
if (!$ok && !$OJ_CACHE_SHARE) {
    $view_errors = "You don't have permission to view this source code!\n";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/comparesource.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');

