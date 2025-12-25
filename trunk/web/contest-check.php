<?php
require_once("include/curl.php");
$unixnow = time();
$cid = abs(intval($_GET['cid']));
if (isset($_SESSION[$OJ_NAME . "_user_id"]))
    $user_id = $_SESSION[$OJ_NAME . "_user_id"];
else
    $user_id = "";
if ($cid < 0) $cid = -$cid;
$view_cid = $cid;
//print $cid;

/**
 * 验证比赛是否有效并检查用户权限
 * 获取比赛基本信息，验证比赛状态、权限、时间等条件
 * 处理比赛密码验证、IP子网限制、私人比赛权限等
 * 支持个人限时赛功能，限制用户答题时间
 * 根据验证结果决定是否允许用户访问比赛
 */

//check contest valid
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
$contest_ok = true;
$password = "";

if (isset($_POST['password']))
    $password = $_POST['password'];

// 检查IP是否在比赛允许的子网范围内
if (!in_subnet_of_contest($ip, $cid)) {
    $contest_ok = false;
    $view_description = "Not in $MSG_SUBNET $subnet";
}

// 检查比赛是否存在或是否已关闭（非管理员无法访问已关闭比赛）
if ($rows_cnt == 0 || ($row['defunct'] == 'Y' && !isset($_SESSION[$OJ_NAME . '_administrator']))) {
    $view_title = "比赛已经关闭!";
    $contest_ok = false;
} else {

    $view_private = $row['private'];
    $view_contest_creator = $row['user_id'];
    if ($password != "" && $password == $row['password'])
        $_SESSION[$OJ_NAME . '_' . 'c' . $cid] = true;

    // 检查私人比赛权限
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

    // 管理员和比赛创建者拥有特殊权限
    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']))
        $contest_ok = true;

    // 判断比赛是否已经结束
    if ($unixnow > $end_time) $contest_is_over = true;    // 已经结束的比赛，按练习方式提交
    else $contest_is_over = false;

    // 检查比赛是否还未开始，未开始则显示错误页面
    if (!isset($_SESSION[$OJ_NAME . '_' . 'administrator']) && $unixnow < $start_time) {
        $view_errors = "<center>";
        $view_errors .= "<h3>$MSG_CONTEST_ID : $view_cid - $view_title</h3>";
        $view_errors .= "<p>$view_description</p>";
        $view_errors .= "<br>";
        $view_errors .= "<span class=text-success>$MSG_TIME_WARNING</span>";
        $view_errors .= "</center>";
        $view_errors .= "<br><br>";

        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }
}

// 处理个人限时赛功能
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
        $ip = ($_SERVER['REMOTE_ADDR']);
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
        $view_errors .= "<h3>$MSG_CONTEST_ID : $view_cid - $view_title</h3>";
        $view_errors .= "<p>$view_description</p>";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    } else {
        $time_left = $contest_limit_minutes * 60 - ($unixnow - $first_login_contest);
        if ($time_left > 0)
            $view_description .= "<br><span id='time_left'>$MSG_LeftTime :" . intval($time_left / 60) . " $MSG_MINUTES" . ($time_left % 60) . $MSG_SECONDS . "<span>";
    }

};

// 如果比赛验证失败，显示错误页面和密码输入表单
if (!$contest_ok) {
    $view_errors = "<center>";
    $view_errors .= "<h3>$MSG_CONTEST_ID : $view_cid - $view_title</h3>";
    $view_errors .= "<p>$view_description</p>";
    $view_errors .= "<span class=text-danger>$MSG_PRIVATE_WARNING</span>";

    $view_errors .= "<br><br>";

    $view_errors .= "<div class='btn-group'>";
    $view_errors .= "<a href=contestrank.php?cid=$view_cid class='btn btn-primary'>$MSG_STANDING</a>";
    $view_errors .= "<a href=contestrank-oi.php?cid=$view_cid class='btn btn-primary'>OI$MSG_STANDING</a>";
    $view_errors .= "<a href=conteststatistics.php?cid=$view_cid class='btn btn-primary'>$MSG_STATISTICS</a>";
    $view_errors .= "</div>";

    $view_errors .= "<br><br>";
    $view_errors .= "<table align=center width=80%>";
    $view_errors .= "<tr align='center'>";
    $view_errors .= "<td>";
    $view_errors .= "<form class=form-inline method=post action=contest.php?cid=$cid>";
    $view_errors .= "<input class=input-mini type=password name=password value='' placeholder=$MSG_CONTEST-$MSG_PASSWORD>";
    $view_errors .= "<button class='form-control'>$MSG_SUBMIT</button>";
    $view_errors .= "</form>";
    $view_errors .= "</td>";
    $view_errors .= "</tr>";
    $view_errors .= "</table>";
    $view_errors .= "<br>";

    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

