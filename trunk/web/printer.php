<?php
require_once('./include/db_info.inc.php');
require_once('./include/const.inc.php');
require_once('./include/memcache.php');
require_once('./include/setlang.php');

$view_title = $MSG_SUBMIT;

// 检查用户是否已登录，未登录则跳转到登录页面
if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {

    $view_errors = "<a href=loginpage.php>$MSG_Login</a>";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
//	$_SESSION[$OJ_NAME.'_'.'user_id']="Guest";
}

// 如果是POST请求，则进行安全验证
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once("include/check_post_key.php");
}

// 处理打印机功能相关的逻辑
if (isset($OJ_PRINTER) && $OJ_PRINTER) {
    // 获取当前用户的学校信息
    $school = pdo_query("select school from users where user_id=?", $_SESSION[$OJ_NAME . "_user_id"])[0][0];

    // 检查用户是否具有打印机权限
    if (isset($_SESSION[$OJ_NAME . '_' . 'printer'])) {
        // 处理打印任务状态更新或清理操作
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            pdo_query("update printer set status=1 where printer_id=?", $id);
        }
        if (isset($_POST['clean'])) {
            pdo_query("delete from printer where user_id like ?", "$school%");
        }

        // 获取并显示打印任务列表
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
        // 处理新的打印请求
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
    // 打印机功能未启用时显示错误信息
    $view_errors = "$MSG_PRINTER not available!";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}
/////////////////////////Template
/////////////////////////Common foot


