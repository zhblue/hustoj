<?php @session_start();
	ini_set("display_errors","Off");  //set this to "On" for debugging  ,especially when no reason blank shows up.
	ini_set("session.cookie_httponly", 1);   
	header('X-Frame-Options:SAMEORIGIN');

//for people using hustoj out of China , be careful of the last two line of this file !

// connect db 
static 	$DB_HOST="localhost";
static 	$DB_NAME="jol";
static 	$DB_USER="root";
static 	$DB_PASS="root";

static 	$OJ_NAME="HUSTOJ";
static 	$OJ_HOME="./";
static 	$OJ_ADMIN="root@localhost";
static 	$OJ_DATA="/home/judge/data";
static 	$OJ_BBS="discuss3";//"bbs" for phpBB3 bridge or "discuss" for mini-forum
static  $OJ_ONLINE=false;
static  $OJ_LANG="cn";
static  $OJ_SIM=false; 
static  $OJ_DICT=false;
static  $OJ_LANGMASK=0; //1mC 2mCPP 4mPascal 8mJava 16mRuby 32mBash 1008 for security reason to mask all other language
static  $OJ_EDITE_AREA=true;//true: syntax highlighting is active
static  $OJ_AUTO_SHARE=false;//true: One can view all AC submit if he/she has ACed it onece.
static  $OJ_CSS="white.css";
static  $OJ_SAE=false; //using sina application engine
static  $OJ_VCODE=false;
static  $OJ_APPENDCODE=false;
static  $OJ_CE_PENALTY=false;
static  $OJ_MEMCACHE=false;
static  $OJ_MEMSERVER="127.0.0.1";
static  $OJ_MEMPORT=11211;
static  $OJ_REDIS=false;
static  $OJ_REDISSERVER="127.0.0.1";
static  $OJ_REDISPORT=6379;
static  $OJ_REDISQNAME="hustoj";
static  $SAE_STORAGE_ROOT="http://hustoj-web.stor.sinaapp.com/";
static  $OJ_TEMPLATE="bs3"; //使用的默认模板
if(isset($_GET['tp'])) $OJ_TEMPLATE=$_GET['tp'];
static  $OJ_LOGIN_MOD="hustoj";
static  $OJ_REGISTER=true; //允许注册新用户
static  $OJ_REG_NEED_CONFIRM=false; //新注册用户需要审核
static  $OJ_RANK_LOCK_PERCENT=0; //比赛封榜时间比例
static  $OJ_SHOW_DIFF=false; //是否显示WA的对比说明
static  $OJ_TEST_RUN=false; //提交界面是否允许测试运行
static  $OJ_BLOCKLY=false; //是否启用Blockly界面
static  $OJ_ENCODE_SUBMIT=false; //是否启用base64编码提交的功能，用来回避WAF防火墙误拦截。
static $OJ_OPENID_PWD = '8a367fe87b1e406ea8e94d7d508dcf01';

/* weibo config here */
static  $OJ_WEIBO_AUTH=false;
static  $OJ_WEIBO_AKEY='1124518951';
static  $OJ_WEIBO_ASEC='df709a1253ef8878548920718085e84b';
static  $OJ_WEIBO_CBURL='http://192.168.0.108/JudgeOnline/login_weibo.php';

/* renren config here */
static  $OJ_RR_AUTH=false;
static  $OJ_RR_AKEY='d066ad780742404d85d0955ac05654df';
static  $OJ_RR_ASEC='c4d2988cf5c149fabf8098f32f9b49ed';
static  $OJ_RR_CBURL='http://192.168.0.108/JudgeOnline/login_renren.php';
/* qq config here */
static  $OJ_QQ_AUTH=false;
static  $OJ_QQ_AKEY='1124518951';
static  $OJ_QQ_ASEC='df709a1253ef8878548920718085e84b';
static  $OJ_QQ_CBURL='192.168.0.108';

//题库市场账号配置
static  $OJ_MARKET_HOST = "http://tk.wxy1.cn";
static  $OJ_MARKET_USERNAME = "hustoj";
static  $OJ_MARKET_PASSWORD = "e10adc3949ba59abbe56e057f20f883e";

$filename = dirname(__FILE__)."/../config/system.conf.php";
$config = parse_ini_file($filename);
if($config)
{
    $DB_HOST=$config['DB_HOST'];
    $DB_NAME=$config['DB_NAME'];
    $DB_USER=$config['DB_USER'];
    $DB_PASS=$config['DB_PASS'];

	$OJ_NAME = $config['OJ_NAME'];

	$OJ_HOME = $config['OJ_HOME'];
	$OJ_BBS = $config['OJ_BBS'];
	$OJ_ONLINE =  boolval($config['OJ_ONLINE']);
	$OJ_LANG = $config['OJ_LANG'];
	$OJ_SIM = boolval($config['OJ_SIM']);
	$OJ_DICT = boolval($config['OJ_DICT']);
	$OJ_LANGMASK = intval($config['OJ_LANGMASK']);
	$OJ_EDITE_AREA = boolval($config['OJ_EDITE_AREA']);
	$OJ_AUTO_SHARE = boolval($config['OJ_AUTO_SHARE']);
	$OJ_CSS = $config['OJ_CSS'];
	$OJ_VCODE = boolval($config['OJ_VCODE']);
	$OJ_APPENDCODE = boolval($config['OJ_APPENDCODE']);
	$OJ_CE_PENALTY = boolval($config['OJ_CE_PENALTY']);
	$OJ_TEMPLATE = $config['OJ_TEMPLATE'];

	$OJ_LOGIN_MOD = $config['OJ_LOGIN_MOD'];
	$OJ_REGISTER = boolval($config['OJ_REGISTER']);
	$OJ_REG_NEED_CONFIRM = boolval($config['OJ_REG_NEED_CONFIRM']);
	$OJ_RANK_LOCK_PERCENT = intval($config['OJ_RANK_LOCK_PERCENT']);
	$OJ_SHOW_DIFF = boolval($config['OJ_SHOW_DIFF']);
	$OJ_TEST_RUN = boolval($config['OJ_TEST_RUN']);
	$OJ_BLOCKLY = boolval($config['OJ_BLOCKLY']);
	$OJ_ENCODE_SUBMIT = boolval($config['OJ_ENCODE_SUBMIT']);

	$OJ_MARKET_HOST = $config['OJ_MARKET_HOST'];
	$OJ_MARKET_USERNAME = $config['OJ_MARKET_USERNAME'];
	$OJ_MARKET_PASSWORD = $config['OJ_MARKET_PASSWORD'];
}

//if(date('H')<5||date('H')>21||isset($_GET['dark'])) $OJ_CSS="dark.css";
if( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && strstr($_SERVER['HTTP_ACCEPT_LANGUAGE'],"zh-CN")) {
        $OJ_LANG="cn";
}
if (isset($_SESSION[$OJ_NAME.'_'.'OJ_LANG'])) $OJ_LANG=$_SESSION[$OJ_NAME.'_'.'OJ_LANG'];


function pdo_query($sql){
    $num_args = func_num_args();
    $args = func_get_args();       //获得传入的所有参数的数组
    $args=array_slice($args,1,--$num_args);
    
    global $DB_HOST,$DB_NAME,$DB_USER,$DB_PASS,$dbh,$OJ_SAE;
    if(!$dbh){
			
		if(isset($OJ_SAE)&&$OJ_SAE)	{
			$OJ_DATA="saestor://data/";
		//  for sae.sina.com.cn
			$DB_NAME=SAE_MYSQL_DB;
			$dbh=new PDO("mysql:host=".SAE_MYSQL_HOST_M.';dbname='.SAE_MYSQL_DB, SAE_MYSQL_USER, SAE_MYSQL_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
		}else{
			$dbh=new PDO("mysql:host=".$DB_HOST.';dbname='.$DB_NAME, $DB_USER, $DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8"));
		}
		
    }
   
    $sth = $dbh->prepare($sql);
    $sth->execute($args);
    $result=array();
    if(stripos($sql,"select") === 0){
        $result=$sth->fetchAll();
    }else if(stripos($sql,"insert") === 0){
	$result=$dbh->lastInsertId();
    }else{
        $result=$sth->rowCount();
    }
    //print_r($result);
    $sth->closeCursor();
    return $result;
}
		// use db
	//pdo_query("set names utf8");	
		
	if(isset($OJ_CSRF)&&$OJ_CSRF&&$OJ_TEMPLATE=="bs3"&&basename($_SERVER['PHP_SELF'])!="problem_judge")
		 require_once('csrf_check.php');

	//sychronize php and mysql server with timezone settings, dafault setting for China
	//if you are not from China, comment out these two lines or modify them.
	//date_default_timezone_set("PRC");
	//pdo_query("SET time_zone ='+8:00'");

?>
