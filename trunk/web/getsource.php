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

$ok = false;
$id = intval($_GET['id']);

// 查询solution表获取提交信息
$sql = "SELECT * FROM `solution` WHERE `solution_id`=?";
$result = pdo_query($sql, $id);
$row = !empty($result) ? $result[0] : null;

if (!$row) {
    $view_errors = "No such code!\n";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

$slanguage = $row['language'];
$sresult = $row['result'];
$stime = $row['time'];
$nick = $row['nick'];
$smemory = $row['memory'];
$sproblem_id = $row['problem_id'];
$view_user_id = $suser_id = $row['user_id'];


// 检查用户是否具有自动分享权限，如果用户已AC相同题目则允许查看代码
if (isset($OJ_AUTO_SHARE) && $OJ_AUTO_SHARE && isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $sql = "SELECT 1 FROM solution where 
			result=4 and problem_id=? and user_id=?";
    $rrs = pdo_query($sql, $sproblem_id, $_SESSION[$OJ_NAME . '_' . 'user_id']);
    $ok = (count($rrs) > 0);
}
$view_source = "No source code available!";

// 检查用户权限：如果是代码所有者或具有源码浏览器权限，则允许查看
if (isset($_SESSION[$OJ_NAME . '_' . 'user_id']) && $row && $row['user_id'] == $_SESSION[$OJ_NAME . '_' . 'user_id']) $ok = true;
if (isset($_SESSION[$OJ_NAME . '_' . 'source_browser'])) $ok = true;

// 查询源码表获取实际的源代码内容
$sql = "SELECT `source` FROM `source_code_user` WHERE `solution_id`=?";
$result = pdo_query($sql, $id);
$row = !empty($result) ? $result[0] : null;
if ($row)
    $view_source = $row['source'];

// 如果用户有权限查看代码，则显示格式化的源码信息
if ($ok == true) {
    $brush = strtolower($language_name[$slanguage]);
    if ($brush == 'pascal') $brush = 'delphi';
    if ($brush == 'obj-c') $brush = 'c';
    if ($brush == 'freebasic') $brush = 'vb';
    ob_start();
    echo "/**************************************************************\n";
    echo "\tProblem: $sproblem_id\n\tUser: $suser_id\n\tNick:$nick\n";
    echo "\tLanguage: " . $language_name[$slanguage] . "\n\tResult: " . $judge_result[$sresult] . "\n";
    if ($sresult == 4) {
        echo "\tTime:" . $stime . " ms\n";
        echo "\tMemory:" . $smemory . " kb\n";
    }
    echo "****************************************************************/\n\n";
    $auth = ob_get_contents();
    ob_end_clean();

    echo (str_replace("\n\r", "\n", $view_source)) . "\n" . $auth;

} else {
    // 用户没有权限查看代码时显示提示信息
    echo "I am sorry, You could not view this code!";
}
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
