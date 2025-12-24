<?php
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = "Welcome To Online Judge";

require_once("./include/const.inc.php");
require_once("./include/my_func.inc.php");

// 验证请求方法
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Method not allowed');
}

// 获取用户提交的表单数据并进行基本验证
$lost_user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
$lost_key = isset($_POST['lost_key']) ? trim($_POST['lost_key']) : '';
$vcode = isset($_POST['vcode']) ? trim($_POST['vcode']) : '';

// 输入验证
if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $lost_user_id)) {
    $view_errors = "Invalid user ID format";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit;
}

if (strlen($lost_key) < 6 || strlen($lost_key) > 128) {
    $view_errors = "Invalid key format";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit;
}

if (strlen($vcode) < 1 || strlen($vcode) > 10) {
    $view_errors = "Invalid verification code";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit;
}

// 验证验证码是否正确，如果验证码错误则弹出提示并返回上一页
if ($lost_user_id == $_SESSION[$OJ_NAME . '_' . 'lost_user_id'] && 
    ($vcode != $_SESSION[$OJ_NAME . '_' . "vcode"] || $vcode == "" || $vcode == null)) {
    echo "<script language='javascript'>\n";
    echo "alert('Verify Code Wrong!');\n";
    echo "history.go(-1);\n";
    echo "</script>";
    exit(0);
}


// 构建更新用户密码的SQL语句
$sql = "UPDATE `users` SET password=? WHERE `user_id`=?";

// 验证会话中的用户ID和密钥是否与提交的数据匹配，如果匹配则更新密码，否则重置失败
if (
    isset($_SESSION[$OJ_NAME . '_' . 'lost_user_id']) &&
    isset($_SESSION[$OJ_NAME . '_' . 'lost_key']) &&
    $_SESSION[$OJ_NAME . '_' . 'lost_user_id'] == $lost_user_id &&
    $_SESSION[$OJ_NAME . '_' . 'lost_key'] == $lost_key
) {
    $result = pdo_query($sql, pwGen($lost_key), $lost_user_id);
    
    // 重置成功后清除会话数据，防止重复使用
    unset($_SESSION[$OJ_NAME . '_' . 'lost_user_id']);
    unset($_SESSION[$OJ_NAME . '_' . 'lost_key']);
    unset($_SESSION[$OJ_NAME . '_' . 'vcode']);
    
    $view_errors = "Password Reseted to the key you've just inputed.Click <a href=index.php>Here</a> to login!";
} else {
    $view_errors = "Password Reset Fail";
}

require("template/" . $OJ_TEMPLATE . "/error.php");
/////////////////////////Template

/////////////////////////Common foot
