<?php
////////////////////////////Common head
$cache_time=2;
$OJ_CACHE_SHARE=false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title= "$MSG_STATUS";

require_once("./include/my_func.inc.php");
if(isset($OJ_LANG)){
  require_once("./lang/$OJ_LANG.php");
}
require_once("./include/const.inc.php");

if($OJ_TEMPLATE!="classic"){
  $judge_color=Array("btn gray","text-info","text-warning","text-warning","text-success","text-danger","text-danger","text-warning","text-warning","text-warning","text-warning","text-warning","text-warning","text-info");
}

$str2="";
$lock=false;
$lock_time=date("Y-m-d H:i:s",time());
$sql="SELECT * FROM `solution` WHERE problem_id > 0 ";
$isContest = false;
if (isset($_GET['cid'])){
  $isContest = true;
  $cid=intval($_GET['cid']);
  $sql=$sql." AND `contest_id`='$cid' and num >= 0 ";
  $str2=$str2."&cid=$cid";
  $sql_lock="SELECT `start_time`,`title`,`end_time` FROM `contest` WHERE `contest_id`='$cid'";
  $result=mysql_query($sql_lock) or die(mysql_error());
  $rows_cnt=mysql_num_rows($result);
  $start_time=0;
  $end_time=0;
  if ($rows_cnt>0){
    $row=mysql_fetch_array($result);
    $start_time=strtotime($row[0]);
    $title=$row[1];
    $end_time=strtotime($row[2]);
  }
  $lock_time=$end_time-($end_time-$start_time)*$OJ_RANK_LOCK_PERCENT;
  //$lock_time=date("Y-m-d H:i:s",$lock_time);
  $time_sql="";
  //echo $lock.'-'.date("Y-m-d H:i:s",$lock);
  if(time()>$lock_time&&time()<$end_time){
    //$lock_time=date("Y-m-d H:i:s",$lock_time);
    //echo $time_sql;
    $lock=true;
  }else{
    $lock=false;
  }
}else{
  if(isset($_SESSION['administrator'])||isset($_SESSION['source_browser'])||(isset($_SESSION['user_id'])&&$_GET['user_id']==$_SESSION['user_id'])){
    if ($_SESSION['user_id']!="guest")
      $sql="SELECT * FROM `solution` WHERE contest_id is null ";
  }else{
    $sql="SELECT * FROM `solution` WHERE problem_id > 0 and contest_id is null ";
  }
}
$start_first=true;




// check the problem arg
$problem_id="";
if (isset($_GET['problem_id'])&&$_GET['problem_id']!=""){

  if(isset($_GET['cid'])){
    $problem_id=$_GET['problem_id'];
    $num=strpos($PID,$problem_id);
    $sql=$sql."AND `num`='".$num."' ";
    $str2=$str2."&problem_id=".$problem_id;

  }else{
    $problem_id=strval(intval($_GET['problem_id']));
    if ($problem_id!='0'){
      $sql=$sql."AND `problem_id`='".$problem_id."' ";
      $str2=$str2."&problem_id=".$problem_id;
    }
    else $problem_id="";
  }
}
// check the user_id arg
$user_id="";

if (isset($_GET['user_id'])) {
  $user_id=$_GET['user_id'];
}

if (is_valid_user_name($user_id) && $user_id!=""){
  $sql=$sql."AND `user_id`='".$user_id."' ";
  if ($str2!="") $str2=$str2."&";
  $str2=$str2."user_id=".$user_id;
} else {
  $user_id="";
}

if (isset($_GET['language'])) $language=intval($_GET['language']);
else $language=-1;

if ($language>count($language_ext) || $language<0) $language=-1;
if ($language!=-1){
  $sql=$sql."AND `language`='".strval($language)."' ";
  $str2=$str2."&language=".$language;
}
if (isset($_GET['jresult'])) $result=intval($_GET['jresult']);
else $result=-1;

if ($result>12 || $result<0) $result=-1;
if ($result!=-1&&!$lock){
  $sql=$sql."AND `result`='".strval($result)."' ";
  $str2=$str2."&jresult=".$result;
}



if($OJ_SIM){
  $old=$sql;
  $sql="select * from ($sql order by solution_id desc limit 1000) solution left join `sim` on solution.solution_id=sim.s_id WHERE 1 ";
  if(isset($_GET['showsim'])&&intval($_GET['showsim'])>0){
    $showsim=intval($_GET['showsim']);
    $sql="select * from ($old ) solution
      left join `sim` on solution.solution_id=sim.s_id WHERE result=4 and sim>=$showsim limit 1000";
    $sql="SELECT * FROM ($sql) `solution`
      left join(select solution_id old_s_id,user_id old_user_id from solution limit 1000) old
      on old.old_s_id=sim_s_id WHERE  old_user_id!=user_id and sim_s_id!=solution_id ";
    $str2.="&showsim=$showsim";
  }
}


$prevtop_sql = "";
if (isset($_GET['top'])) {
  $top = strval(intval($_GET['top']));
  if ($top != -1) {
    $prevtop_sql = $sql."AND `solution_id` > '".$top."' ";
    $sql = $sql."AND `solution_id`<='".$top."' ";
  }
}

$sql = $sql." ORDER BY `solution_id` DESC LIMIT 20";

// get prevtop value
$prev_top = -1;
if ($prevtop_sql != "") {
  $prevtop_sql = $prevtop_sql." ORDER BY `solution_id` ASC LIMIT 20";

  $prev_result = mysql_query($prevtop_sql);
  $prev_rows_cnt = 0;
  if($prev_result) $prev_rows_cnt = mysql_num_rows($prev_result);
  for ($i = 0; $i < $prev_rows_cnt; $i++) {
    $prev_row = mysql_fetch_array($prev_result);
    $prev_top = $prev_row['solution_id'];
  }
  mysql_free_result($prev_result);
}

if($OJ_MEMCACHE){
  require("./include/memcache.php");
  $result = mysql_query_cache($sql);// or die("Error! ".mysql_error());
  if($result) $rows_cnt=count($result);
  else $rows_cnt=0;
}else{
  $result = mysql_query($sql);// or die("Error! ".mysql_error());
  if($result) $rows_cnt=mysql_num_rows($result);
  else $rows_cnt=0;
}

$top=$bottom=-1;
$cnt=0;
if ($start_first){
  $row_start=0;
  $row_add=1;
}else{
  $row_start=$rows_cnt-1;
  $row_add=-1;
}

$view_status=Array();

$last=0;
for ($i=0;$i<$rows_cnt;$i++){
  if($OJ_MEMCACHE)
    $row=$result[$i];
  else
    $row=mysql_fetch_array($result);
  //$view_status[$i]=$row;
  if($i==0&&$row['result']<4) $last=$row['solution_id'];


  if ($top==-1) $top=$row['solution_id'];
  $bottom=$row['solution_id'] + 1;
  $flag=(!is_running(intval($row['contest_id']))) ||
    isset($_SESSION['source_browser']) ||
    isset($_SESSION['administrator']) || 
    (isset($_SESSION['user_id'])&&!strcmp($row['user_id'],$_SESSION['user_id']));

  $cnt=1-$cnt;


  $view_status[$i][0]=$row['solution_id'];

  if ($row['contest_id']>0) {
    $view_status[$i][1]= "<a href='contestrank.php?cid=".$row['contest_id']."&user_id=".$row['user_id']."#".$row['user_id']."'>".$row['user_id']."</a>";
  }else{
    $view_status[$i][1]= "<a href='userinfo.php?user=".$row['user_id']."'>".$row['user_id']."</a>";
  }

  if ($row['contest_id']>0) {
    $view_status[$i][2]= "<div class=center><a href='problem.php?cid=".$row['contest_id']."&pid=".$row['num']."'>";
    if(isset($cid)){
      $view_status[$i][2].= $PID[$row['num']];
    }else{
      $view_status[$i][2].= $row['problem_id'];
    }
    $view_status[$i][2].="</div></a>";
  }else{
    $view_status[$i][2]= "<div class=center><a href='problem.php?id=".$row['problem_id']."'>".$row['problem_id']."</a></div>";
  }

  if(isset($_SESSION['http_judge'])) {
    $view_status[$i][3].="<form method=post action=admin/problem_judge.php><input type=hidden name=sid value='".$row['solution_id']."'>";
  }
  if (intval($row['result'])==11 && ((isset($_SESSION['user_id'])&&$row['user_id']==$_SESSION['user_id']) || isset($_SESSION['source_browser']))){
    $view_status[$i][3].= "<a href='ceinfo.php?sid=".$row['solution_id']."' class='".$judge_color[$row['result']]."'  title='$MSG_Click_Detail'>".$MSG_Compile_Error."</a>";
  }else if (((intval($row['result'])==6&&$OJ_SHOW_DIFF)||$row['result']==10||$row['result']==13) && ((isset($_SESSION['user_id'])&&$row['user_id']==$_SESSION['user_id']) || isset($_SESSION['source_browser']))){
    $view_status[$i][3].= "<a href='reinfo.php?sid=".$row['solution_id']."' class='".$judge_color[$row['result']]."' title='$MSG_Click_Detail'>".$judge_result[$row['result']]."</a>";

  }else{
    if(!$lock||$lock_time>$row['in_date']||$row['user_id']==$_SESSION['user_id']){
      if($OJ_SIM&&$row['sim']>80&&$row['sim_s_id']!=$row['s_id']) {
        $view_status[$i][3].= "<span class='".$judge_color[$row['result']]."'>*".$judge_result[$row['result']]."</span>";

        if( isset($_SESSION['source_browser'])){

          $view_status[$i][3].= "<a href=comparesource.php?left=".$row['sim_s_id']."&right=".$row['solution_id']."  class='btn btn-info'  target=original>".$row['sim_s_id']."(".$row['sim']."%)</a>";
        }else{

          $view_status[$i][3].= "<span class='btn btn-info'>".$row['sim_s_id']."</span>";

        }
        if(isset($_GET['showsim'])&&isset($row[13])){
          $view_status[$i][3].= "$row[13]";

        }
      }else{

        $view_status[$i][3].= "<span class='".$judge_color[$row['result']]."'>".$judge_result[$row['result']]."</span>";
      }
    }else{
      echo "<td>----";
    }

  }
  if ($row['result']!=4&&isset($row['pass_rate'])&&$row['pass_rate']>0&&$row['pass_rate']<.98)
    $view_status[$i][3].="<span class='btn btn-info'>". (100-$row['pass_rate']*100)."%</span>";
  if(isset($_SESSION['http_judge'])) {
    //           $view_status[$i][3].="<form method=post action=admin/problem_judge.php><input type=hidden name=sid value='".$row['solution_id']."'>";
    $view_status[$i][3].="<select class='btn input-small' length=4 name=result>";
    $view_status[$i][3].="<option value=0>$MSG_Manual</a>";
    $view_status[$i][3].="<option value=4>$MSG_AC</a>";
    $view_status[$i][3].="<option value=6>$MSG_WA</a>";
    $view_status[$i][3].="</select>";
    $view_status[$i][3].="<input class='btn input-small' title='$MSG_Explain' type='text' name='explain'>";
    $view_status[$i][3].="<input class='btn'  type=submit name=manual value='$MSG_OK'>";
    $view_status[$i][3].="</form>";
  }




  if ($flag){


    if ($row['result']>=4){
      $view_status[$i][4]= "<div id=center class=red>".$row['memory']."</div>";
      $view_status[$i][5]= "<div id=center class=red>".$row['time']."</div>";
      //echo "=========".$row['memory']."========";
    }else{
      $view_status[$i][4]= "---";
      $view_status[$i][5]= "---";

    }
    //echo $row['result'];
    if (!(isset($_SESSION['user_id'])&&strtolower($row['user_id'])==strtolower($_SESSION['user_id']) || isset($_SESSION['source_browser']))){
      $view_status[$i][6]=$language_name[$row['language']];
    }else{

      $view_status[$i][6]= "<a href=showsource.php?id=".$row['solution_id'].">".$language_name[$row['language']]."</a>/";

      if (isset($cid)) {
        $view_status[$i][6].= "<a href=\"submitpage.php?cid=".$cid."&pid=".$row['num']."&sid=".$row['solution_id']."\">Edit</a>";
      }else{
        $view_status[$i][6].= "<a href=\"submitpage.php?id=".$row['problem_id']."&sid=".$row['solution_id']."\">Edit</a>";
      }
    }
    $view_status[$i][7]= $row['code_length']." B";

  }else
      {
        $view_status[$i][4]="----";
        $view_status[$i][5]="----";
        $view_status[$i][6]="----";
        $view_status[$i][7]="----";
      }
  $view_status[$i][8]= $row['in_date'];




}
if(!$OJ_MEMCACHE)mysql_free_result($result);

/////////////////////////Template
require("template/".$OJ_TEMPLATE."/status.php");

/////////////////////////Common foot
if(file_exists('./include/cache_end.php'))
  require_once('./include/cache_end.php');
?>

