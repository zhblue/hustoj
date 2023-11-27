<?php session_start();
require_once "include/db_info.inc.php";
require_once "include/my_func.inc.php";
require_once "include/email.class.php";
require_once "include/base64.php";

if(isset($OJ_CSRF) && $OJ_CSRF && $OJ_TEMPLATE=="bs3" && !isset($_SESSION[$OJ_NAME.'_'.'http_judge']))
  require_once(dirname(__FILE__)."/include/csrf_check.php");

if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
 
	$view_errors = "<a href=loginpage.php>$MSG_Login</a>";
	require("template/".$OJ_TEMPLATE."/error.php");
	exit(0);
}

require_once "include/memcache.php";
require_once "include/const.inc.php";

$now =  date('Y-m-d H:i:s', time());
$user_id = $_SESSION[$OJ_NAME.'_'.'user_id'];
$language = intval($_POST['language']);

if (!$OJ_BENCHMARK_MODE) {
  $sql = "SELECT count(1) FROM `solution` WHERE result<4";
  $result = mysql_query_cache($sql);
  $row = $result[0];

  if ($row[0] > 50) {
    $OJ_VCODE = true;
  }

  if ($OJ_VCODE) {
    $vcode = $_POST["vcode"];
  }

  $err_str = "";
  $err_cnt = 0;

  if ($OJ_VCODE && ($_SESSION[$OJ_NAME.'_'."vcode"]==null || $vcode!=$_SESSION[$OJ_NAME.'_'."vcode"] || $vcode=="" || $vcode==null)) {
    $_SESSION[$OJ_NAME.'_'."vcode"] = null;
    $err_str = $err_str.$MSG_VCODE_WRONG."\\n";
    $err_cnt++;
    $view_errors = $err_str;
    $_SESSION[ $OJ_NAME . '_' . "vfail" ]=true;
    require "template/".$OJ_TEMPLATE."/error.php";
    exit(0);
  }else{
          $_SESSION[ $OJ_NAME . '_' . "vfail" ]=false;
  }

}

$test_run = false;
if (isset($_POST['cid'])) {
  $pid = intval($_POST['pid']);
  $cid = intval($_POST['cid']);
  $test_run = $cid<0;
  if($test_run) $cid =-$cid ;
  $sql = "SELECT `problem_id`,'N' FROM `contest_problem` WHERE `num`='$pid' AND contest_id=$cid";
}
else {
  $id = intval($_POST['id']);
  $test_run = $id<=0;
  $sql = "SELECT `problem_id`,defunct FROM `problem` WHERE `problem_id`='$id' ";
    
  if(!($test_run||isset($_SESSION[$OJ_NAME.'_'.'administrator'])))
    $sql .= " and defunct='N'";
}

//echo $sql;
if(!$test_run){
	$res = mysql_query_cache($sql);
	if (isset($res) && count($res)<1 && !isset($_SESSION[$OJ_NAME.'_'.'administrator']) && !((isset($cid)&&$cid<=0) || (isset($id)&&$id<=0))) {
	  $view_errors = $MSG_LINK_ERROR."<br>";
	  require "template/".$OJ_TEMPLATE."/error.php";
	  exit(0);
	}
}
if ($res[0][1]!='N' && !($test_run||isset($_SESSION[$OJ_NAME.'_'.'administrator']))) {
   // echo "res:$res,count:".count($res);
   //  echo "$sql";
  $view_errors = $MSG_PROBLEM_RESERVED."<br>";

  if (isset($_POST['ajax'])) {
    echo $view_errors.$res[0][1];
    exit(0);
  }
  else {
    require "template/".$OJ_TEMPLATE."/error.php";
  }
  exit(0);
}

$title = "";

if (isset($_POST['id'])) {
  $id = intval($_POST['id']);
  if($id<=0) $id=-$id;
  $langmask = $OJ_LANGMASK;
}
else if (isset($_POST['pid']) && isset($_POST['cid']) && $_POST['cid']!=0) {
  $pid = intval($_POST['pid']);
  $cid = intval($_POST['cid']);

  if ($test_run) {
    $cid = -$cid;
  }

  //check user if private
  $sql = "SELECT `private`,langmask,title FROM `contest` WHERE `contest_id`=$cid AND `start_time`<='$now' AND `end_time`>'$now'";
  //"SELECT `private`,langmask FROM `contest` WHERE `contest_id`=? AND `start_time`<=? AND `end_time`>?";
  //$result = pdo_query($sql, $cid, $now, $now);

  $result = mysql_query_cache($sql);
  $rows_cnt = count($result);

  if ($rows_cnt != 1) {
    $view_errors .= $MSG_NOT_IN_CONTEST;

    require "template/" . $OJ_TEMPLATE . "/error.php";
    exit(0);
  }
  else {
    $row = $result[0];
    $isprivate = intval($row[0]);
    $langmask = $row[1];
    $title = $row[2];

    if ($isprivate==1 && !isset($_SESSION[$OJ_NAME.'_'.'c'.$cid])) {
      $sql = "SELECT count(*) FROM `privilege` WHERE `user_id`=? AND `rightstr`=?";
      $result = pdo_query($sql, $user_id, "c$cid");

      $row = $result[0];
      $ccnt = intval($row[0]);

      if ($ccnt==0 && !isset($_SESSION[$OJ_NAME.'_'.'administrator'])) {
        $view_errors = $MSG_NOT_INVITED."\n";
        require "template/" . $OJ_TEMPLATE . "/error.php";
        exit(0);
      }
    }
  }

  $sql = "SELECT `problem_id` FROM `contest_problem` WHERE `contest_id`=? AND `num`=?";
  $result = pdo_query($sql, $cid, $pid);

  $rows_cnt = count($result);

  if ($rows_cnt != 1) {
    $view_errors = $MSG_NO_PROBLEM."\n";
    require "template/".$OJ_TEMPLATE."/error.php";
    exit(0);
  }
  else {
    $row = $result[0];
    $id = intval($row['problem_id']);
    
    if ($test_run) {
      $id = -$id;
    }
  }
  
}
else {
  $id = 0;
  /*
  $view_errors= "No Such Problem!\n";
  require("template/".$OJ_TEMPLATE."/error.php");
  exit(0);
  */
  $langmask = $OJ_LANGMASK;
  $test_run = true;
}

if ($language > count($language_name) || $language < 0) {
  $language = 0;
}

$language = strval($language);

if ($langmask&(1<<$language)) {
  $view_errors = $MSG_NO_PLS."\n[$language][$langmask][".($langmask&(1<<$language))."]";
  require "template/".$OJ_TEMPLATE."/error.php";
  exit(0);
}

$source = $_POST['source'];
$input_text = "";

if (isset($_POST['input_text'])) {
  $input_text = $_POST['input_text'];
}


if (isset($_POST['encoded_submit'])) {

  $source = decode64($source);
}

$input_text = preg_replace("(\r\n)", "\n", $input_text);
$source = $source;
$input_text = $input_text;
$source_user = $source;

if ($test_run) {
  $id = -$id;
}

//use append Main code
$prepend_file = "$OJ_DATA/$id/prepend.".$language_ext[$language];

if (isset($OJ_APPENDCODE) && $OJ_APPENDCODE && file_exists($prepend_file)) {
  $source = file_get_contents($prepend_file)."\n".$source;
}

$append_file = "$OJ_DATA/$id/append.".$language_ext[$language];
//echo $append_file;

if (isset($OJ_APPENDCODE) && $OJ_APPENDCODE && file_exists($append_file)) {
  $source .= "\n".file_get_contents($append_file);
//echo "$source";
}
//end of append

$spj=pdo_query("select spj from problem where problem_id=?",$id);
if(count($spj)>0) $spj=$spj[0][0];

if ($language == 6 && $spj!= 2) {
  $source = "# coding=utf-8\n".$source;
}

if ($test_run) {
  $id = 0;
}

$len = strlen($source);
//echo $source;

setcookie('lastlang', $language, time()+360000);

$ip = $_SERVER['REMOTE_ADDR'];

if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR'];
  $tmp_ip = explode(',', $REMOTE_ADDR);
  $ip = htmlentities($tmp_ip[0], ENT_QUOTES, "UTF-8");
}

if ($len < 2) {
  $view_errors = $MSG_TOO_SHORT."<br>";
  require "template/".$OJ_TEMPLATE."/error.php";
  exit(0);
}

if ($len > 65536) {
  $view_errors = $MSG_TOO_LONG."<br>";
  require "template/" . $OJ_TEMPLATE . "/error.php";
  exit(0);
}

if (!$OJ_BENCHMARK_MODE) {
  // last submit
  if(!isset($OJ_SUBMIT_COOLDOWN_TIME))  $OJ_SUBMIT_COOLDOWN_TIME = 5;
  $now =  date('Y-m-d H:i:s', time()-$OJ_SUBMIT_COOLDOWN_TIME);
  $sql = "SELECT `in_date`,solution_id FROM `solution` WHERE `user_id`=? AND in_date>? ORDER BY `in_date` DESC LIMIT 1";
  $res = pdo_query($sql, $user_id, $now);

  if (count($res)>=1) {
    /*
    $view_errors = $MSG_BREAK_TIME."<br>";
    require "template/".$OJ_TEMPLATE."/error.php";
    exit(0);
       // 预防WAF抽风
    */
	$statusURI = "status.php?user_id=".$_SESSION[$OJ_NAME.'_'.'user_id'];
	if (isset($cid)) {
		if(isset($_GET['spa'])) $statusURI .="&spa";
		$statusURI .= "&cid=$cid&fixed=";
	}
	if (!$test_run) {
		header("Location: $statusURI");
	} else {
		echo $res[0][1];
	}
	exit();
   }
}

if (~$OJ_LANGMASK&(1<<$language)) {
  $sql = "SELECT nick FROM users WHERE user_id=?";
  $nick = pdo_query($sql, $user_id);

  if ($nick) {
    $nick = $nick[0][0];
  }
  else {
    $nick = "Guest";
  }

  if (!isset($pid)) {
    $sql = "INSERT INTO solution(problem_id,user_id,nick,in_date,language,ip,code_length,result) VALUES(?,?,?,NOW(),?,?,?,14)";
    $insert_id = pdo_query($sql, $id, $user_id, $nick, $language, $ip, $len);
  }
  else {
    $sql = "INSERT INTO solution(problem_id,user_id,nick,in_date,language,ip,code_length,contest_id,num,result) VALUES(?,?,?,NOW(),?,?,?,?,?,14)";

    if ((stripos($title,$OJ_NOIP_KEYWORD)!==false) && isset($OJ_OI_1_SOLUTION_ONLY) && $OJ_OI_1_SOLUTION_ONLY) {
      $delete = pdo_query("DELETE FROM solution WHERE contest_id=? AND user_id=? AND num=?", $cid, $user_id, $pid);

      if ($delete>0) {
        $sql_fix = "UPDATE problem p INNER JOIN (SELECT problem_id pid ,count(1) ac FROM solution WHERE problem_id=? AND result=4) s ON p.problem_id=s.pid SET p.accepted=s.ac;";        
        $fixed = pdo_query($sql_fix,$id);
        $sql_fix = "UPDATE problem p INNER JOIN (SELECT problem_id pid ,count(1) submit FROM solution WHERE problem_id=?) s ON p.problem_id=s.pid SET p.submit=s.submit;";
        $fixed = pdo_query($sql_fix,$id);
      }
    }

    $insert_id = pdo_query($sql, $id, $user_id, $nick, $language, $ip, $len, $cid, $pid);
  }

  $sql = "INSERT INTO `source_code_user`(`solution_id`,`source`) VALUES(?,?)";
  pdo_query($sql, $insert_id, $source_user);

  $sql = "INSERT INTO `source_code`(`solution_id`,`source`) VALUES(?,?)";
  pdo_query($sql, $insert_id, $source);

  if ($test_run) {
    $sql = "INSERT INTO `custominput`(`solution_id`,`input_text`) VALUES(?,?)";
    pdo_query($sql, $insert_id, $input_text);
  }
  else {
    $sql = "UPDATE problem SET submit=submit+1 WHERE problem_id=?";
    pdo_query($sql,$id);

    if (isset($cid) && $cid>0) {
      $sql = "UPDATE contest_problem SET c_submit=c_submit+1 WHERE contest_id=? AND num=?";
      pdo_query($sql,$cid,$pid);
    }
  }


 ////remote oj
  $result=0;
  $sql="select remote_oj from problem where problem_id=?";
  $remote_oj=pdo_query($sql,$id);
  if(isset($remote_oj[0])){
          $remote_oj=$remote_oj[0][0];
          if($remote_oj!=""){
                $result=16;
                $sql="update solution set result=16,remote_oj=? where solution_id=?";
                pdo_query($sql,$remote_oj,$insert_id);
          }
  }
  
  ////poison robot account,give system resources to the REAL people
  if(isset($OJ_POISON_BOT_COUNT) && $OJ_POISON_BOT_COUNT >0 && $id>0 &&
          !(isset($_SESSION[$OJ_NAME."_administrator"])||
            isset($_SESSION[$OJ_NAME."_source_browser"])||
            isset($_SESSION[$OJ_NAME."_contest_creator"])||
            isset($_SESSION[$OJ_NAME."_problem_editor"])
           )
    ){
        $sql="select count(1) from solution where user_id=? and result=4 and problem_id=?";
        $count=pdo_query($sql,$user_id,$id);
        if($count) $count=$count[0][0];
        if($count>=$OJ_POISON_BOT_COUNT){
                $result=rand(5,11);
                $memory=rand(100,2000);
                $time=rand(100,2000);
                $sql="update solution set memory=?,time=?,judger='poisoner' where solution_id=?";
                pdo_query($sql,$memory,$time,$insert_id);
                if( $OJ_ADMIN != "root@localhost" ){
                         email($OJ_ADMIN,$MSG_SYS_WARN,"$DOMAIN $MSG_USER $user_id $MSG_IS_ROBOT");
                }
        }
        /*   //prepare system ready for even worse robots
        $now =  date('Y-m-d H:i', time()-$OJ_SUBMIT_COOLDOWN_TIME * 6 );
        $sql="select count(1) from solution where user_id=? and in_date > ?";
        $count=pdo_query($sql,$user_id,$now);
        if($count>=$OJ_POISON_BOT_COUNT){
                $sql="update users set defunct='Y' where user_id=?";
                pdo_query($sql,$_SESSION[$OJ_NAME."_user_id"]);
                $sql="select ip from users where user_id=? ";
                $ip=pdo_query($sql,$_SESSION[$OJ_NAME."_user_id"]);
                if(count($ip)>0){
                        $ip=$ip[0][0];
                        if($ip!="" && $ip!="127.0.0.1"){
                                $sql="update users set defunct='Y' where ip=?";
                                pdo_query($sql,$ip);
                        }
                }
                unset($_SESSION[$OJ_NAME.'_'.'user_id']);
                setcookie($OJ_NAME."_user","");
                setcookie($OJ_NAME."_check","");
                session_destroy();
                header("Location:index.php");
        }
        */
  }

  $sql = "UPDATE solution SET result=? WHERE solution_id=?";
  pdo_query($sql,$result,$insert_id);


  //using redis task queue
  if ( $OJ_REDIS && $result==0 ) {
    $redis = new Redis();
    $redis->connect($OJ_REDISSERVER, $OJ_REDISPORT);
    
    if (isset($OJ_REDISAUTH)) {
      $redis->auth($OJ_REDISAUTH);
    }

    $redis->lpush($OJ_REDISQNAME, $insert_id);
    $redis->close();
  }
}

if (isset($OJ_UDP) && $OJ_UDP && $result==0 ) {      
           trigger_judge($insert_id);     // moved to my_func.inc.php
}

if ($OJ_BENCHMARK_MODE) {
  echo $insert_id;
  exit(0);
}

$statusURI = strstr($_SERVER['REQUEST_URI'], "submit", true)."status.php";

if (isset($cid)) {
  $statusURI .= "?cid=$cid";
}

$sid = "";
if (isset($_SESSION[$OJ_NAME.'_'.'user_id'])) {
  $sid .= session_id().$_SERVER['REMOTE_ADDR'];
}

if (isset($_SERVER["REQUEST_URI"])) {
  $sid .= $statusURI;
}
//echo $statusURI."<br>";

$sid = md5($sid);
$file = "cache/cache_$sid.html";
//echo $file;

if ($OJ_MEMCACHE) {
  $mem = new Memcache();
    
  if ($OJ_SAE) {
    $mem = memcache_init();
  }
  else {
    $mem->connect($OJ_MEMSERVER, $OJ_MEMPORT);
  }
    
  $mem->delete($file, 0);
}
elseif (file_exists($file)) {
  unlink($file);
}
//echo $file;

$statusURI = "status.php?user_id=".$_SESSION[$OJ_NAME.'_'.'user_id'];
if(isset($_GET['spa'])) $statusURI .="&spa";

if (isset($cid)) {
  $statusURI .= "&cid=$cid&fixed=";
}

if (!$test_run&&!isset($_GET['ajax'])) {
  header("Location: $statusURI");
}
else {
  if (isset($_GET['ajax'])) {
    echo $insert_id;
  }
  else {
    ?>
    <script>window.parent.setTimeout("fresh_result('<?php echo $insert_id; ?>')",1000);</script><?php
  }
}
?>
