<?php
require_once("./include/db_info.inc.php");
require_once("./include/my_func.inc.php");
require_once('./include/setlang.php');
if (isset($OJ_CSRF) && $OJ_CSRF) require_once("./include/csrf_check.php");

/**
 * 处理用户登录验证的脚本
 * 支持Cookie长期登录和常规登录两种方式
 * 包含验证码验证、登录失败限制、权限检查等功能
 */

// 初始化登录状态变量
$use_cookie = false;
$login = false;

// 安全获取客户端IP
function get_client_ip() {
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        // 取第一个IP（处理代理链）
        $ip = explode(',', $ip)[0];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // 验证IP格式
    $ip = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    return $ip ? $ip : '0.0.0.0';
}

$ip = get_client_ip();

// 检查是否使用Cookie进行长期登录
if ($OJ_LONG_LOGIN && isset($_COOKIE[$OJ_NAME . "_user"]) && isset($_COOKIE[$OJ_NAME . "_check"])) {
    $C_check = $_COOKIE[$OJ_NAME . "_check"];
    $C_user = $_COOKIE[$OJ_NAME . "_user"];
    $use_cookie = true;

    // 验证Cookie的完整性
    $C_num = strlen($C_check) - 1;
    $C_num = ($C_num * $C_num) % 7;
    if ($C_check[strlen($C_check) - 1] != $C_num) {
        setcookie($OJ_NAME . "_check", "", 0, '/', '', true, true);
        setcookie($OJ_NAME . "_user", "", 0, '/', '', true, true);
        echo "<script>\n alert('Cookie失效或错误!(-1)'); \n history.go(-1); \n </script>";
        exit(0);
    }

    // 从数据库获取用户密码和访问时间信息
    $C_info = pdo_query("SELECT `password`,`accesstime` FROM `users` WHERE `user_id`=? and defunct='N'", $C_user)[0];
    if (!$C_info) {
        setcookie($OJ_NAME . "_check", "", 0, '/', '', true, true);
        setcookie($OJ_NAME . "_user", "", 0, '/', '', true, true);
        echo "<script>\n alert('Cookie失效或错误!(-1)'); \n history.go(-1); \n </script>";
        exit(0);
    }
    $C_len = strlen($C_info['accesstime']);

    // 解密验证Cookie信息
    $C_res = '';
    for ($i = 0; $i < strlen($C_info['password']); $i++) {
        $tp = ord($C_info['password'][$i]);
        $C_res .= chr(39 + ($tp * $tp + ord($C_info['accesstime'][$i % $C_len]) * $tp) % 88);
    }

    // 验证Cookie哈希值
    if (substr($C_check, 0, -1) == sha1($C_res))
        $login = $C_user;
    else {
        setcookie($OJ_NAME . "_check", "", 0, '/', '', true, true);
        setcookie($OJ_NAME . "_user", "", 0, '/', '', true, true);
        echo "<script>\n alert('Cookie失效或错误!(-2)'); \n history.go(-1); \n </script>";
        exit(0);
    }
}

$vcode = "";
if (!$use_cookie) {
    // 获取验证码
    if (isset($_POST['vcode'])) $vcode = trim($_POST['vcode']);

    // 验证码验证
    if ($OJ_VCODE && ($vcode != $_SESSION[$OJ_NAME . '_' . "vcode"] || $vcode == "" || $vcode == null)) {
        $_SESSION[$OJ_NAME . '_' . "vfail"] = true;
        echo "<script language='javascript'>\n";
        echo "alert('Verify Code Wrong!');\n";
        echo "history.go(-1);\n";
        echo "</script>";
        exit(0);
    }

    $view_errors = "";
    require_once("./include/login-" . $OJ_LOGIN_MOD . ".php");
    $user_id = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // 检查登录失败次数限制
    $fiveMinutesAgo = date('Y-m-d H:i:s', strtotime("-5 minutes"));
    $failed = pdo_query("SELECT
                        (SELECT COUNT(1) FROM loginlog WHERE user_id=? AND password='login fail' AND time>=?) as user_fail,
                        (SELECT COUNT(1) FROM loginlog WHERE ip=? AND password='login fail' AND time>=?) as ip_fail;", $user_id, $fiveMinutesAgo, $ip, $fiveMinutesAgo);
    if (isset($OJ_LOGIN_FAIL_LIMIT) && ($OJ_LOGIN_FAIL_LIMIT > 0) && ($failed[0]['user_fail'] > $OJ_LOGIN_FAIL_LIMIT || $failed[0]['ip_fail'] > $OJ_LOGIN_FAIL_LIMIT * 4)) {
        $view_errors = "Failed login too frequently!";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }

    // 执行登录验证
    $login = check_login($user_id, $password);
}

// 登录成功后的处理
if ($login) {
    // 提取组名
    session_regenerate_id(true);
    $group_name = "";
    $group_row = pdo_query("select group_name,nick from users where user_id=?", $login);
    if (!empty($group_row)) {
        $group_name = $group_row[0]['group_name'];
        $_SESSION[$OJ_NAME . '_nick'] = htmlspecialchars($group_row[0]['nick'], ENT_QUOTES, 'UTF-8');
        $_SESSION[$OJ_NAME . '_group_name'] = htmlspecialchars($group_name, ENT_QUOTES, 'UTF-8');
    }

    // 根据用户组获取权限信息
    if (empty($group_name)) {
        $sql = "SELECT * FROM `privilege` WHERE `user_id`=?";
        $_SESSION[$OJ_NAME . '_' . 'user_id'] = $login;
        $result = pdo_query($sql, $login);
    } else {  // 如果去掉下面的 and rightstr like 'c%' 则能获得该组的所有权限，如：在teacher组可以有teacher用户的所有权限。管理方便，但需谨慎使用。
        $sql = "SELECT * FROM `privilege` WHERE `user_id`=? or (user_id=? and rightstr like 'c%' )";
        $_SESSION[$OJ_NAME . '_' . 'user_id'] = $login;
        $result = pdo_query($sql, $login, $group_name);
    }

    // 对用户权限进行session转存
    foreach ($result as $row) {
        if (isset($row['valuestr']))
            $_SESSION[$OJ_NAME . '_' . $row['rightstr']] = $row['valuestr'];
        else
            $_SESSION[$OJ_NAME . '_' . $row['rightstr']] = true;
    }

    // VIP用户权限处理：VIP mark can access all [VIP] marked contest vip权限用户可以参加所有标记了[VIP]字样的比赛
    if (isset($_SESSION[$OJ_NAME . '_vip'])) {
        $sql = "select contest_id from contest where title like '%[VIP]%'";
        $result = pdo_query($sql);
        foreach ($result as $row) {
            $_SESSION[$OJ_NAME . '_c' . $row['contest_id']] = true;
        }
    }

    // 更新用户访问时间
    $sql = "update users set accesstime=now() where user_id=?";
    $result = pdo_query($sql, $login);

    // 设置长期登录Cookie
    if ($OJ_LONG_LOGIN) {
        $C_info = pdo_query("SELECT `password` , `accesstime` FROM`users` WHERE`user_id`=? and defunct='N'", $login)[0];
        if ($C_info) {
            $C_len = strlen($C_info['accesstime']);
            $C_res = "";
            for ($i = 0; $i < strlen($C_info['password']); $i++) {
                $tp = ord($C_info['password'][$i]);
                $C_res .= chr(39 + ($tp * $tp + ord($C_info['accesstime'][$i % $C_len]) * $tp) % 88);
            }
            $C_res = sha1($C_res);
            $C_time = time() + 86400 * $OJ_KEEP_TIME;
            setcookie($OJ_NAME . "_user", $login, $C_time, '/', '', true, true);
            setcookie($OJ_NAME . "_check", $C_res . (strlen($C_res) * strlen($C_res)) % 7, $C_time, '/', '', true, true);
        }
    }

    // 根据用户权限跳转到相应页面
    echo "<script language='javascript'>\n";
    if (isset($_SESSION[$OJ_NAME . "_administrator"]))
        echo "window.location.href='admin';\n";
    else if (isset($_SESSION[$OJ_NAME . "_contest_creator"]))
        echo "window.location.href='contest.php?my';\n";
    else if ($OJ_NEED_LOGIN)
        echo "window.location.href='index.php';\n";
    else
        echo "setTimeout('history.go(-2)',500);\n";
    echo "</script>";
} else {
    // 登录失败记录日志
    $sql = "INSERT INTO `loginlog`(user_id,password,ip,time) VALUES(?,'login fail',?,NOW())";
    pdo_query($sql, $user_id ?? '', $ip);

    // 记录登录失败日志
    if (isset($OJ_LOG_ENABLED) && $OJ_LOG_ENABLED && isset($logger)) {
        $params = json_encode($_REQUEST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $logger->info($params);
    }

    // 显示错误信息
    if ($view_errors) {
        require("template/" . $OJ_TEMPLATE . "/error.php");
    } else {
        echo "<script language='javascript'>\n";
        echo "alert('UserName or Password Wrong!');\n";
        echo "history.go(-1);\n";
        echo "</script>";
    }
}
