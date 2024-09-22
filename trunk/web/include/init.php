<?php
require_once(dirname(__FILE__)."/pdo.php");
require_once(dirname(__FILE__)."/memcache.php");

//自动切换夜间模式
//if(date('H')<5||date('H')>21||isset($_GET['dark'])) $OJ_CSS="dark.css";

//允许用参数tp临时切换皮肤
/*
if(in_array($_GET['tp'],$OJ_TP)){
    $OJ_TEMPLATE=$_GET['tp'];
    setcookie("tp", $_GET['tp'], time()+3600);
}else if (in_array($_COOKIE['tp'],$OJ_TP)){
    $OJ_TEMPLATE=$_COOKIE['tp'];
}
*/

//自动识别语言
if (isset($_SESSION[$OJ_NAME . '_' . 'OJ_LANG'])) {
	$OJ_LANG=$_SESSION[$OJ_NAME . '_' . 'OJ_LANG'];
} else if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], array("cn", "ug", "en", 'fa', 'ko', 'th'))) {
	$OJ_LANG=$_COOKIE['lang'];
} else if (isset($_GET['lang']) && in_array($_GET['lang'], array("cn", "ug", "en", 'fa', 'ko', 'th'))) {
	$OJ_LANG=$_GET['lang'];
} else if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) &&   $OJ_LANG != "cn") {
    $userLanguages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    foreach ($userLanguages as $userLang) {
        $langParts = explode(';', $userLang);
        $lang = strtolower(substr($langParts[0], 0, 2));
	if (in_array($lang, array("zh", "ug", "en", 'fa', 'ko', 'th'))) {
            $OJ_LANG = $lang;
	    if($lang=="zh") $OJ_LANG="cn";
            break;
        }
    }
}
require(dirname(__FILE__)."/../lang/$OJ_LANG.php");

$domain=basename($_SERVER["HTTP_HOST"]);

if($OJ_SaaS_ENABLE){
	$DOMAIN="my.hustoj.com";   //   如启用，需要替换为SaaS服务的主域名。
	$OJ_SaaS_CONF=realpath(dirname(__FILE__)."/..")."/SaaS/".basename($_SERVER["HTTP_HOST"]).".php";
	if(file_exists($OJ_SaaS_CONF)){
		require_once($OJ_SaaS_CONF);
	}else{
	//	echo $OJ_SaaS_CONF;
	}
	if($domain==$DOMAIN)  $MSG_REG_INFO.="/初始化MyOJ";
}else{
	$DOMAIN=$domain;
}

if(isset($_SERVER["HTTP_USER_AGENT"])&&strpos($_SERVER["HTTP_USER_AGENT"],"MSIE")){  // 360 or IE use bs3 instead
    $OJ_TEMPLATE="bs3";
}


if(isset($_SESSION[$OJ_NAME.'_user_id'])&&isset($OJ_LIMIT_TO_1_IP)&& $OJ_LIMIT_TO_1_IP){
        $ip = ($_SERVER['REMOTE_ADDR']);
        if( isset($_SERVER['HTTP_X_FORWARDED_FOR'] )&&!empty( trim( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ){
            $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR'];
            $tmp_ip=explode(',',$REMOTE_ADDR);
            $ip =(htmlentities($tmp_ip[0],ENT_QUOTES,"UTF-8"));
        } else if(isset($_SERVER['HTTP_X_REAL_IP'])&& !empty( trim( $_SERVER['HTTP_X_REAL_IP'] ) ) ){
            $REMOTE_ADDR = $_SERVER['HTTP_X_REAL_IP'];
            $tmp_ip=explode(',',$REMOTE_ADDR);
            $ip =(htmlentities($tmp_ip[0],ENT_QUOTES,"UTF-8"));
        }
        $sql="select ip from loginlog where user_id=? order by time desc";
        $rows=pdo_query($sql,$_SESSION[$OJ_NAME.'_user_id'] );
        $lastip=$rows[0][0];
        if($ip!=$lastip){
                unset($_SESSION[$OJ_NAME.'_'.'user_id']);
                setcookie($OJ_NAME."_user","");
                setcookie($OJ_NAME."_check","");
                session_destroy();
                $view_errors="Logged in another ip address:$lastip, auto logout!";
                require("template/$OJ_TEMPLATE/error.php");
                exit(0);
        }

}



$OJ_LOG_FILE="/var/log/hustoj/{$OJ_NAME}.log";
require_once(dirname(__FILE__) . "/logger.php");

$logger=new Logger(isset($_SESSION[$OJ_NAME . '_' . 'user_id'])?$_SESSION[$OJ_NAME . '_' . 'user_id']:"guest", 
					$OJ_LOG_FILE, 
					$OJ_LOG_DATETIME_FORMAT, 
					$OJ_LOG_ENABLED, 
					$OJ_LOG_PID_ENABLED,
					$OJ_LOG_USER_ENABLED,
					$OJ_LOG_URL_ENABLED,
					$OJ_LOG_URL_HOST_ENABLED,
					$OJ_LOG_URL_PARAM_ENABLED,
					$OJ_LOG_TRACE_ENABLED);
$logger->info();
// these lines can help you make a SaaS platform of HUSTOJ with the help of JudgeHub
// 傻瓜级保姆配置系统
switch($OJ_FRIENDLY_LEVEL) {
	case 9:
	   $OJ_GUEST=true;
	case 8:
	   $OJ_DOWNLOAD=true;
	case 7:
	   $OJ_BBS="discuss3";
	   $OJ_FREE_PRACTICE=true;
	case 6:
	   $OJ_LONG_LOGIN=true; 
	case 5:
	   $OJ_TEST_RUN=true; 
	case 4:
	   $OJ_MAIL=true;
	   $OJ_AUTO_SHARE=true;
	case 3:
	   $OJ_SHOW_DIFF=true; 
	   $OJ_VCODE=false;
	case 2:
	   $OJ_LANG="cn";
	case 1:
	   date_default_timezone_set("Asia/Shanghai");
	   pdo_query("SET time_zone ='+8:00'");
	case 0:
	   break;
	case -1:
	   $OJ_NEED_LOGIN=true;
	   $OJ_REGISTER=false;
	   
}
if(!isset($OJ_SUBMIT_COOLDOWN_TIME)) $OJ_SUBMIT_COOLDOWN_TIME=3;
// if using EXAM or ON site auto turn off free practice
if(isset($OJ_ON_SITE_CONTEST_ID) || isset($OJ_EXAM_CONTEST_ID)) $OJ_FREE_PRACTICE=false;

// $OJ_BG="/image/bg".date('H').".jpg";  //每个整点更换壁纸，需要准备bg[0~23].jpg在image目录
// if OJ_BG==bing ,using bing.com for daily change background
if(isset($OJ_BG)&&$OJ_BG=="bing"){
    $logfile="/dev/shm/bing.log";
    $history=@file_get_contents($logfile);
    if($history!=""){
        $history=json_decode($history);
    }else{
        $history=array();
    }
   $bg_file=dirname(dirname(__FILE__))."/image/bg.url";
   if(!file_exists($bg_file)) touch($bg_file);
   if(time()-fileatime($bg_file)>3600*24){
           require_once(dirname(__FILE__)."/curl.php");
           $data=curl_get("https://cn.bing.com/");
           $OJ_BG=getPartByMark($data,"<link rel=\"preload\" href=\"","\" as=\"image\" id=\"preloadBg\"");
           if(strpos($OJ_BG,"http")!=0)$OJ_BG="https://cn.bing.com/".$OJ_BG;
           //echo $OJ_BG;
           if($OJ_BG){
                   file_put_contents($bg_file,$OJ_BG);
                    if(!in_array($OJ_BG,$history)){
                        array_push($history,$OJ_BG);
                        if(count($history)>30) array_shift($history);
                        file_put_contents($logfile,json_encode($history));
                    }
           }
           else touch($bg_file);
   }else{
           $OJ_BG=file_get_contents($bg_file);
           if(!empty($history)){
                $OJ_BG=$history[rand(0,count($history)-1)];

           }
   }

}
if (!empty($OJ_CDN_URL)) {
        header('Access-Control-Allow-Origin:'.$OJ_CDN_URL);
}
