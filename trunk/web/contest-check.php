<?php
require_once("include/curl.php");

/**
 * 验证比赛访问权限和状态的脚本
 * 检查用户是否有权访问指定比赛，验证比赛状态（开始/结束/私有等）
 * 并处理比赛密码验证、IP子网限制、限时比赛等功能
 */

/**
 * 获取当前Unix时间戳
 */
$unixnow = time();

/**
 * 获取并验证比赛ID
 * 确保比赛ID为正整数
 */
$cid = intval($_GET['cid']);
if ($cid <= 0) {
    $cid = 1000; // 设置默认值防止负数或零
}
$view_cid = $cid;
//print $cid;

/**
 * 获取当前登录用户ID
 * 从SESSION中获取用户ID，如果未登录则为空字符串
 */
if (isset($_SESSION[$OJ_NAME . "_user_id"]))
    $user_id = $_SESSION[$OJ_NAME . "_user_id"];
else
    $user_id = "";

/**
 * 检查比赛是否存在和有效性
 * 从数据库查询比赛信息并验证比赛状态
 */
$sql = "SELECT * FROM `contest` WHERE `contest_id`=?";
$result = mysql_query_cache($sql, $cid);
$rows_cnt = count($result);

if ($rows_cnt > 0) {
    $row = $result[0];
    $start_time = strtotime($row['start_time']);
    $end_time = strtotime($row['end_time']);
    $view_description = $row['description'];
    $view_title = $row['title'];
    $view_start_time = $row['start_time'];
    $view_end_time = $row['end_time'];
    $subnet = $row['subnet'];
}

/**
 * 初始化比赛访问状态和密码
 */
$contest_ok = true;
$password = "";

if (isset($_POST['password']))
    $password = $_POST['password'];

if (false) {
    $password = stripslashes($password);
}

/**
 * 验证IP子网限制
 * 检查用户IP是否在比赛允许的子网范围内
 */
if (!in_subnet_of_contest($ip, $cid)) {
    $contest_ok = false;
    $view_description = "Not in $MSG_SUBNET $subnet";
}

/**
 * 验证比赛是否存在和是否已关闭
 * 如果比赛不存在或已被禁用且用户不是管理员，则设置错误状态
 */
if ($rows_cnt == 0 || ($row['defunct'] == 'Y' && !isset($_SESSION[$OJ_NAME . '_administrator']))) {
    $view_title = "比赛已经关闭!";
    $contest_ok = false;
} else {

    $view_private = $row['private'];
    $view_contest_creator = $row['user_id'];
    if ($password != "" && $password == $row['password'])
        $_SESSION[$OJ_NAME . '_' . 'c' . $cid] = true;

    /**
     * 处理私有比赛的权限验证
     * 检查用户是否有访问私有比赛的权限
     */
    if ($row['private'] && !isset($_SESSION[$OJ_NAME . '_' . 'c' . $cid])) {

        $sql = "SELECT count(*) FROM `privilege` WHERE `user_id`=? AND `rightstr`=?";
        $result = mysql_query_cache($sql, $user_id, "c$cid");
        $row = $result[0];
        $ccnt = intval($row[0]);
        if ($ccnt > 0) {
            $contest_ok = true;
            $_SESSION[$OJ_NAME . '_' . 'c' . $cid] = true;
        } else {
            $contest_ok = false;
        }
    }
//              if($row['defunct']=='Y')  //defunct problem not in contest/exam list
//                      $contest_ok = false;

    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']))
        $contest_ok = true;

    /**
     * 检查比赛是否已经结束
     * 设置比赛结束标志用于后续处理
     */
    if ($unixnow > $end_time) $contest_is_over = true;    // 已经结束的比赛，按练习方式提交
    else $contest_is_over = false;

    /**
     * 检查比赛是否尚未开始
     * 如果比赛未开始且用户不是管理员，显示时间警告并退出
     */
    if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator']) && $unixnow < $start_time) {
        $view_errors = "<center>";
        $view_errors .= "<h3>" . htmlspecialchars($MSG_CONTEST_ID, ENT_QUOTES, 'UTF-8') . " : " . htmlspecialchars($view_cid, ENT_QUOTES, 'UTF-8') . " - " . htmlspecialchars($view_title, ENT_QUOTES, 'UTF-8') . "</h3>";
        $view_errors .= "<p>" . htmlspecialchars($view_description, ENT_QUOTES, 'UTF-8') . "</p>";
        $view_errors .= "<br>";
        $view_errors .= "<span class=text-success>" . htmlspecialchars($MSG_TIME_WARNING, ENT_QUOTES, 'UTF-8') . "</span>";
        $view_errors .= "</center>";
        $view_errors .= "<br><br>";

        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }
}

/**
 * 处理限时比赛功能
 * 检查比赛描述中是否包含限时关键词，实现个人限时比赛功能
 */
if (!isset($OJ_CONTEST_LIMIT_KEYWORD)) $OJ_CONTEST_LIMIT_KEYWORD = "限时";
if ($contest_ok && str_contains($view_description, $OJ_CONTEST_LIMIT_KEYWORD) && isset($_SESSION[$OJ_NAME . "_user_id"])) {
    //echo "<!-- 个人限时赛  -->";
    $contest_limit_minutes = intval(getPartByMark($view_description, $OJ_CONTEST_LIMIT_KEYWORD, "分钟"));  //允许比赛描述中用 "限时xx分钟" 规定个人做题时间。
    if ($contest_limit_minutes == 0) $contest_limit_minutes = 120;
    //echo "<!-- $contest_limit_minutes mins -->";
    $user_id = $_SESSION[$OJ_NAME . "_user_id"];
    $first_login_contest = mysql_query_cache("select time from loginlog where user_id=? and password=?", $user_id, "c" . $cid);
    if (empty($first_login_contest)) {
        //echo "<!-- 首次访问  -->";
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty(trim($_SERVER['HTTP_X_FORWARDED_FOR']))) {
            $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR'];
            $tmp_ip = explode(',', $REMOTE_ADDR);
            $ip = (htmlentities($tmp_ip[0], ENT_QUOTES, "UTF-8"));
        } else if (isset($_SERVER['HTTP_X_REAL_IP']) && !empty(trim($_SERVER['HTTP_X_REAL_IP']))) {
            $REMOTE_ADDR = $_SERVER['HTTP_X_REAL_IP'];
            $tmp_ip = explode(',', $REMOTE_ADDR);
            $ip = (htmlentities($tmp_ip[0], ENT_QUOTES, "UTF-8"));
        }
        $sql = "INSERT INTO `loginlog`(user_id,password,ip,time) VALUES(?,?,?,NOW())";
        mysql_query_cache($sql, $user_id, "c" . $cid, $ip);
        $first_login_contest = time();
    } else {
        $first_login_contest = strtotime($first_login_contest[0][0]);
    }
    $unixnow = time();
    //echo "<!-- $unixnow - $first_login_contest = ".($unixnow-$first_login_contest)." -->";
    if ($unixnow - $first_login_contest >= $contest_limit_minutes * 60 && !isset($_SESSION[$OJ_NAME . "_m" . $cid]) && !isset($_SESSION[$OJ_NAME . "_administrator"])) {
        $contest_ok = false;

        $view_errors = "<center>";
        $view_errors .= "<h3>" . htmlspecialchars($MSG_CONTEST_ID, ENT_QUOTES, 'UTF-8') . " : " . htmlspecialchars($view_cid, ENT_QUOTES, 'UTF-8') . " - " . htmlspecialchars($view_title, ENT_QUOTES, 'UTF-8') . "</h3>";
        $view_errors .= "<p>" . htmlspecialchars($view_description, ENT_QUOTES, 'UTF-8') . "</p>";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    } else {
        $time_left = $contest_limit_minutes * 60 - ($unixnow - $first_login_contest);
        if ($time_left > 0)
            $view_description .= "<br><span id='time_left'>" . htmlspecialchars($MSG_LeftTime, ENT_QUOTES, 'UTF-8') . ":" . intval($time_left / 60) . " " . htmlspecialchars($MSG_MINUTES, ENT_QUOTES, 'UTF-8') . ($time_left % 60) . htmlspecialchars($MSG_SECONDS, ENT_QUOTES, 'UTF-8') . "<span>";
    }

};


/**
 * 处理比赛访问被拒绝的情况
 * 显示错误信息、排行榜链接和密码输入表单
 */
if (!$contest_ok) {
    $view_errors = "<center>";
    $view_errors .= "<h3>" . htmlspecialchars($MSG_CONTEST_ID, ENT_QUOTES, 'UTF-8') . " : " . htmlspecialchars($view_cid, ENT_QUOTES, 'UTF-8') . " - " . htmlspecialchars($view_title, ENT_QUOTES, 'UTF-8') . "</h3>";
    $view_errors .= "<p>" . htmlspecialchars($view_description, ENT_QUOTES, 'UTF-8') . "</p>";
    $view_errors .= "<span class=text-danger>" . htmlspecialchars($MSG_PRIVATE_WARNING, ENT_QUOTES, 'UTF-8') . "</span>";

    $view_errors .= "<br><br>";

    $view_errors .= "<div class='btn-group'>";
    $view_errors .= "<a href=contestrank.php?cid=" . intval($view_cid) . " class='btn btn-primary'>" . htmlspecialchars($MSG_STANDING, ENT_QUOTES, 'UTF-8') . "</a>";
    $view_errors .= "<a href=contestrank-oi.php?cid=" . intval($view_cid) . " class='btn btn-primary'>OI" . htmlspecialchars($MSG_STANDING, ENT_QUOTES, 'UTF-8') . "</a>";
    $view_errors .= "<a href=conteststatistics.php?cid=" . intval($view_cid) . " class='btn btn-primary'>" . htmlspecialchars($MSG_STATISTICS, ENT_QUOTES, 'UTF-8') . "</a>";
    $view_errors .= "</div>";

    $view_errors .= "<br><br>";
    $view_errors .= "<table align=center width=80%>";
    $view_errors .= "<tr align='center'>";
    $view_errors .= "<td>";
    $view_errors .= "<form class=form-inline method=post action=contest.php?cid=" . intval($view_cid) . ">";
    $view_errors .= "<input class=input-mini type=password name=password value='' placeholder='" . htmlspecialchars($MSG_CONTEST, ENT_QUOTES, 'UTF-8') . "-" . htmlspecialchars($MSG_PASSWORD, ENT_QUOTES, 'UTF-8') . "'>";
    $view_errors .= "<button class='form-control'>" . htmlspecialchars($MSG_SUBMIT, ENT_QUOTES, 'UTF-8') . "</button>";
    $view_errors .= "</form>";
    $view_errors .= "</td>";
    $view_errors .= "</tr>";
    $view_errors .= "</table>";
    $view_errors .= "<br>";

    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}
