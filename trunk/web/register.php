<?php
require_once("./include/db_info.inc.php");
if (isset($OJ_REGISTER) && !$OJ_REGISTER) exit(0);
require_once("./include/my_func.inc.php");
require_once('./include/setlang.php');
require_once("./include/email.class.php");     // 新版本的邮件发送信息请填写到db_info.inc.php
if (isset($OJ_CSRF) && $OJ_CSRF) require_once("./include/csrf_check.php");

// 初始化错误信息和计数器
$err_str = "";
$err_cnt = 0;
$len;

// 获取并验证用户提交的注册信息
$user_id = trim($_POST['user_id']);
$len = mb_strlen($user_id);
$email = trim($_POST['email']);
$school = trim($_POST['school']);

// 如果启用验证码，则获取验证码
if (isset($OJ_VCODE) && $OJ_VCODE) $vcode = trim($_POST['vcode']);

// 验证验证码是否正确
if ($OJ_VCODE && ($vcode != $_SESSION[$OJ_NAME . '_' . "vcode"] || $vcode == "" || $vcode == null)) {
    $_SESSION[$OJ_NAME . '_' . "vcode"] = null;
    $err_str = $err_str . "Verification Code Wrong!\\n";
    $err_cnt++;
    $_SESSION[$OJ_NAME . '_' . "vfail"] = true;
}

// 检查登录模块是否为hustoj，如果不是则禁止注册
if ($OJ_LOGIN_MOD != "hustoj") {
    $err_str = $err_str . "$MSG_SYSTEM $MSG_DISABLE $MSG_REGISTER \\n";
    $err_cnt++;
}

// 验证用户名长度
if ($len > 48) {
    $err_str = $err_str . "$MSG_USER_ID $MSG_TOO_LONG !\\n";
    $err_cnt++;
} else if ($len < 3) {
    $err_str = $err_str . " $MSG_WARNING_USER_ID_SHORT\\n";
    $err_cnt++;
}

// 验证用户名格式是否有效
if (!is_valid_user_name($user_id)) {
    $err_str = $err_str . "$MSG_USER_ID $MSG_WRONG !\\n";
    $err_cnt++;
}

// 处理昵称信息
$nick = trim($_POST['nick']);
$len = mb_strlen($nick);
if ($len > 20) {
    $err_str = $err_str . "$MSG_NICK $MSG_TOO_LONG !\\n";
    $err_cnt++;
} else if ($len == 0) $nick = $user_id;

// 检查用户名、学校、昵称是否包含不良词汇
if (has_bad_words($user_id)) {
    $err_str = $err_str . $MSG_USER_ID . "$MSG_TOO_BAD!\\n";
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

// 验证两次输入的密码是否一致
if (strcmp($_POST['password'], $_POST['rptpassword']) != 0) {
    $err_str = $err_str . "$MSG_WARNING_REPEAT_PASSWORD_DIFF!\\n";
    $err_cnt++;
}

// 验证密码长度是否小于6位
if (strlen($_POST['password']) < 6) {
    $err_cnt++;
    $err_str = $err_str . "$MSG_WARNING_PASSWORD_SHORT \\n";
}

// 验证学校名称长度
$len = mb_strlen($_POST['school']);
if ($len > 20) {
    $err_str = $err_str . "$MSG_SCHOOL $MSG_TOO_LONG!\\n";
    $err_cnt++;
}

// 验证邮箱长度
$len = mb_strlen($_POST['email']);
if ($len > 100) {
    $err_str = $err_str . "$MSG_EMAIL $MSG_TOO_LONG!\\n";
    $err_cnt++;
}

// 如果存在错误信息，则显示错误并返回
if ($err_cnt > 0) {
    print "<script language='javascript'>\n";
    print "alert('";
    print $err_str;
    print "');\n history.go(-1);\n</script>";
    exit(0);

}

// 生成加密密码
$password = pwGen($_POST['password']);

// 检查用户名是否已存在
$sql = "SELECT `user_id` FROM `users` WHERE `users`.`user_id` = ?";
$result = pdo_query($sql, $user_id);
$rows_cnt = count($result);
if ($rows_cnt == 1) {
    print "<script language='javascript'>\n";
    print "alert('$MSG_USER_ID Existed!\\n');\n";
    print "history.go(-1);\n</script>";
    exit(0);
}

// 检查特殊用户ID是否冲突
if ($domain == $DOMAIN && $OJ_NAME == $user_id) {
    print "<script language='javascript'>\n";
    print "alert('$MSG_USER_ID Existed!\\n');\n";
    print "history.go(-1);\n</script>";
    exit(0);
}

// 对用户输入进行HTML实体编码以防止XSS攻击
$nick = (htmlentities($nick, ENT_QUOTES, "UTF-8"));
$school = (htmlentities($school, ENT_QUOTES, "UTF-8"));
$email = (htmlentities($email, ENT_QUOTES, "UTF-8"));
$ip = ($_SERVER['REMOTE_ADDR']);

// 获取真实IP地址，处理代理服务器情况
if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty(trim($_SERVER['HTTP_X_FORWARDED_FOR']))) {
    $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR'];
    $tmp_ip = explode(',', $REMOTE_ADDR);
    $ip = (htmlentities($tmp_ip[0], ENT_QUOTES, "UTF-8"));
} else if (isset($_SERVER['HTTP_X_REAL_IP']) && !empty(trim($_SERVER['HTTP_X_REAL_IP']))) {
    $REMOTE_ADDR = $_SERVER['HTTP_X_REAL_IP'];
    $tmp_ip = explode(',', $REMOTE_ADDR);
    $ip = (htmlentities($tmp_ip[0], ENT_QUOTES, "UTF-8"));
}

// 检查IP是否已经注册过
if (isset($OJ_REG_SPEED) && $OJ_REG_SPEED > 0) {

    // 查询最近1小时内该IP地址已经注册的用户数量
    $sql = "SELECT COUNT(*) FROM `users` WHERE (`ip` = ? or email = ? ) AND `reg_time` > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
    $result = pdo_query($sql, $ip, $email);
    $count = intval($result[0][0]);

    if ($count > $OJ_REG_SPEED) {
        // 如果数量大于$OJ_REG_SPEED ，则表示该IP地址在最近1小时内已经注册过$OJ_REG_SPEED个账户
        $warning = "$ip 正在快速注册大量新账号，请确认是否存在攻击行为。若能确认是攻击行为，可以用sudo iptables -A INPUT -s  $ip  -j DROP 命令 封禁IP。";
        if ($OJ_ADMIN != "root@localhost") email($OJ_ADMIN, "系统警告,疑似攻击!", $warning . "\n from $domain");   //只有设置好的才发送邮件
        print "<script language='javascript'>\n";
        print "alert('您的IP地址或Email已经注册过" . $OJ_REG_SPEED . "个账户，请稍后再试。\\n');\n";
        print "history.go(-1);\n</script>";
        exit(0);
    }
}

// 生成激活码或设置为空
if (isset($OJ_EMAIL_CONFIRM) && $OJ_EMAIL_CONFIRM)
    $_SESSION[$OJ_NAME . '_' . 'activecode'] = getToken(18);
else
    $_SESSION[$OJ_NAME . '_' . 'activecode'] = "";

// 根据是否需要确认设置用户状态
if (isset($OJ_REG_NEED_CONFIRM) && $OJ_REG_NEED_CONFIRM) $defunct = "Y";
else $defunct = "N";

// 插入新用户到数据库
$sql = "INSERT INTO `users`("
        . "`user_id`,`email`,`ip`,`accesstime`,`password`,`reg_time`,`nick`,`school`,`group_name`,`defunct`,activecode)"
        . "VALUES(?,?,?,NOW(),?,NOW(),?,?,?,?,?)";
$rows = pdo_query($sql, $user_id, $email, $ip, $password, $nick, $school, getMappedSpecial($user_id), $defunct, $_SESSION[$OJ_NAME . '_' . 'activecode']);// or die("Insert Error!\n");

//发送激活邮件
if (isset($OJ_EMAIL_CONFIRM) && $OJ_EMAIL_CONFIRM) {
    $link = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "active.php?code=" . $_SESSION[$OJ_NAME . '_' . 'activecode'];
    email($email, "$MSG_ACTIVE_YOUR_ACCOUNT",
            "$MSG_CLICK_COPY $MSG_ACTIVE_YOUR_ACCOUNT $user_id :\n " . $link);

    $view_errors = "<div class='ui main container' ><font size=5 > $MSG_CHECK $email $MSG_EMAIL , $MSG_CLICK_COPY $MSG_ACTIVE_YOUR_ACCOUNT";
    $view_errors .= "</font></div><hr><hr>";

    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 记录登录日志
$sql = "INSERT INTO `loginlog`(user_id,password,ip,time) VALUES(?,?,?,NOW())";
pdo_query($sql, $user_id, "no save", $ip);

// 如果不需要确认注册，则自动登录用户
if (!isset($OJ_REG_NEED_CONFIRM) || !$OJ_REG_NEED_CONFIRM) {
    $sql = "SELECT `user_id` FROM `users` WHERE `users`.`user_id` = ?";
    $result = pdo_query($sql, $user_id);
    $rows_cnt = count($result);
    if ($rows_cnt == 1) {
        $_SESSION[$OJ_NAME . '_' . 'user_id'] = $user_id;
        $sql = "SELECT `rightstr` FROM `privilege` WHERE `user_id`=?";
        //echo $sql."<br />";
        $result = pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id']);
        foreach ($result as $row) {
            $_SESSION[$OJ_NAME . '_' . $row['rightstr']] = true;
            //echo $_SESSION[$OJ_NAME.'_'.$row['rightstr']]."<br />";
        }
        $_SESSION[$OJ_NAME . '_' . 'ac'] = array();
        $_SESSION[$OJ_NAME . '_' . 'sub'] = array();
        if ($OJ_SaaS_ENABLE && $domain == $DOMAIN) header("location:modifypage.php#MyOJ");
    }
}else{
    ?>
<script>
    alert("<?php echo "$MSG_SYSTEM $MSG_Pending $MSG_ADMIN / $MSG_EMAIL $MSG_ACTIVE_YOUR_ACCOUNT";?>");
    history.go(-2);
</script>
   <?php
}


