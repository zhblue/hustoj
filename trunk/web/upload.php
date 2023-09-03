<?php
$OJ_CACHE_SHARE = false;
$cache_time = 0;

require_once('./include/db_info.inc.php');
require_once('./include/const.inc.php');
require_once('./include/cache_start.php');
require_once('./include/curl.php');
require_once('./include/memcache.php');
require_once('./include/setlang.php');

$view_title = "Upload";
$upload_dir = "./upload/";
// 获取目录中的所有文件
$files = scandir($upload_dir);

// 创建一个数组来存储文件详细信息
$fileDetails = [];

foreach($files as $file) {
    if($file != '.' && $file != '..') { 
        $fileDetails[] = [
            'name' => $file,
            'time' => filemtime($upload_dir . $file),
            'size' => filesize($upload_dir . $file)
        ];
    }
}

// 根据时间对文件进行排序
usort($fileDetails, function($a, $b) {
    return $b['time'] - $a['time'];
});

//remember page
$page = "1";
if (isset($_GET['page'])) {
	$page = intval($_GET['page']);

	if (isset($_SESSION[$OJ_NAME.'_'.'user_id'])) {
		$sql = "update users set volume=? where user_id=?";
		pdo_query($sql,$page,$_SESSION[$OJ_NAME.'_'.'user_id']);
	}
}
else {
	if (isset($_SESSION[$OJ_NAME.'_'.'user_id'])) {
		$sql = "select volume from users where user_id=?";
		$result = pdo_query($sql,$_SESSION[$OJ_NAME.'_'.'user_id']);
		$row = $result[0];
		$page = intval($row[0]);
	}else{
		$page = 1;
	}

	if (!is_numeric($page) || $page<=0)
		$page = '1';
}
if(isset($_GET['ajax'])){
	require("template/bs3/upload.php");
}else{
	require("template/".$OJ_TEMPLATE."/upload.php");
}
if(file_exists('./include/cache_end.php'))
	require_once('./include/cache_end.php');
?>

