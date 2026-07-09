<?php 
// 1. 设置脚本运行环境（建议根据实际情况微调）
ini_set("memory_limit", "1024M"); 
ini_set("max_execution_time", "600");

// 2. 定义白名单列表 (统一使用纯主机名/域名/IP，不要带端口或 http://)
$allowed_hosts = [
    'www.yourdomain.com', 
    'yourdomain.com',
    '127.0.0.1', 
    'localhost'
];

// 如果需要动态追加当前请求的主机名（过滤掉端口）
if (isset($_SERVER['HTTP_HOST'])) {
    // 使用 parse_url 或 explode 截取纯 Host（去除 :port）
    $current_host = explode(':', $_SERVER['HTTP_HOST'])[0];
    $allowed_hosts[] = strtolower($current_host);
}

// 去重并统一转换为小写（防止大小写绕过，如 Localhost）
$allowed_hosts = array_unique(array_map('strtolower', $allowed_hosts));

// 3. 安全获取并解析 Referer
$referer = $_SERVER['HTTP_REFERER'] ?? null;
$referer_host = null;

if ($referer) {
    $parsed_host = parse_url($referer, PHP_URL_HOST);
    if ($parsed_host) {
        $referer_host = strtolower($parsed_host);
    }
}

// 4. 严格校验
// 注意：如果允许直接打开/刷新页面（没有 Referer 的情况），可以取消下面 $allow_empty_referer 的注释逻辑
$allow_empty_referer = false; // 是否允许空 Referer（直接访问）

if (
    (!$referer_host && !$allow_empty_referer) || 
    ($referer_host && !in_array($referer_host, $allowed_hosts, true))
) {
    // 现代化响应 403
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    exit("Access Denied: Invalid Referer.\n如果使用了反代或者穿透，请检查当前访问域名是否已加入白名单(admin-header.php)。");
}
require_once("../include/db_info.inc.php");
require_once ("../include/my_func.inc.php");
if(file_exists("../lang/$OJ_LANG.php")) require_once("../lang/$OJ_LANG.php");
if(isset($OJ_LOG_ENABLED) && $OJ_LOG_ENABLED){
	$params = json_encode($_REQUEST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	$logger->info($params);
}
?>
<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel=stylesheet href='../bootstrap/css/bootstrap.min.css' type="text/css">
<script src="../template/bs3/jquery.min.js"></script>
<script>
$("document").ready(function (){
  $("form").append("<div id='csrf' />");
  $("#csrf").load("../csrf.php");
});

</script>
<?php if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])||isset($_SESSION[$OJ_NAME.'_'.'password_setter']))){
	echo "<a href='../loginpage.php'>".(isset($MSG_Login)?$MSG_Login:"Please Login First!")."</a>";
	exit(1);
}
if(file_exists("../template/$OJ_TEMPLATE/css.php")) require_once("../template/$OJ_TEMPLATE/css.php");
?>
<iframe src="../session.php" height=0px width=0px ></iframe>

