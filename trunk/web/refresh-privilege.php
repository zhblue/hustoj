<?php
require_once("./include/db_info.inc.php");
require_once('./include/setlang.php');

/**
 * 权限刷新和用户验证脚本
 * 
 * 该脚本用于检查用户登录状态，并根据用户权限更新会话信息
 * 如果用户已登录，则查询并刷新用户的权限信息
 * 如果用户未登录，则跳转到登录页面
 */

// 当前user_id
$user_id = $_SESSION[$OJ_NAME . '_' . 'user_id'];

if (!empty($user_id)) {
    // 已登录的
    if (!empty($_SESSION[$OJ_NAME . '_group_name'])) {
        $sql = "SELECT * FROM `privilege` WHERE `user_id`=? or (user_id=? and rightstr like 'c%' )";
        $result = pdo_query($sql, $user_id, $_SESSION[$OJ_NAME . '_group_name']);
    } else {
        $sql = "SELECT * FROM `privilege` WHERE `user_id`=? ";
        $result = pdo_query($sql, $user_id);
    }
    // 刷新各种权限
    foreach ($result as $row) {
        if (isset($row['valuestr'])) {
            $_SESSION[$OJ_NAME . '_' . $row['rightstr']] = $row['valuestr'];
        } else {
            $_SESSION[$OJ_NAME . '_' . $row['rightstr']] = true;
        }
    }
    // VIP用户特殊权限处理：VIP用户可以访问所有标记为[VIP]的比赛
    if (isset($_SESSION[$OJ_NAME . '_vip'])) {  // VIP mark can access all [VIP] marked contest
        $sql = "select contest_id from contest where title like '%[VIP]%'";
        $result = pdo_query($sql);
        foreach ($result as $row) {
            $_SESSION[$OJ_NAME . '_c' . $row['contest_id']] = true;
        }
    };
    ?>
    <script>console.log("Privilege refreshed !!");</script>
    <?php
} else {
    // 没登录的
    ?>
    <script>
        alert("<?php echo $MSG_Login; ?>");
        window.location.href = "./loginpage.php";
    </script>
    <?php
}


