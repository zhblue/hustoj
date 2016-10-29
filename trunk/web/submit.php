<?php session_start();
if (!isset($_SESSION['user_id'])){
  require_once("oj-header.php");
  echo "<a href='loginpage.php'>Please Login First!</a>";
  require_once("oj-footer.php");
  exit(0);
}
require_once("include/db_info.inc.php");
require_once("include/const.inc.php");
$now=strftime("%Y-%m-%d %H:%M",time());
$user_id=$_SESSION['user_id'];

if (isset($_POST['cid'])){
  $pid=intval($_POST['pid']);
  $cid=intval($_POST['cid']);
  $sql="SELECT `problem_id` from `contest_problem` 
    where `num`='$pid' and contest_id=$cid";
}else{
  $id=intval($_POST['id']);
  $sql="SELECT `problem_id` from `problem` where `problem_id`='$id' and problem_id not in (select distinct problem_id from contest_problem where `contest_id` IN (
    SELECT `contest_id` FROM `contest` WHERE 
    (`end_time`>'$now' or private=1)and `defunct`='N'
  ))";
  if(!isset($_SESSION['administrator']))
    $sql.=" and defunct='N'";
}
//echo $sql;	

$res=mysql_query($sql);
if ($res&&mysql_num_rows($res)<1&&!isset($_SESSION['administrator'])&&!((isset($cid)&&$cid==0)||(isset($id)&&$id==0))){
  mysql_free_result($res);
  $view_errors=  "Where do find this link? No such problem.<br>";
  require("template/".$OJ_TEMPLATE."/error.php");
  exit(0);
}
mysql_free_result($res);



if (isset($_POST['id'])) {
  $id=intval($_POST['id']);

}else if (isset($_POST['pid']) && isset($_POST['cid'])&&$_POST['cid']!=0){
  $pid=intval($_POST['pid']);
  $cid=intval($_POST['cid']);
  // check user if private
  $sql="SELECT `private` FROM `contest` WHERE `contest_id`='$cid' AND `start_time`<='$now' AND `end_time`>'$now'";
  $result=mysql_query($sql);
  $rows_cnt=mysql_num_rows($result);
  if ($rows_cnt!=1){
    echo "You Can't Submit Now Because Your are not invited by the contest or the contest is not running!!";
    mysql_free_result($result);
    require_once("oj-footer.php");
    exit(0);
  }else{
    $row=mysql_fetch_array($result);
    $isprivate=intval($row[0]);
    mysql_free_result($result);
    if ($isprivate==1){
      $sql="SELECT count(*) FROM `privilege` WHERE `user_id`='$user_id' AND `rightstr`='c$cid'";
      $result=mysql_query($sql) or die (mysql_error()); 
      $row=mysql_fetch_array($result);
      $ccnt=intval($row[0]);
      mysql_free_result($result);
      if ($ccnt==0&&!isset($_SESSION['administrator'])){
        $view_errors= "You are not invited!\n";
        require("template/".$OJ_TEMPLATE."/error.php");
        exit(0);
      }
    }
  }
  $sql="SELECT `problem_id` FROM `contest_problem` WHERE `contest_id`='$cid' AND `num`='$pid'";
  $result=mysql_query($sql);
  $rows_cnt=mysql_num_rows($result);
  if ($rows_cnt!=1){
    $view_errors= "No Such Problem!\n";
    require("template/".$OJ_TEMPLATE."/error.php");
    mysql_free_result($result);
    exit(0);
  }else{
    $row=mysql_fetch_object($result);
    $id=intval($row->problem_id);
    mysql_free_result($result);
  }
}else{
  $id=0;
/*
  $view_errors= "No Such Problem!\n";
  require("template/".$OJ_TEMPLATE."/error.php");
  exit(0);
 */
}

$language=intval($_POST['language']);
if ($language>count($language_name) || $language<0) $language=0;
$language=strval($language);


$source=$_POST['source'];
$input_text=$_POST['input_text'];
if(get_magic_quotes_gpc()){
  $source=stripslashes($source);
  $input_text=stripslashes($input_text);

}
$input_text=preg_replace ( "(\r\n)", "\n", $input_text );

$len=strlen($source);

$source=mysql_real_escape_string($source);
$input_text=mysql_real_escape_string($input_text);
//$source=trim($source);
//use append Main code
$append_file="$OJ_DATA/$id/append.$language_ext[$language]";
if(isset($OJ_APPENDCODE)&&$OJ_APPENDCODE&&file_exists($append_file)){
  $source.=mysql_real_escape_string("\n".file_get_contents($append_file));
}
//end of append 


setcookie('lastlang',$language,time()+360000);

$ip=$_SERVER['REMOTE_ADDR'];

if ($len<2){
  $view_errors="Code too short!<br>";
  require("template/".$OJ_TEMPLATE."/error.php");
  exit(0);
}
if (strlen($source)>65536){
  $view_errors="Code too long!<br>";
  require("template/".$OJ_TEMPLATE."/error.php");
  exit(0);
}

// last submit
$now=strftime("%Y-%m-%d %X",time()-10);
$sql="SELECT `in_date` from `solution` where `user_id`='$user_id' and in_date>'$now' order by `in_date` desc limit 1";
$res=mysql_query($sql);
if (mysql_num_rows($res)==1){
  //$row=mysql_fetch_row($res);
  //$last=strtotime($row[0]);
  //$cur=time();
  //if ($cur-$last<10){
  $view_errors="You should not submit more than twice in 10 seconds.....<br>";
  require("template/".$OJ_TEMPLATE."/error.php");
  exit(0);
  //}
}


if((~$OJ_LANGMASK)&(1<<$language)){

  if (!isset($pid)){
    $sql="INSERT INTO solution(problem_id,user_id,in_date,language,ip,code_length)
      VALUES('$id','$user_id',NOW(),'$language','$ip','$len')";
  }else{
    $sql="INSERT INTO solution(problem_id,user_id,in_date,language,ip,code_length,contest_id,num)
      VALUES('$id','$user_id',NOW(),'$language','$ip','$len','$cid','$pid')";
  }
  mysql_query($sql);
  $insert_id=mysql_insert_id();

  $sql="INSERT INTO `source_code`(`solution_id`,`source`)VALUES('$insert_id','$source')";
  mysql_query($sql);

  if($id==0&&(!isset($cid)||$cid==0)){
    $sql="INSERT INTO `custominput`(`solution_id`,`input_text`)VALUES('$insert_id','$input_text')";
    mysql_query($sql);
  }
  //echo $sql;
}


$statusURI=strstr($_SERVER['REQUEST_URI'],"submit",true)."status.php";
if (isset($cid)) 
  $statusURI.="?cid=$cid";

$sid="";
if (isset($_SESSION['user_id'])){
  $sid.=session_id().$_SERVER['REMOTE_ADDR'];
}
if (isset($_SERVER["REQUEST_URI"])){
  $sid.=$statusURI;
}
// echo $statusURI."<br>";

$sid=md5($sid);
$file = "cache/cache_$sid.html";
//echo $file;  
if($OJ_MEMCACHE){
  $mem = new Memcache;
  if($OJ_SAE)
    $mem=memcache_init();
  else{
    $mem->connect($OJ_MEMSERVER,  $OJ_MEMPORT);
  }
  $mem->delete($file,0);
}
else if(file_exists($file)) 
  unlink($file);
//echo $file;

$statusURI="status.php?user_id=".$_SESSION['user_id'];
if (isset($cid))
  $statusURI.="&cid=$cid";

if($id!=0||$cid!=0)	
  header("Location: $statusURI");
else{
?>
  <script>window.parent.setTimeout("fresh_result('<?php echo $insert_id;?>')",2000);</script>
<?php

}
?>
