<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<?php
require_once("./include/db_info.inc.php");
require_once("./include/my_func.inc.php");
//Step1£º»ñÈ¡Authorization Code
session_start();
$code = $_REQUEST["code"];
if (empty($code)) {
    $_SESSION[$OJ_NAME . '_' . 'state'] = md5(uniqid(rand(), TRUE));
    $dialog_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
            . $OJ_QQ_AKEY . "&redirect_uri=" . urlencode($OJ_QQ_CBURL) . "&state="
            . $_SESSION[$OJ_NAME . '_' . 'state'];
    echo("<script> top.location.href='" . $dialog_url . "'</script>");
}

//get Access Token
if ($_REQUEST['state'] == $_SESSION[$OJ_NAME . '_' . 'state']) {
    $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
            . "client_id=" . $OJ_QQ_AKEY . "&redirect_uri=" . urlencode($OJ_QQ_CBURL)
            . "&client_secret=" . $OJ_QQ_ASEC . "&code=" . $code;
    $response = file_get_contents($token_url);
    if (strpos($response, "callback") !== false) {
        $lpos = strpos($response, "(");
        $rpos = strrpos($response, ")");
        $response = substr($response, $lpos + 1, $rpos - $lpos - 1);
        $msg = json_decode($response);
        if (isset($msg->error)) {
            echo "<h3>error:</h3>" . $msg->error;
            echo "<h3>msg  :</h3>" . $msg->error_description;
            exit;
        }
    }

    //get OpenID
    $params = array();
    parse_str($response, $params);
    $graph_url = "https://graph.qq.com/oauth2.0/me?access_token="
            . $params['access_token'];
    $str = file_get_contents($graph_url);
    if (strpos($str, "callback") !== false) {
        $lpos = strpos($str, "(");
        $rpos = strrpos($str, ")");
        $str = substr($str, $lpos + 1, $rpos - $lpos - 1);
    }

    $user = json_decode($str);
    if (isset($user->error)) {
        echo "<h3>error:</h3>" . $user->error;
        echo "<h3>msg  :</h3>" . $user->error_description;
        exit;
    }

    //get userInfo
    $graph_url = "https://graph.qq.com/user/get_user_info?access_token="
            . $params['access_token'] . "&oauth_consumer_key=" . $OJ_QQ_AKEY
            . "&openid=" . $user->openid . "&format=json";
    $str = file_get_contents($graph_url);
    $userInfo = json_decode($str);

    $user_id = "qq_" . $user->openid;
    $nick = $userInfo->nickname;
    $password = $OJ_OPENID_PWD;;
    $email = "xx";
    $school = "xx";

    $sql = "SELECT `user_id` FROM `users` where `user_id`=?";
    $res = pdo_query($sql, $user_id);
    $row_num = count($res);
    echo('row_num' . $row_num . '<br>');
    if ($row_num == 0) {
        $sql = "INSERT INTO `users`("
                . "`user_id`,`email`,`ip`,`accesstime`,`password`,`reg_time`,`nick`,`school`)"
                . "VALUES(?,?,?,NOW(),?,NOW(),?,?)";
        // reg it
        $ip = ($_SERVER['REMOTE_ADDR']);
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR'];
            $tmp_ip = explode(',', $REMOTE_ADDR);
            $ip = (htmlentities($tmp_ip[0], ENT_QUOTES, "UTF-8"));
        }
        pdo_query($sql, $user_id, $email, $ip, $password, $nick, $school);
    }
    //login it
    $_SESSION[$OJ_NAME . '_' . 'user_id'] = $user_id;
    // redirect it
    header("Location: ./");
} else {
    echo("The state does not match. You may be a victim of CSRF.");
}
?>


