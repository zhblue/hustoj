<?php
$cache_time = 10;
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/my_func.inc.php');
require_once('./include/setlang.php');
if (!isset($OJ_FRIENDLY_LEVEL)) $OJ_FRIENDLY_LEVEL = 0;

// 检查考试或现场比赛期间是否允许发送邮件
if (
    $OJ_FRIENDLY_LEVEL < 2
    && (
        (isset($OJ_EXAM_CONTEST_ID) && $OJ_EXAM_CONTEST_ID > 0) ||
        (isset($OJ_ON_SITE_CONTEST_ID) && $OJ_ON_SITE_CONTEST_ID > 0)
    )
) {
    header("Content-type: text/html; charset=utf-8");
    $view_errors = $MSG_MAIL_NOT_ALLOWED_FOR_EXAM;
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit ();
}
// 检查邮件功能是否启用
if (isset($OJ_MAIL) && !$OJ_MAIL) {
    header("Content-type: text/html; charset=utf-8");
    $view_errors = $MSG_NO_MAIL_HERE;
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit ();
}
$view_title = $MSG_MAIL;
$to_user = "";
$title = "";
if (isset($_GET['to_user'])) {
    $to_user = htmlentities($_GET['to_user'], ENT_QUOTES, "UTF-8");
}
if (isset($_GET['title'])) {
    $title = htmlentities($_GET['title'], ENT_QUOTES, "UTF-8");
}

// 检查用户是否已登录
if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $view_errors = "<a href=loginpage.php>$MSG_Login</a>";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit ();
}
require_once("./include/db_info.inc.php");
require_once("./include/const.inc.php");
if (isset($OJ_LANG)) {
    require_once("./lang/$OJ_LANG.php");
    if (file_exists("./faqs.$OJ_LANG.php")) {
        $OJ_FAQ_LINK = "faqs.$OJ_LANG.php";
    }
}
echo "<title>$MSG_MAIL</title>";


// 查看邮件功能：根据邮件ID获取邮件内容
$view_content = false;
if (isset($_GET['vid'])) {
    $vid = intval($_GET['vid']);
    $sql = "SELECT * FROM `mail` WHERE `mail_id`=?
								and (to_user=? or from_user=?)";
    $result = pdo_query($sql, $vid, $_SESSION[$OJ_NAME . '_' . 'user_id'], $_SESSION[$OJ_NAME . '_' . 'user_id']);

    if ($result && count($result) > 0) {
        $row = $result[0];
        $from_user = $row['from_user'];
        $to_user = $row['to_user'];
        $view_title = $row['title'];
        $view_content = $row['content'];

        $sql = "update `mail` set new_mail=0 WHERE `mail_id`=?";
        pdo_query($sql, $vid);
    } else {
        // 邮件不存在或无权限访问
        header("Content-type: text/html; charset=utf-8");
        $view_errors = $MSG_NO_SUCH_MAIL;
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit();
    }
}
// 发送邮件功能：处理POST请求发送邮件
//send mail
if (isset($_POST['to_user'])) {
    $to_user = trim($_POST['to_user']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $from_user = $_SESSION[$OJ_NAME . '_' . 'user_id'];

    if (false) {
        $to_user = stripslashes($to_user);
        $title = stripslashes($title);
        $content = stripslashes($content);
    }
    $title = RemoveXSS($title);
    $content = RemoveXSS($content);

    // 验证输入
    if (empty($to_user) || empty($title) || empty($content)) {
        $view_errors = $MSG_PLEASE_INPUT_REQUIRED_FIELDS;
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit();
    }

    // 检查发送者和接收者是否为管理员或代码审查员
    $sql = "select user_id from privilege where (rightstr='source_browser' or rightstr='administrator') and user_id=?";
    $from_res = pdo_query($sql, $from_user);
    $to_res = pdo_query($sql, $to_user);

    if (($from_res && count($from_res) < 1) && ($to_res && count($to_res) < 1)) {
        $view_errors = $MSG_MAIL_CAN_ONLY_BETWEEN_TEACHER_AND_STUDENT;
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit ();
    } else {
        $sql = "insert into mail(to_user,from_user,title,content,in_date)
						values(?,?,?,?,now())";

        $result = pdo_query($sql, $to_user, $from_user, $title, $content);
        if (!$result) {
            $view_title = "Not Mailed!";
        } else {
            $view_title = "Mailed!";
        }
    }
}
// 获取邮件列表：查询当前用户的收件和发件记录
$sql = "SELECT * FROM `mail` WHERE to_user=? or from_user=?
					order by mail_id desc";
$result = pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id'], $_SESSION[$OJ_NAME . '_' . 'user_id']);
$view_mail = array();
$i = 0;
foreach ($result as $row) {
    $view_mail[$i][0] = $row['mail_id'];
    if ($row['new_mail']) $view_mail[$i][0] .= "<span class=red>New</span>";
    $view_mail[$i][1] = "<a href='mail.php?vid=" . $row['mail_id'] . "'>" .
        $row['from_user'] . ":" . $row['title'] . "</a>";
    $view_mail[$i][2] = $row['in_date'];
    $i++;
}


/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/mail.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');

