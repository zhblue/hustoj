<?php session_start();
require_once "include/db_info.inc.php";
require_once "include/init.php";
if (!$OJ_REMOTE_JUDGE) exit(0);

/**
 * 远程评测系统健康检查脚本
 * 
 * 该脚本用于检查配置的远程OJ系统的可用性，通过HTTP请求验证远程站点状态，
 * 并根据响应状态码决定是否加载对应的远程评测检查页面
 */

// 定义需要检查的远程OJ系统列表
$remote_ojs = array(
    "bas"       // "pku","hdu"     //使用一本通启蒙设为："bas"
);

// 定义各远程OJ系统的访问地址
$sites = array(
    "demo" => "http://demo.hustoj.com/",
    "pku" => "http://poj.org/",
    "hdu" => "http://acm.hdu.edu.cn/",
    "bas" => "http://www.ssoier.cn:18087/pubtest/"
);

$i = 0;
foreach ($remote_ojs as $remote_oj) {
    $file = "include/remote_$remote_oj.php";
    if (file_exists($file)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sites[$remote_oj]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        $curl_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($curl_code == 200) {
            echo "<iframe src='$file?check' ></iframe>";
        } else {
            echo "$remote_oj error code:" . $curl_code;

        }
    } else {
        echo "no file:" . $file;
    }

    $i++;
}

