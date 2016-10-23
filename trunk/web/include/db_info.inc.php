<?php @session_start();
	ini_set("display_errors","Off");  //set this to "On" for debugging  ,especially when no reason blank shows up.
	
//for people using hustoj out of China , be careful of the last two line of this file !

// connect db 
static 	$DB_HOST="localhost";
static 	$DB_NAME="hustojdb";
static 	$DB_USER="'DBUSERNAME'";
static 	$DB_PASS="'DBPASSWORD'";

static 	$OJ_NAME="HUSTOJ";
static 	$OJ_HOME="./";
static 	$OJ_ADMIN="it@cnsa.hs.kr";
static 	$OJ_DATA="/home/judge/data";
static 	$OJ_BBS="discuss3";//"bbs" for phpBB3 bridge or "discuss" for mini-forum
static  $OJ_ONLINE=false;
static  $OJ_LANG="en";
static  $OJ_SIM=true; 
static  $OJ_DICT=false;
static  $OJ_LANGMASK=0; //1mC 2mCPP 4mPascal 8mJava 16mRuby 32mBash 1008 for security reason to mask all other language
static  $OJ_EDITE_AREA=true;//true: syntax highlighting is active
static  $OJ_AUTO_SHARE=false;//true: One can view all AC submit if he/she has ACed it onece.
static  $OJ_CSS="hoj.css";
static  $OJ_SAE=false; //using sina application engine
static  $OJ_VCODE=false;
static  $OJ_APPENDCODE=false;
static  $OJ_MEMCACHE=false;
static  $OJ_MEMSERVER="127.0.0.1";
static  $OJ_MEMPORT=11211;
static  $SAE_STORAGE_ROOT="http://hustoj-web.stor.sinaapp.com/";
static  $OJ_TEMPLATE="bs3";
if(isset($_GET['tp'])) $OJ_TEMPLATE=$_GET['tp'];
static  $OJ_LOGIN_MOD="hustoj";
static  $OJ_RANK_LOCK_PERCENT=0;
static  $OJ_SHOW_DIFF=false;
static  $OJ_TEST_RUN=false;
static $OJ_OPENID_PWD = '8a367fe87b1e406ea8e94d7d508dcf01';

/* weibo config here */
static  $OJ_WEIBO_AUTH=false;
static  $OJ_WEIBO_AKEY='1124518951';
static  $OJ_WEIBO_ASEC='df709a1253ef8878548920718085e84b';
static  $OJ_WEIBO_CBURL='http://192.168.0.108/judge/login_weibo.php';

/* renren config here */
static  $OJ_RR_AUTH=false;
static  $OJ_RR_AKEY='d066ad780742404d85d0955ac05654df';
static  $OJ_RR_ASEC='c4d2988cf5c149fabf8098f32f9b49ed';
static  $OJ_RR_CBURL='http://192.168.0.108/judge/login_renren.php';
/* qq config here */
static  $OJ_QQ_AUTH=false;
static  $OJ_QQ_AKEY='1124518951';
static  $OJ_QQ_ASEC='df709a1253ef8878548920718085e84b';
static  $OJ_QQ_CBURL='192.168.0.108';


//if(date('H')<5||date('H')>21||isset($_GET['dark'])) $OJ_CSS="dark.css";
if (isset($_SESSION['OJ_LANG'])) $OJ_LANG=$_SESSION['OJ_LANG'];
global $mysqli;
	if($OJ_SAE)	{
		$OJ_DATA="saestor://data/";
	//  for sae.sina.com.cn
		$mysqli=mysqli_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
		$DB_NAME=SAE_MYSQL_DB;
	}else{
		//for normal install
		if(($mysqli=mysqli_connect($DB_HOST,$DB_USER,$DB_PASS))==null) 
			die('Could not connect: ' . mysqli_error());
	}
	// use db
	mysqli_query($mysqli,"set names utf8");
  //if(!$OJ_SAE)mysqli_set_charset("utf8");
	
	if(!mysqli_select_db($mysqli,$DB_NAME))
		die('Can\'t use foo : ' . mysqli_error());
		
	if(isset($OJ_CSRF)&&$OJ_CSRF&&$OJ_TEMPLATE=="bs3"&&basename($_SERVER['PHP_SELF'])!="problem_judge")
		 require_once('csrf_check.php');

	//sychronize php and mysql server with timezone settings, dafault setting for China
	//if you are not from China, comment out these two lines or modify them.
	date_default_timezone_set("PRC");
	mysqli_query($mysqli,"SET time_zone ='+8:00'");?>
