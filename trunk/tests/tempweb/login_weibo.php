<?php

function http_request($url, $is_post = False)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($is_post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "");
    }
    $resp = curl_exec($ch);
    curl_close($ch);
    return $resp;
}

require_once("./include/db_info.inc.php");
require_once("./include/my_func.inc.php");

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $GURL = "https://api.weibo.com/oauth2/access_token?";
    $vars = array(
        'client_id' => $OJ_WEIBO_AKEY,
        'client_secret' => $OJ_WEIBO_ASEC,
        'grant_type' => 'authorization_code',
        'redirect_uri' => $OJ_WEIBO_CBURL,
        'code' => $code);
    $GURL = $GURL . http_build_query($vars);
    $ret = http_request($GURL, True);
    $data = json_decode($ret);
    if (isset($data['uid'])) {
        $token = $data->access_token;
        $uid = $data->uid;
        $vars = array(
            "access_token" => $token,
            "uid" => $uid
        );
        $UURL = "https://api.weibo.com/2/users/show.json?" . http_build_query($vars);
        $user = json_decode(http_request($UURL, False));
#        var_dump($user);
#        exit(0);
        // register this user and login it
        $uname = "weibo_" . $uid;
        $nick = "@" . $user->screen_name;
        $password = $OJ_OPENID_PWD;
        $email = $user->screen_name . "@weibo.com";
        $school = "";
        // check first
        $sql = "SELECT `user_id` FROM `users` where `user_id`=?";
        $res = pdo_query($sql, $uname);
        $row_num = count($res);
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
            pdo_query($sql, $uname, $email, $ip, $password, $nick, $school);
        }
        // login it
        $_SESSION[$OJ_NAME . '_' . 'user_id'] = $uname;
        // redirect it
        header("Location: ./");
    } else {
        echo "Login Expire!";
    }
} else {
    $CBURL = "https://api.weibo.com/oauth2/authorize?client_id={$OJ_WEIBO_AKEY}&response_type=code&redirect_uri=$OJ_WEIBO_CBURL";
    header("Location: " . $CBURL);
}
