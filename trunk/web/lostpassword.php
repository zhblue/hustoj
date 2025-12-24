<?php
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = "Welcome To Online Judge";

require_once("./include/const.inc.php");
require_once("./include/my_func.inc.php");

// 获取用户提交的用户ID和邮箱信息，并进行基本验证
$lost_user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
$lost_email = isset($_POST['email']) ? trim($_POST['email']) : '';

// 验证输入格式
if (!empty($lost_user_id) && !preg_match('/^[a-zA-Z0-9_]{3,20}$/', $lost_user_id)) {
    $error_msg = "用户名格式不正确";
    require("template/" . $OJ_TEMPLATE . "/lostpassword.php");
    exit;
}

if (!empty($lost_email) && !filter_var($lost_email, FILTER_VALIDATE_EMAIL)) {
    $error_msg = "邮箱格式不正确";
    require("template/" . $OJ_TEMPLATE . "/lostpassword.php");
    exit;
}

if (isset($_POST['vcode'])) {
    $vcode = trim($_POST['vcode']);
} else {
    $vcode = '';
}

// 验证验证码是否正确
if ($lost_user_id && ($vcode != $_SESSION[$OJ_NAME . '_' . "vcode"] || $vcode == "" || $vcode == null)) {
    echo "<script language='javascript'>\n";
    echo "alert('Verify Code Wrong!');\n";
    echo "history.go(-1);\n";
    echo "</script>";
    exit(0);
}

// 查询数据库验证用户信息
if (!empty($lost_user_id)) {
    $sql = "SELECT `email` FROM `users` WHERE `user_id`=? and defunct='N' ";
    $result = pdo_query($sql, $lost_user_id);
    $row = !empty($result) ? $result[0] : null;

    // 验证用户邮箱是否匹配并发送密码重置邮件
    if ($row && $row['email'] === $lost_email && strpos($lost_email, '@') !== false) {
        $_SESSION[$OJ_NAME . '_' . 'lost_user_id'] = $lost_user_id;
        $_SESSION[$OJ_NAME . '_' . 'lost_key'] = getToken(16);

        require_once("include/email.class.php");     // 新版本的邮件发送信息请填写到这个文件中
        
        // 安全处理邮件内容，避免XSS
        $safe_user_id = htmlspecialchars($lost_user_id, ENT_QUOTES, 'UTF-8');
        $mailtitle = "OJ系统密码重置激活";//邮件主题
        $mailcontent = "$safe_user_id:\n您好！\n您在${OJ_NAME}系统选择了找回密码服务,为了验证您的身份,请将下面字串输入口令重置页面以确认身份:" . $_SESSION[$OJ_NAME . '_' . 'lost_key'] . "\n 注意：验证通过后，上面这个字符串将成为您的新密码!\n\n\nHUSTOJ在线评测系统";//邮件内容

        // 根据SMTP配置选择不同的邮件发送方式
        if ($SMTP_USER == "mailer@qq.com") {
            $email_param = urlencode($row['email']);
            $token_param = urlencode($_SESSION[$OJ_NAME . '_' . 'lost_key']);
            file_get_contents("http://demo.hustoj.com/email-proxy.php?email=" . $email_param . "&token=" . $token_param);
        } else {
            email($row['email'], $mailtitle, $mailcontent);
        }

        require("template/" . $OJ_TEMPLATE . "/lostpassword2.php");

    } else {
        if ($row && $row['email'] != $lost_email) {
            $error_msg = "用户名与Email不匹配";
        } else {
            $error_msg = "用户不存在或邮箱不匹配";
        }
        /////////////////////////Template
        require("template/" . $OJ_TEMPLATE . "/lostpassword.php");
    }
} else {
    $error_msg = "请输入用户名";
    require("template/" . $OJ_TEMPLATE . "/lostpassword.php");
}
/////////////////////////Common foot
