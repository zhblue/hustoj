<?php @session_start();
//ini_set("display_errors", "Off");  //set this to "On" for debugging  ,especially when no reason blank shows up.
//header('X-Frame-Options:SAMEORIGIN');

//for people using hustoj out of China , be careful of the last two line of this file !

// connect db 
static 	$DB_HOST="localhost";  //数据库服务器ip或域名
static 	$DB_NAME="jol";   //数据库名
static 	$DB_USER="root";  //数据库账户
static 	$DB_PASS="root";  //数据库密码

static 	$OJ_NAME="LongOI";  //左上角显示的系统名称
static 	$OJ_HOME="./";    //主页目录
static 	$OJ_ADMIN="root@localhost";  //管理员email
static 	$OJ_DATA="/home/judge/data";  //测试数据目录
static 	$OJ_BBS=false; //设为"discuss3" 启用， "bbs" for phpBB3 bridge or "discuss" for mini-forum or false for close any 
static  $OJ_ONLINE=false;  //是否记录在线情况
static  $OJ_LANG="en";  //默认语言
static  $OJ_SIM=true;  //显示相似度
static  $OJ_DICT=false; //显示在线翻译
static  $OJ_LANGMASK=1637684; //TIOBE index top 10, calculator :   https://pigeon-developer.github.io/hustoj-langmask/
static  $OJ_EDITE_AREA=true; //true: syntax highlighting is active
static  $OJ_ACE_EDITOR=true;
static  $OJ_AUTO_SHARE=false; //true: One can view all AC submit if he/she has ACed it onece.
static  $OJ_CSS="white.css";
static  $OJ_SAE=false; //using sina application engine
static  $OJ_VCODE=false;  //验证码
static  $OJ_APPENDCODE=false;  // 代码预定模板
if (!$OJ_APPENDCODE) 	ini_set("session.cookie_httponly", 1);   // APPENDCODE模式需要允许javascript操作cookie保存当前语言。
static  $OJ_CE_PENALTY=false;  // 编译错误是否罚时
static  $OJ_PRINTER=false;  //启用打印服务
static  $OJ_MAIL=false; //内邮
static  $OJ_MARK="mark"; // "mark" for right "percent" for WA
static  $OJ_MEMCACHE=false;  //使用内存缓存
static  $OJ_MEMSERVER="127.0.0.1";
static  $OJ_MEMPORT=11211;
static  $OJ_UDP=true;   //使用UDP通知
static  $OJ_UDPSERVER="127.0.0.1";
static  $OJ_UDPPORT=1536;
static  $OJ_REDIS=false;   //使用REDIS队列
static  $OJ_REDISSERVER="127.0.0.1";
static  $OJ_REDISPORT=6379;
static  $OJ_REDISQNAME="hustoj";
static  $SAE_STORAGE_ROOT="http://hustoj-web.stor.sinaapp.com/";
static  $OJ_CDN_URL="";  //  http://cdn.hustoj.com/  https://raw.githubusercontent.com/zhblue/hustoj/master/trunk/web/ 
static  $OJ_TEMPLATE="bs3"; //使用的默认模板, [bs3 ie ace sweet sae mario] work with discuss3, [classic bs] work with discuss
//if(isset($_GET['tp'])) $OJ_TEMPLATE=$_GET['tp'];
if ($OJ_TEMPLATE == "classic") $OJ_CSS="hoj.css";
static  $OJ_LOGIN_MOD="hustoj";
static  $OJ_REGISTER=false; //允许注册新用户
static  $OJ_REG_NEED_CONFIRM=false; //新注册用户需要审核
static  $OJ_NEED_LOGIN=true; //需要登录才能访问
static  $OJ_LONG_LOGIN=true; //启用长时间登录信息保留
static  $OJ_KEEP_TIME="30";  //登录Cookie有效时间(单位:天(day),仅在上一行为true时生效)
static  $OJ_RANK_LOCK_PERCENT=0; //比赛封榜时间比例
static  $OJ_SHOW_DIFF=false; //是否显示WA的对比说明
static  $OJ_DOWNLOAD=false; //是否允许下载WA的测试数据
static  $OJ_TEST_RUN=false; //提交界面是否允许测试运行
static  $OJ_MATHJAX=false;  // 激活mathjax
static  $OJ_BLOCKLY=false; //是否启用Blockly界面
static  $OJ_ENCODE_SUBMIT=false; //是否启用base64编码提交的功能，用来回避WAF防火墙误拦截。
static  $OJ_OI_1_SOLUTION_ONLY=false; //比赛是否采用noip中的仅保留最后一次提交的规则。true则在新提交发生时，将本场比赛该题老的提交删除。
static  $OJ_OI_MODE=false; //是否开启OI比赛模式，禁用排名、状态、统计、用户信息、内邮、论坛等。
static  $OJ_SHOW_METAL=true; //榜单上是否按比例显示奖牌
static  $OJ_RANK_LOCK_DELAY=3600; //赛后封榜持续时间，单位秒。根据实际情况调整，在闭幕式颁奖结束后设为0即可立即解封。
static  $OJ_BENCHMARK_MODE=false; //此选项将影响代码提交，不再有提交间隔限制，提交后会返回solution id

static  $OJ_NOIP_KEYWORD="noip";  // 标题包含此关键词，激活noip模式，赛中不显示结果，仅保留最后一次提交。
static  $OJ_BEIAN=false;  // 如果有备案号，填写备案号

//static  $OJ_EXAM_CONTEST_ID=1000; // 启用考试状态，填写考试比赛ID
//static  $OJ_ON_SITE_CONTEST_ID=1000; //启用现场赛状态，填写现场赛比赛ID

/* share code */
static  $OJ_SHARE_CODE=false; // 代码分享功能
/* recent contest */
static  $OJ_RECENT_CONTEST=false; // "http://algcontest.rainng.com/contests.json" ; // 名校联赛

//$OJ_ON_SITE_TEAM_TOTAL用于根据比例的计算奖牌的队伍总数
//CCPC比赛的一种做法是比赛结束后导出终榜看AC至少1题的不打星的队伍数，现场修改此值即可正确计算奖牌
//0表示根据榜单上的出现的队伍总数计算(包含了AC0题的队伍和打星队伍)
static $OJ_ON_SITE_TEAM_TOTAL=0;

static $OJ_OPENID_PWD='8a367fe87b1e406ea8e94d7d508dcf01';

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

/* log */
$OJ_LOG_FILE="/var/log/hustoj/{$OJ_NAME}.log";
static  $OJ_LOG_ENABLED=false;
static  $OJ_LOG_DATETIME_FORMAT="Y-m-d H:i:s";
static  $OJ_LOG_PID_ENABLED=false;
static  $OJ_LOG_USER_ENABLED=false;
static  $OJ_LOG_URL_ENABLED=false;
static  $OJ_LOG_URL_HOST_ENABLED=false;
static  $OJ_LOG_URL_PARAM_ENABLED=false;
static  $OJ_LOG_TRACE_ENABLED=false;

//if(date('H')<5||date('H')>21||isset($_GET['dark'])) $OJ_CSS="dark.css";
if (isset($_SESSION[$OJ_NAME . '_' . 'OJ_LANG'])) {
	$OJ_LANG=$_SESSION[$OJ_NAME . '_' . 'OJ_LANG'];
} else if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], array("cn", "ug", "en", 'fa', 'ko', 'th'))) {
	$OJ_LANG=$_COOKIE['lang'];
} else if (isset($_GET['lang']) && in_array($_GET['lang'], array("cn", "ug", "en", 'fa', 'ko', 'th'))) {
	$OJ_LANG=$_GET['lang'];
} else if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && strstr($_SERVER['HTTP_ACCEPT_LANGUAGE'], "zh-CN")) {
	$OJ_LANG="cn";
}

require_once(dirname(__FILE__) . "/pdo.php");

// use db
//pdo_query("set names utf8");	



//sychronize php and mysql server with timezone settings, dafault setting for China
//if you are not from China, comment out these two lines or modify them.
//date_default_timezone_set("PRC");
//pdo_query("SET time_zone ='+8:00'");

require_once(dirname(__FILE__) . "/logger.php");

$logger=new Logger($_SESSION[$OJ_NAME . '_' . 'user_id'], 
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
