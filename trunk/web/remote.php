<?php session_start();
     require_once "include/db_info.inc.php";
     require_once "include/init.php";
        $remote_ojs=array(
                 "bas"       // "pku","hdu"     //使用一本通启蒙设为："bas"  
        );
	$sites=array(
		"pku" => "http://poj.org/",
		"hdu" => "http://acm.hdu.edu.cn/",
		"bas" => "http://bas.ssoier.cn:8086/about.php"
	);
$i=0;
foreach($remote_ojs as $remote_oj){
	$file="include/remote_$remote_oj.php";
    if(file_exists($file)){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $sites[$remote_oj]);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_exec($ch);  
		$curl_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($curl_code == 200){
			echo "<iframe src='$file?check' ></iframe>";
		}else{
                        echo "$remote_oj error code:".$curl_code;

                }
    }else{
        echo "no file:".$file;
    }

	$i++;
}
