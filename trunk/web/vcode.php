<?php
	/****************************************
	*  验证码 v0.9
	*  Powerd by awaysoft.com
	*  本组件采用GPLv3发布
	*  2011-07-15
	****************************************/
	/* $len 为随机字符串长度，$type为类型，a为字符数字，c为字符,n为数字， 已经去除可能误导的字符 */
	function get_rand_string($len=4, $type="n"){
		if ($len < 0) $len = 4;
		if ($type == 'a') $chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz023456789';
		else if ($type == 'c') $chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
		else if ($type == 'n') $chars = '0123456789';
		else $chars = '0123456789';
		
		$result = '';
		for ($i = 0; $i < $len; $i ++){
			$index = mt_rand(0, strlen($chars) - 1);
			$result .= substr($chars, $index, 1);
		}
		return $result;
	}
	
	session_start();
   header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pramga: no-cache");

	
	/* 输出图片类型，字符长度，类型 */
	$imgtype = 'gif';
	$len = 4;
	$vcodetype = 'n';
	
	$width = 15 * $len;
	$height = 34;
	/* 生成随机字符串并写入SESSION */
	$vcode = get_rand_string($len, $vcodetype);
	$_SESSION['vcode'] = $vcode;
	header("Content-type: image/".$imgtype);
	
	if($imgtype != 'gif' && function_exists('imagecreatetruecolor')){
		$im = imagecreatetruecolor($width, $height);
	}else{
		$im = imagecreate($width, $height);
	}
	
	$r = mt_rand(0, 255);
	$g = mt_rand(0, 255);
	$b = mt_rand(0, 255);
	/* 生成背景颜色 */
	$backColor = ImageColorAllocate($im, $r, $g, $b);
	/* 生成边框颜色 */
	$borderColor = ImageColorAllocate($im, 0, 0, 0);
	/* 生成干扰点颜色 */
	$pointColor = ImageColorAllocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
	
	/* 背景位置 */
	imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
	/* 边框位置 */
	imagerectangle($im, 0, 0, $width - 1, $height - 1, $borderColor);
	
	/* 字符串颜色(背景反色) */
	$stringColor = ImageColorAllocate($im, 255 - $r, 255 - $g, 255 - $b);
	
	/* 产生干扰点 */
	$pointNumber = mt_rand($len * 25, $len * 50);
	for($i=0; $i<=$pointNumber; $i++){
		$pointX = mt_rand(2,$width-2);
		$pointY = mt_rand(2,$height-2);
		imagesetpixel($im, $pointX, $pointY, $pointColor);
	}
	
	imagettftext($im, 15, 0, 5, 24, $stringColor, "include/Vera.ttf", $vcode);
	$image_out = 'Image' . $imgtype;
	$image_out($im);
	@ImageDestroy($im);
?>
