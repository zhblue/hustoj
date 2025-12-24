<?php
require_once("include/db_info.inc.php");
require_once("include/const.inc.php");
require_once("include/memcache.php");

// 检查BBS功能是否被禁用，如果禁用则显示错误信息并退出
if (isset($OJ_BBS) && !$OJ_BBS) {
    $view_errors = "$MSG_BBS_NOT_ALLOWED_FOR_EXAM  || $MSG_BBS is not available.";
    // 验证模板名称，防止路径遍历
    $template_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $OJ_TEMPLATE);
    if (empty($template_name)) {
        die("Invalid template name");
    }
    require("template/" . $template_name . "/error.php");
    exit(0);
}
ob_start();

/**
 * 检查题目或竞赛是否存在
 * 
 * @param string $pid 题目ID
 * @param string $cid 竞赛ID
 * @return bool 如果题目或竞赛存在返回true，否则返回false
 */
function problem_exist($pid, $cid)
{
    if ($pid == '') $pid = 0;
    if ($cid != '') {
        $cid = intval($cid);
    } else {
        $cid = null;
    }
    
    if ($pid != 0) {
        if ($cid !== null) {
            $sql = "SELECT 1 FROM `contest_problem` WHERE `contest_id` = ? AND `problem_id` = ?";
            $result = pdo_query($sql, $cid, intval($pid));
        } else {
            $sql = "SELECT 1 FROM `problem` WHERE `problem_id` = ?";
            $result = pdo_query($sql, intval($pid));
        }
    } else if ($cid !== null) {
        $sql = "SELECT 1 FROM `contest` WHERE `contest_id` = ?";
        $result = pdo_query($sql, $cid);
    } else {
        return true;
    }
    
    return count($result) > 0;
}

/**
 * 显示错误信息并退出程序
 * 
 * @param string $msg 错误信息
 */
function err_msg($msg)
{
    global $view_errors, $OJ_TEMPLATE; // 确保变量在函数作用域内可用
    $view_errors = "$msg";
    // 验证模板名称，防止路径遍历
    $template_name = preg_replace('/[^a-zA-Z0-9_-]/', '', $OJ_TEMPLATE);
    if (empty($template_name)) {
        die("Invalid template name");
    }
    require("template/" . $template_name . "/error.php");
    exit(0);
}

// 处理URL参数，获取题目ID和竞赛ID
$parm = "";
if (isset($_GET['pid'])) {
    $pid = intval($_GET['pid']);
    $parm = "pid=" . $pid;
} else {
    $pid = 0;
}
if (isset($_GET['cid'])) {
    $cid = intval($_GET['cid']);
} else {
    $cid = 0;
}
if (!empty($parm)) {
    $parm .= "&cid=" . $cid;
} else {
    $parm = "cid=" . $cid;
}
