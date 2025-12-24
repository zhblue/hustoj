<?php
require_once(dirname(__FILE__) . "/include/db_info.inc.php");

/**
 * 检查用户账户状态，如果用户账户被禁用则自动登出用户
 * 从会话中获取用户ID，查询数据库中该用户是否被标记为禁用(defunct='Y')
 * 如果用户被禁用，则清除会话和cookie信息，销毁会话，并重定向到首页
 */
if (isset($_SESSION[$OJ_NAME . "_user_id"])) {
    $user_id = $_SESSION[$OJ_NAME . "_user_id"];
    $defunct = pdo_query("select defunct from users where user_id=?", $user_id);
    if (!empty($defunct) && $defunct[0][0] == "Y") {
        unset($_SESSION[$OJ_NAME . '_' . 'user_id']);
        setcookie($OJ_NAME . "_user", "");
        setcookie($OJ_NAME . "_check", "");
        session_destroy();
        ?>
        <script>window.top.location.href = "index.php";</script>
        <?php
    }
}

/**
 * 处理内网IP穿透功能
 * 如果配置了$OJ_LIP_URL，则通过GET参数或COOKIE获取内网IP信息
 * 如果没有获取到内网IP信息，则跳转到LIP服务URL获取
 */
// $OJ_LIP_URL="http://192.168.2.36/lip.php";  //如果希望穿透NAT网络，识别用户的内网IP地址，可以在内网部署一个LIP服务，用内网的lip.php向公网服务器传递用户的内网IP。
if (!empty($OJ_LIP_URL)) {
    if (isset($_GET['lip'])) {
        $lip = intval($_GET['lip']);
        setcookie("lip", $lip);
    } else if (!isset($_COOKIE['lip'])) {
        echo "<script> window.setTimeout(\"window.location.href='$OJ_LIP_URL';\",1000);</script>";
    }
}
/**
 * 设置页面自动刷新功能
 * 每5分钟(300000毫秒)自动刷新页面
 */
?>
<script>window.setTimeout('window.location.reload();', 300000);</script>
