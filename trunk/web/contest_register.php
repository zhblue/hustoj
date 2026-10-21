<?php
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
require_once('./include/const.inc.php');
require_once('./include/my_func.inc.php');

if (!isset($MSG_REGISTERABLE)) $MSG_REGISTERABLE = $MSG_Private;
if (!isset($MSG_CONTEST_REGISTERED)) $MSG_CONTEST_REGISTERED = $MSG_REGISTER;
if (!isset($MSG_REGISTERABLE_ONLY)) $MSG_REGISTERABLE_ONLY = $MSG_REGISTERABLE;
if (!isset($MSG_REGISTER_CLOSED)) $MSG_REGISTER_CLOSED = $MSG_CONTEST_CLOSED;
if (!isset($MSG_CONTEST_ID_INVALID)) $MSG_CONTEST_ID_INVALID = $MSG_CONTEST;
if (!isset($MSG_CONTEST_NOT_FOUND)) $MSG_CONTEST_NOT_FOUND = $MSG_CONTEST;
$view_title = $MSG_REGISTERABLE . ' - ' . $OJ_NAME;
$cid = isset($_GET['cid']) ? intval($_GET['cid']) : (isset($_POST['cid']) ? intval($_POST['cid']) : 0);
$view_error = '';
$view_message = '';
$view_registered = false;
$view_contest = array();
$view_login_required = false;

if ($cid <= 0) {
    $view_error = $MSG_CONTEST_ID_INVALID;
} else {
    $result = pdo_query("SELECT `contest_id`,`title`,`description`,`start_time`,`end_time`,`private`,`defunct`,`user_id` FROM `contest` WHERE `contest_id`=? LIMIT 1", $cid);
    if (empty($result)) {
        $view_error = $MSG_CONTEST_NOT_FOUND;
    } else {
        $view_contest = $result[0];
        $view_private = intval($view_contest['private']);
        $view_start_time = $view_contest['start_time'];
        $view_end_time = $view_contest['end_time'];
        $view_start_timestamp = strtotime($view_start_time);
        $view_end_timestamp = strtotime($view_end_time);
        $now = time();
        $user_id = isset($_SESSION[$OJ_NAME . '_user_id']) ? $_SESSION[$OJ_NAME . '_user_id'] : '';

        if ($view_private != 3) {
            $view_error = $MSG_REGISTERABLE_ONLY;
        } else if ($view_contest['defunct'] == 'Y') {
            $view_error = $MSG_CONTEST_CLOSED;
        } else if ($user_id == '') {
            $view_error = $MSG_Login;
            $view_login_required = true;
        } else {
            $privilege_result = pdo_query("SELECT COUNT(1) FROM `privilege` WHERE `user_id`=? AND `rightstr`=? AND `defunct`='N'", $user_id, 'c' . $cid);
            $view_registered = !empty($privilege_result) && intval($privilege_result[0][0]) > 0;
            if ($view_registered) {
                $_SESSION[$OJ_NAME . '_c' . $cid] = true;
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                require_once('./include/check_post_key.php');
                if ($now >= $view_end_timestamp) {
                    $view_error = $MSG_CONTEST_CLOSED;
                } else if ($now >= $view_start_timestamp) {
                    $view_error = $MSG_REGISTER_CLOSED;
                } else if (!$view_registered) {
                    pdo_query("INSERT INTO `privilege` (`user_id`,`rightstr`) VALUES(?,?)", $user_id, 'c' . $cid);
                    $_SESSION[$OJ_NAME . '_c' . $cid] = true;
                    header('Location: contest_register.php?cid=' . $cid . '&registered=1');
                    exit(0);
                }
            }
            if ($view_registered && isset($_GET['registered'])) $view_message = $MSG_SUCCESS;
        }
    }
}

require("template/" . $OJ_TEMPLATE . "/contest_register.php");
if (file_exists('./include/cache_end.php')) require_once('./include/cache_end.php');
?>
