<?php
require_once('./include/db_info.inc.php');
require_once('./include/my_func.inc.php');
require_once('./include/setlang.php');
if (!isset($OJ_FRIENDLY_LEVEL)) $OJ_FRIENDLY_LEVEL = 0;

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


//view mail
$view_content = false;
if (isset($_GET['vid'])) {
    $vid = intval($_GET['vid']);
    $sql = "SELECT * FROM `mail` WHERE `mail_id`=?
								and (to_user=? or from_user=?)";
    $result = pdo_query($sql, $vid, $_SESSION[$OJ_NAME . '_' . 'user_id'], $_SESSION[$OJ_NAME . '_' . 'user_id']);
    $row = $result[0];
    $from_user = $row['from_user'];
    $to_user = $row['to_user'];
    $view_title = $row['title'];
    $view_content = $row['content'];


    $sql = "update `mail` set new_mail=0 WHERE `mail_id`=?";
    pdo_query($sql, $vid);

}
//send mail page
//send mail
if (isset($_POST['to_user'])) {
    $to_user = $_POST ['to_user'];
    $title = $_POST ['title'];
    $content = $_POST ['content'];
    $from_user = $_SESSION[$OJ_NAME . '_' . 'user_id'];
    if (false) {
        $to_user = stripslashes($to_user);
        $title = stripslashes($title);
        $content = stripslashes($content);
    }
    $title = RemoveXSS($title);

    $sql = "select 1 from privilege where (rightstr='source_browser' or rightstr='administrator') and (user_id=? or user_id=? )";
    $res = pdo_query($sql, $from_user, $to_user);
    if ($res && count($res) < 1) {
        //$view_title= "Mail can only send to or from a Code Reviewer!";
        $view_errors = $MSG_MAIL_CAN_ONLY_BETWEEN_TEACHER_AND_STUDENT;
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit ();
    } else {
        if ($res)
            $sql = "insert into mail(to_user,from_user,title,content,in_date)
						values(?,?,?,?,now())";

        if (!pdo_query($sql, $to_user, $from_user, $title, $content)) {
            $view_title = "Not Mailed!";
        } else {
            $view_title = "Mailed!";
        }
    }
}
//list mail
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


