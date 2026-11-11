<?php
if (__FILE__ === $_SERVER['SCRIPT_FILENAME']) {
    // 当前文件就是用户直接请求的入口脚本
    http_response_code(403);
    exit('Direct access is not allowed.');
}
	$str=file_get_contents('http://cn.bing.com/HPImageArchive.aspx?idx=0&n=1');
	if(preg_match("/<url>(.+?)<\/url>/ies",$str,$matches)){
		$imgurl='http://cn.bing.com'.$matches[1];
	}
	if($imgurl){
		header('Content-Type: image/JPEG');
		@ob_end_clean();
		@readfile($imgurl);
		@flush(); @ob_flush();
		exit();
	}else{
		exit('error');
	}

?>