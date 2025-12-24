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
}

// 如果请求方法为POST，则进行POST密钥检查
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    require_once("include/check_post_key.php");
}

// 处理打印机功能相关逻辑
if (isset($OJ_PRINTER) && $OJ_PRINTER) {
    // 获取当前用户的学校信息
    $stmt = pdo_query("select school from users where user_id=?", $_SESSION[$OJ_NAME . "_user_id"]);
    if (!$stmt || count($stmt) == 0) {
        $view_errors = "User not found!";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }
    $school = $stmt[0][0];
    
    // 检查用户是否具有打印机权限
    if (isset($_SESSION[$OJ_NAME . '_' . 'printer'])) {
        // 处理打印任务状态更新或清理操作
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            // 验证该打印任务是否属于当前用户学校
            $stmt = pdo_query("select user_id from printer where printer_id=?", $id);
            if ($stmt && count($stmt) > 0) {
                $task_user_id = $stmt[0][0];
                // 验证任务是否属于当前用户学校
                if (strpos($task_user_id, $school) === 0) {
                    pdo_query("update printer set status=1 where printer_id=?", $id);
                }
            }
        }
        if (isset($_POST['clean'])) {
            // 确保只能清理当前用户学校相关的打印任务
            pdo_query("delete from printer where user_id like ?", "$school%");
        }
        
        // 获取并显示打印机列表
        $view_printer = array();
        $result = pdo_query("select printer_id,user_id,status,content from printer where user_id like ? order by status,printer_id desc limit 50", "$school%");
        $i = 0;
        foreach ($result as $row) {
            $view_printer[$i] = array();
            $view_printer[$i][0] = htmlspecialchars($row['printer_id']);
            $view_printer[$i][1] = htmlspecialchars($row['user_id']);
            if ($row['status'] == 1) {
                $view_printer[$i][2] = htmlspecialchars($MSG_PRINT_DONE);
            } else {
                $view_printer[$i][2] = htmlspecialchars($MSG_PRINT_PENDING);
            }
            $view_printer[$i][3] = "<a href='printer_view.php?id=" . intval($row['printer_id']) . "' target='_self'>" . htmlspecialchars($MSG_PRINTER) . "</a>";

            $i++;
        }
        require("template/" . $OJ_TEMPLATE . "/printer_list.php");
        exit(0);
    } else {
        // 处理打印任务提交
        if (isset($_POST['content'])) {
            $content = $_POST['content'];
            // 验证内容长度等限制
            if (strlen($content) > 10000) { // 假设最大长度限制为10000字符
                $view_errors = "Content too long!";
                require("template/" . $OJ_TEMPLATE . "/error.php");
                exit(0);
            }
            
            $sql = "insert into printer(user_id,in_date,status,content) values(?,now(),0,?)";
            $result = pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id'], $content);
            if (!$result) {
                $view_errors = "Failed to submit print job!";
                require("template/" . $OJ_TEMPLATE . "/error.php");
                exit(0);
            }
            
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
    // 打印机功能不可用时显示错误信息
    $view_errors = "$MSG_PRINTER not available!";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}
/////////////////////////Template
/////////////////////////Common foot
