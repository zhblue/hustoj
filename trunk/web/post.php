<?php
require_once("discuss_func.inc.php");
require_once("include/db_info.inc.php");
require_once("include/my_func.inc.php");

// 获取客户端IP地址
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

// 检查用户是否已登录，未登录则跳转到登录页面
if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id']) || empty($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $view_errors = "<a href=loginpage.php>Please Login First</a>";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 验证POST数据是否存在
if (!isset($_POST['content']) || !isset($_POST['title'])) {
    $view_errors = "Invalid request data!";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 检查帖子内容长度是否超过5000字符限制
if (strlen($_POST['content']) > 5000) {
    $view_errors = "Your contents is too long!";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 检查帖子标题长度是否超过60字符限制
if (strlen($_POST['title']) > 60) {
    require_once("oj-header.php");
    echo "Your title is too long!";
    // require_once("../oj-footer.php");
    exit(0);
}

// 检查帖子内容是否包含敏感词
if (has_bad_words($_POST['content'])) {
    $view_errors = "请文明上网！";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 检查帖子标题是否包含敏感词
if (has_bad_words($_POST['title'])) {
    $view_errors = "请文明上网！";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

$tid = null;
if ($_REQUEST['action'] == 'new') {
    // 处理新帖子创建逻辑
    if (isset($_POST['title']) && isset($_POST['content']) && !empty(trim($_POST['title'])) && !empty(trim($_POST['content']))) {
        // 获取并验证问题ID和竞赛ID
        if (isset($_REQUEST['pid']) && !empty($_REQUEST['pid']))
            $pid = intval($_REQUEST['pid']);
        else
            $pid = 0;

        if (isset($_REQUEST['cid']) && !empty($_REQUEST['cid']))
            $cid = intval($_REQUEST['cid']);
        else
            $cid = 0;

        // 处理竞赛相关的问题ID映射
        if ($pid == 0 && $cid > 0 && isset($_POST['pid']) && !empty($_POST['pid'])) {
            $problem_id = trim($_POST['pid']);
            $num = strpos($PID, $problem_id);
            if ($num !== false) {
                $contest_problem_result = pdo_query("select problem_id from contest_problem where contest_id=? and num=?", $cid, $num);
                if (!empty($contest_problem_result) && isset($contest_problem_result[0][0])) {
                    $pid = intval($contest_problem_result[0][0]);
                }
            }
        }

        // 插入新话题到数据库
        $sql = "INSERT INTO `topic` (`title`, `author_id`, `cid`, `pid`) VALUES(?,?,?,?)";
        $result = pdo_query($sql, $_POST['title'], $_SESSION[$OJ_NAME . '_' . 'user_id'], $cid, $pid);

        if ($result <= 0) {
            echo('Unable to post new.');
        } else {
            $tid = $result;
        }
    } else {
        echo('Error!');
    }
}

if ($_REQUEST['action'] == 'reply' || !is_null($tid)) {
    // 处理回复帖子逻辑
    if (is_null($tid) && isset($_POST['tid'])) {
        $tid = intval($_POST['tid']);
    }

    if (!is_null($tid) && isset($_POST['content']) && !empty(trim($_POST['content']))) {
        // 验证话题是否存在
        $rows = pdo_query("SELECT tid FROM topic WHERE tid=?", $tid);
        if (!empty($rows) && isset($rows[0]) && isset($rows[0][0]) && $rows[0][0] > 0) {

            // 插入回复内容到数据库
            $sql = "INSERT INTO `reply` (`author_id`, `time`, `content`, `topic_id`,`ip`) VALUES(?,NOW(),?,?,?)";
            $insert_result = pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id'], $_POST['content'], $tid, $ip);
            if ($insert_result) {
                // 根据是否存在竞赛ID重定向到相应页面
                if (isset($_REQUEST['cid'])) {
                    $cid = intval($_REQUEST['cid']);
                    header('Location: thread.php?cid=' . $cid . '&tid=' . $tid);
                } else {
                    header('Location: thread.php?tid=' . $tid);
                }
                exit(0);
            } else {
                $view_errors = "发帖失败！";
                require("template/" . $OJ_TEMPLATE . "/error.php");
                exit(0);
            }
        } else {
            $view_errors = "回复不存在的帖子！";
            require("template/" . $OJ_TEMPLATE . "/error.php");
            exit(0);
        }
    } else {
        $view_errors = "请文明上网！";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }
}
