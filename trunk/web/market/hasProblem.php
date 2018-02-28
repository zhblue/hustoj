<?php 
require_once (dirname(__FILE__) ."/../include/db_info.inc.php");
require_once (dirname(__FILE__) ."/market.inc.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])
                ||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])
                ||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])
                )){
        echo "Please Login First!";
        exit(1);
}
if (isset($_POST ['title_md5'])) {
	$title_md5 = $_POST['title_md5'];
	require_once(dirname(__FILE__) ."/market.inc.php");
	
	$data_url = $OJ_MARKET_HOST . '/market/hasProblem_api.php';
	$post = "title_md5=" .$title_md5;
	$msg = http_request($data_url,$post);
	//echo $title_md5;
	echo $msg;
	exit(1);
}
echo "please post title_md5";
?>

