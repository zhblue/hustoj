<?php

require_once("./include/db_info.inc.php");
require_once("./include/my_func.inc.php");

if (isset($OJ_SaaS_ENABLE) && $OJ_SaaS_ENABLE) {
    if ($_SERVER['HTTP_HOST'] == $DOMAIN && isset($_SESSION[$OJ_NAME . '_user_id'])) {
        $template = "bs3";
        $friendly = 0;

        if (isset($_POST['template'])) $template = basename($_POST['template']);
        if (isset($_POST['friendly'])) $friendly = intval($_POST['friendly']);
        $user_id = $_SESSION[$OJ_NAME . '_user_id'];
        create_subdomain($user_id, $template, $friendly);
        header("location:http://$user_id.$DOMAIN");
        exit();
    }
    header("location:modifypage.php");
} else {

    header("location:index.php");

}
