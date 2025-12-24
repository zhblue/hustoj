<?php
$cache_time = 1;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/memcache.php');
require_once('./include/setlang.php');
require_once('./include/bbcode.php');

$view_title = "LOGIN";

// 添加安全响应头
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// 验证必要变量是否存在
if (!isset($OJ_NAME)) {
    $OJ_NAME = 'OJ'; // 设置默认值
}
if (!isset($OJ_LONG_LOGIN)) {
    $OJ_LONG_LOGIN = false; // 设置默认值
}

// 检查用户是否已登录，如果已登录则重定向到首页并提示先退出登录
if (isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    header("Location: index.php");
    echo "<a href='logout.php'>Please logout First!</a>";
    exit(); // 使用标准退出
}

/////////////////////////Template
// 检查是否启用长期登录功能以及相关cookie是否存在，如果存在则自动登录并跳转到首页
if ($OJ_LONG_LOGIN == true && isset($_COOKIE[$OJ_NAME . "_user"]) && isset($_COOKIE[$OJ_NAME . "_check"])) {
    ?>
    <script>
        let xhr = new XMLHttpRequest();
        xhr.open('GET', 'login.php', true);
        xhr.send();
        setTimeout(function() {
            location.href='index.php';
        }, 1500);
    </script>
    <?php
} else {
    // 加载登录页面模板
    require("template/" . $OJ_TEMPLATE . "/loginpage.php");
}
/////////////////////////Common foot
// 检查并加载缓存结束文件
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');

