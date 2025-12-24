<?php
//$cache_time=10;
$OJ_CACHE_SHARE = false;
//require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = "Modify Userinfo";
//require_once("./include/check_post_key.php");
require_once("./include/my_func.inc.php");

/**
 * 检查是否为考试或现场竞赛模式，如果是则不允许修改用户信息
 * 在考试或现场竞赛期间禁止用户修改个人信息
 */
if (
    (isset($OJ_EXAM_CONTEST_ID) && $OJ_EXAM_CONTEST_ID > 0) ||
    (isset($OJ_ON_SITE_CONTEST_ID) && $OJ_ON_SITE_CONTEST_ID > 0)
) {
    $view_errors = $MSG_MODIFY_NOT_ALLOWED_FOR_EXAM;
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit ();
}

$err_str = "";
$err_cnt = 0;
$len;
$user_id = $_SESSION[$OJ_NAME . '_' . 'user_id'];

// 验证用户ID是否存在
if (empty($user_id)) {
    $err_str = $err_str . "User not logged in!";
    $err_cnt++;
} else {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $school = isset($_POST['school']) ? trim($_POST['school']) : '';
    $nick = isset($_POST['nick']) ? trim($_POST['nick']) : '';
    $len = strlen($nick);

    /**
     * 验证昵称长度，如果超过100字符则记录错误，如果为空则使用用户名作为昵称
     */
    if ($len > 100) {
        $err_str = $err_str . "$MSG_NICK $MSG_TOO_LONG !";
        $err_cnt++;
    } else if ($len == 0) $nick = $user_id;

    $password = isset($_POST['opassword']) ? $_POST['opassword'] : '';

    /**
     * 验证原密码是否正确
     * 从数据库查询用户原密码并进行验证
     */
    $sql = "SELECT `user_id`,`password` FROM `users` WHERE `user_id`=?";
    $result = pdo_query($sql, $user_id);
    $row = isset($result[0]) ? $result[0] : null;
    if ($row && pwCheck($password, $row['password'])) $rows_cnt = 1;
    else $rows_cnt = 0;

    if ($rows_cnt == 0) {
        $err_str = $err_str . "$MSG_OLD $MSG_PASSWORD $MSG_WRONG";
        $err_cnt++;
    }

    /**
     * 验证新密码强度和确认密码是否一致
     * 检查密码是否过于简单以及新密码与确认密码是否匹配
     */
    $len = isset($_POST['npassword']) ? strlen($_POST['npassword']) : 0;
    if ($len > 0) {
        if (too_simple($_POST['npassword'])) {
            $err_cnt++;
            $err_str = $err_str . "$MSG_PASSWORD $MSG_TOO_SIMPLE !\\n";
        } else if (strcmp($_POST['npassword'], $_POST['rptpassword']) != 0) {
            $err_str = $err_str . "$MSG_REPEAT_PASSWORD $MSG_DIFFERENT !";
            $err_cnt++;
        }
    }

    /**
     * 验证学校和邮箱长度限制
     * 检查学校和邮箱字段长度是否超过100字符
     */
    $len = isset($_POST['school']) ? strlen($_POST['school']) : 0;
    if ($len > 100) {
        $err_str = $err_str . "$MSG_SCHOOL $MSG_TOO_LONG!";
        $err_cnt++;
    }
    $len = isset($_POST['email']) ? strlen($_POST['email']) : 0;
    if ($len > 100) {
        $err_str = $err_str . "$MSG_EMAIL $MSG_TOO_LONG!";
        $err_cnt++;
    }

    // 验证邮箱格式
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err_str = $err_str . "$MSG_EMAIL $MSG_INVALID!";
        $err_cnt++;
    }

    /**
     * 检查用户ID、学校和昵称是否包含不良词汇
     * 防止用户使用不当内容作为个人信息
     */
    if (has_bad_words($user_id)) {
        $err_str = $err_str . $MSG_USER_ID . " $MSG_TOO_BAD!\\n";
        $err_cnt++;
    }
    if (has_bad_words($school)) {
        $err_str = $err_str . $MSG_SCHOOL . " $MSG_TOO_BAD!\\n";
        $err_cnt++;
    }
    if (has_bad_words($nick)) {
        $err_str = $err_str . $MSG_NICK . " $MSG_TOO_BAD!\\n";
        $err_cnt++;
    }
}

/**
 * 如果存在验证错误，显示错误信息并返回上一页
 */
if ($err_cnt > 0) {
    print "<script language='javascript'>\n";
    echo "alert('" . addslashes($err_str) . "');\n history.go(-1);\n</script>";
    exit(0);

}

/**
 * 生成新密码，如果未输入新密码则使用原密码重新加密
 */
if (isset($_POST['npassword']) && strlen($_POST['npassword']) == 0) $password = pwGen($_POST['opassword']);
else $password = pwGen($_POST['npassword']);

/**
 * 对用户输入进行HTML实体编码，防止XSS攻击
 */
$nick = htmlentities($nick, ENT_QUOTES, "UTF-8");
$school = (htmlentities($school, ENT_QUOTES, "UTF-8"));
$email = (htmlentities($email, ENT_QUOTES, "UTF-8"));

/**
 * 更新用户信息到数据库
 * 使用预处理语句防止SQL注入
 */
$sql = "UPDATE `users` SET"
    . "`password`=?,"
    . "`nick`=?,"    //注释此行  +   删除96行的,$nick +  注释97行   ->   禁用昵称修改
    . "`school`=?,"
    . "`email`=?"
    . "WHERE `user_id`=?";
//echo $sql;
//exit(0);
pdo_query($sql, $password, $nick, $school, $email, $user_id);

/**
 * 更新该用户在solution表中的昵称
 * 同步更新用户提交记录中的昵称信息
 */
if ($nick != "") {
    $sql = "update solution set nick=? where user_id=?";
    pdo_query($sql, $nick, $user_id);
}
header("Location: ./");
