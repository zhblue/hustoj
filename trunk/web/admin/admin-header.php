<?php 
ini_set("memory_limit", "1024M");  //set this bigger to import big files.
ini_set("max_execution_time", "600");
// 检查 Referer 是否存在，且主域名是否与当前服务器域名一致
// 1. 在这里定义你的白名单（支持域名、IP或带端口的地址）
$allowed_hosts = [
    $_SERVER['HTTP_HOST'],     // 默认允许当前访问的主机名
    'www.yourdomain.com',      // 你的主域名
    '127.0.0.1',               // 本地测试IP
    'localhost'
];

// 2. 获取 Referer 中的主机名
$referer_host = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) : null;

// 3. 验证是否在白名单中
if (!$referer_host || !in_array($referer_host, $allowed_hosts)) {
    header('HTTP/1.1 403 Forbidden');
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

