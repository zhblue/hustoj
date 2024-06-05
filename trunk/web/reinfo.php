<?php
$cache_time = 10;
$OJ_CACHE_SHARE = false;

require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');

$view_title = "Welcome To Online Judge";

if(!isset($_SESSION[$OJ_NAME.'_'.'user_id'])){
  header("location:loginpage.php");
  exit(0);
} 

require_once("./include/const.inc.php");

if(!isset($_GET['sid'])){
  echo "No such code!\n";
  require_once("oj-footer.php");
  exit(0);
}

function is_valid($str2){
  global $_SESSION,$OJ_NAME,$OJ_FRIENDLY_LEVEL;
  if(isset($_SESSION[$OJ_NAME.'_'.'source_browser'])) return true;
    //return true; // 如果希望能让任何人都查看对比和RE,放开行首注释，并设定$OJ_SHOW_DIFF=true; if you fail to view diff , try remove the // at beginning of this line.
  if($OJ_FRIENDLY_LEVEL>3) return true;
  
  $n = strlen($str2);
  $str = str_split($str2);
  $m = 1;
  for($i=0; $i<$n; $i++){
    if(is_numeric($str[$i]))
      $m++;
  }
  return $n/$m>3;
}

if(!isset($_SESSION[$OJ_NAME.'_'.'user_id'])){
  $view_errors = $MSG_WARNING_ACCESS_DENIED ;
  require("template/".$OJ_TEMPLATE."/error.php");
  exit(0);
}

$ok = false;
$id = strval(intval($_GET['sid']));

$sql = "SELECT * FROM `solution` WHERE `solution_id`=?";
$result = pdo_query($sql,$id);
$row = $result[0];
$lang = $row['language'];
$contest_id = intval($row['contest_id']);
$isRE = $row['result']==10;
$isAC = $row['result']==4 ;
if((isset($_SESSION[$OJ_NAME.'_'.'user_id']) && $row && ($row['user_id']==$_SESSION[$OJ_NAME.'_'.'user_id']))||isset($_SESSION[$OJ_NAME.'_'.'source_browser']))
{
  $ok = true;
}
$spj=pdo_query("select spj from problem where problem_id=?",$row['problem_id']);
if(!empty($spj)&&$spj[0][0]==2 && $OJ_HIDE_RIGHT_ANSWER && !isset($_SESSION[$OJ_NAME.'_'.'source_browser']) ){
    echo $MSG_WARNING_ACCESS_DENIED;
    $ok = false;
}
$view_reinfo = "";
if(  ($ok && $OJ_FRIENDLY_LEVEL>2) ||
    (
      isset($_SESSION[$OJ_NAME.'_'.'source_browser']) || ($ok&&$contest_id==0&& // 防止打表过数据弱的题目
  !(                                                                                   // 默认禁止比赛中查看WA对比和RE详情
    (isset($OJ_EXAM_CONTEST_ID)&&$OJ_EXAM_CONTEST_ID>0)||                              // 如果希望教学中无论练习或比赛均开放数据对比与运行错误，可以将这里
    (isset($OJ_ON_SITE_CONTEST_ID)&&$OJ_ON_SITE_CONTEST_ID>0)                          // 的所有条件简化为 $ok，即63行到69行简化为: if($ok){
  ))  
     )                   // if you want a friendly WA and RE, change line 63-69 to "if($ok){"
  ){

  if($row['user_id']!=$_SESSION[$OJ_NAME.'_'.'user_id']){
    $view_mail_link= "<a href='mail.php?to_user=".htmlentities($row['user_id'],ENT_QUOTES,"UTF-8")."&title=$MSG_SUBMIT $id'>Mail the auther</a>";
  }
  
  $sql = "SELECT `error` FROM `runtimeinfo` WHERE `solution_id`=?";
  $result = pdo_query($sql,$id);

  if(isset($result[0])){
    $row = $result[0];
    $view_reinfo=htmlentities(str_replace("\n\r","\n",$row['error']),ENT_QUOTES,"UTF-8");
  }

  if(strpos($row['error'],"judge/")!==false&&!isset($_SESSION[$OJ_NAME."_administrator"])) $view_reinfo =  "潜在的数组或指针越界，请检查代码。";
  else if(strpos($row['error'],"php")!==false) $view_reinfo =  "error2";
  else if(strpos($row['error'],"PASS")!==false) $view_reinfo =  "error3";
  else if($OJ_SHOW_DIFF && $row && ($ok||$isRE) && ($OJ_TEST_RUN||is_valid($row['error'])||$ok)){ 
    $view_reinfo = htmlentities(str_replace("\n\r","\n",$row['error']),ENT_QUOTES,"UTF-8");
    $sql="select pass_rate*100 from solution where solution_id=?";
    $result = pdo_query($sql,$id);
    if(count($result)>0) $mark=$result[0][0];
    if($isAC) $mark=100;
    $view_reinfo .="<br>$MSG_MARK:$mark";

  }
  else{
    $view_errors = $MSG_WARNING_ACCESS_DENIED;
    //$view_reinfo = "出于数据保密原因，当前错误提示不可查看，如果希望能让任何人都查看对比和运行错误,请管理员配置\$OJ_SHOW_DIFF=true;<br>然后编辑本文件，开放18行首注释，令is_valid总是返回true。 <br>\n Sorry , not available (RE:".$isRE.",OJ_SHOW_DIFF:".$OJ_SHOW_DIFF.",TR:".$OJ_TEST_RUN.",valid:".is_valid($row['error']).")";
  }
}
else{
  $view_errors = $MSG_WARNING_ACCESS_DENIED;
  require("template/".$OJ_TEMPLATE."/error.php");
  exit(0);
}

/////////////////////////Template

if(!isset($_SESSION[$OJ_NAME.'_'.'source_browser']) && $OJ_SHOW_DIFF==false){
  $view_errors = $MSG_WARNING_ACCESS_DENIED;
  require("template/".$OJ_TEMPLATE."/error.php");
  exit(0);
}
else{
  require("template/".$OJ_TEMPLATE."/reinfo.php");
}
/////////////////////////Common foot
if(file_exists('./include/cache_end.php')){
  require_once('./include/cache_end.php');
}
?>
