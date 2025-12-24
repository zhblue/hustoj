<?php
require_once('./include/db_info.inc.php');
require_once('./include/const.inc.php');
require_once('./include/memcache.php');
require_once('./include/setlang.php');
$view_title = $MSG_SUBMIT;
if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {

    $view_errors = "<a href=loginpage.php>$MSG_Login</a>";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
//	$_SESSION[$OJ_NAME.'_'.'user_id']="Guest";
}
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once("include/check_post_key.php");
}
if (isset($OJ_PRINTER) && $OJ_PRINTER) {
    $school = pdo_query("select school from users where user_id=?", $_SESSION[$OJ_NAME . "_user_id"])[0][0];
    if (isset($_SESSION[$OJ_NAME . '_' . 'printer'])) {
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            pdo_query("update printer set status=1 where printer_id=?", $id);
        }
        if (isset($_POST['clean'])) {
            pdo_query("delete from printer where user_id like ?", "$school%");
        }
        $view_printer = array();
        $result = pdo_query("select printer_id,user_id,status,content from printer where user_id like ? order by status,printer_id desc limit 50", "$school%");
        $i = 0;
        foreach ($result as $row) {
            $view_printer[$i] = array();
            $view_printer[$i][0] = $row['printer_id'];
            $view_printer[$i][1] = $row['user_id'];
            if ($row['status'] == 1) $view_printer[$i][2] = "$MSG_PRINT_DONE";
            else $view_printer[$i][2] = "$MSG_PRINT_PENDING";
            $view_printer[$i][3] = "<a href='printer_view.php?id=" . $row['printer_id'] . "' target='_self'>$MSG_PRINTER</a>";

            $i++;
        }
        require("template/" . $OJ_TEMPLATE . "/printer_list.php");
        exit(0);
    } else {
        if (isset($_POST['content'])) {
            $sql = "insert into printer(user_id,in_date,status,content) values(?,now(),0,?)";
            pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id'], $_POST['content']);
            $view_errors = "$MSG_PRINT_PENDING";
            $view_errors .= "...<br>";
            $view_errors .= "$MSG_PRINT_WAITING";
            require("template/" . $OJ_TEMPLATE . "/error.php");


        } else {
            require("template/" . $OJ_TEMPLATE . "/printer_add.php");
            exit(0);
        }

    }

} else {

    $view_errors = "$MSG_PRINTER not available!";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}
/////////////////////////Template
/////////////////////////Common foot
?>

