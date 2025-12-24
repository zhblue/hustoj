<?php
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
ini_set("display_errors", "Off");

/**
 * 检查用户是否已登录
 * 如果未登录则跳转到登录页面
 */
if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id']) || empty($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $view_errors = "<a href=./loginpage.php>$MSG_Login</a>";
    require_once("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

/**
 * 获取竞赛ID并验证用户权限
 * 只有管理员、竞赛创建者或有特定竞赛权限的用户才能访问
 */
$contest_id = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
if ($contest_id <= 0) {
    $view_errors = "<a href=./loginpage.php>Invalid contest ID!</a>";
    require_once("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 验证模板名称安全性
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $OJ_TEMPLATE)) {
    $view_errors = "<a href=./loginpage.php>Invalid template!</a>";
    require_once("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

if (!(isset($_SESSION[$OJ_NAME . '_' . 'm' . $contest_id]) || isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']))) {
    $view_errors = "<a href=./loginpage.php>No privileges!</a>";
    require_once("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

/**
 * 设置HTTP响应头，以文件下载方式返回竞赛日志
 */
header("content-type: application/file");
header("content-disposition: attachment; filename=\"logs-$contest_id.txt\"");

/**
 * 查询竞赛中的提交记录，包括用户ID、题目ID、结果和源代码
 * 通过右连接获取所有提交的源代码信息
 */
$sql = "select user_id,problem_id,result,source from source_code right join
                (select solution_id,problem_id,user_id,result from solution where contest_id=? ) S
                on source_code.solution_id=S.solution_id order by S.solution_id";
require_once("./include/const.inc.php");

try {
    $result = pdo_query($sql, $contest_id);
    if ($result === false) {
        $view_errors = "<a href=./loginpage.php>Database query failed!</a>";
        require_once("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }
} catch (Exception $e) {
    $view_errors = "<a href=./loginpage.php>Database error occurred!</a>";
    require_once("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

/**
 * 遍历查询结果，格式化输出每条提交记录
 * 输出格式：用户ID:Problem题目ID:结果
 * 换行后输出源代码内容
 * 用分隔线分隔每条记录
 */
foreach ($result as $row) {
    // 确保数组键存在且不为null
    $user_id = isset($row['user_id']) ? $row['user_id'] : '';
    $problem_id = isset($row['problem_id']) ? $row['problem_id'] : '';
    $result_value = isset($row['result']) ? intval($row['result']) : 0;
    $source = isset($row['source']) ? $row['source'] : '';
    
    // 验证结果索引是否在有效范围内
    if (isset($judge_result[$result_value])) {
        echo $user_id . ":Problem" . $problem_id . ":" . $judge_result[$result_value];
    } else {
        echo $user_id . ":Problem" . $problem_id . ":Unknown Result";
    }
    echo "\r\n" . $source;
    echo "\r\n------------------------------------------------------\r\n";
}

