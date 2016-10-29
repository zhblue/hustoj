<?php
$cache_time=10;
$OJ_CACHE_SHARE=false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title= "Welcome To Online Judge";
require_once("./include/const.inc.php");

$id=strval(intval($_GET['id']));

if (isset($_GET['page'])) {
  $page=strval(intval($_GET['page']));
} else {
  $page=0;
}

$view_problem=array();

// total submit
$sql="SELECT count(*) FROM solution WHERE problem_id='$id'";
$result=mysql_query($sql) or die(mysql_error());
$row=mysql_fetch_array($result);
$view_problem[0][0]=$MSG_SUBMIT;
$view_problem[0][1]=$row[0];
$total=intval($row[0]);
mysql_free_result($result);

// total users
$sql="SELECT count(DISTINCT user_id) FROM solution WHERE problem_id='$id'";
$result=mysql_query($sql);
$row=mysql_fetch_array($result);

$view_problem[1][0]="$MSG_USER($MSG_SUBMIT)";
$view_problem[1][1]=$row[0];
mysql_free_result($result);

// ac users
$sql="SELECT count(DISTINCT user_id) FROM solution WHERE problem_id='$id' AND result='4'";
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
$acuser=intval($row[0]);

$view_problem[2][0]="$MSG_USER($MSG_SOVLED)";
$view_problem[2][1]=$row[0];
mysql_free_result($result);

//for ($i=4;$i<12;$i++){
$i=3;
$sql="SELECT result,count(1) FROM solution WHERE problem_id='$id' AND result>=4 group by result order by result";
$result=mysql_query($sql);
while($row=mysql_fetch_array($result)){

  $view_problem[$i][0] =$jresult[$row[0]];
  $view_problem[$i][1] ="<a href=status.php?problem_id=$id&jresult=".$row[0]." >".$row[1]."</a>";
  $i++;
}
mysql_free_result($result);

$pagemin=0; $pagemax=intval(($acuser-1)/20);

if ($page<$pagemin) $page=$pagemin;
if ($page>$pagemax) $page=$pagemax;
$start=$page*20;
$sz=20;
if ($start+$sz>$acuser) $sz=$acuser-$start;

// check whether the problem in a contest
$now=strftime("%Y-%m-%d %H:%M",time());
$sql="SELECT 1 FROM `contest_problem` WHERE `problem_id`=$id AND `contest_id` IN (
  SELECT `contest_id` FROM `contest` WHERE `start_time`<'$now' AND `end_time`>'$now')";
$rrs=mysql_query($sql);
$flag=!(mysql_num_rows($rrs)>0);

// check whether the problem is ACed by user
$AC=false;
if (isset($OJ_AUTO_SHARE)&&$OJ_AUTO_SHARE&&isset($_SESSION['user_id'])){
  $sql="SELECT 1 FROM solution where 
    result=4 and problem_id=$id and user_id='".$_SESSION['user_id']."'";
  $rrs=mysql_query($sql);
  $AC=(intval(mysql_num_rows($rrs))>0);
  mysql_free_result($rrs);
}

$sql=" select * from
  (
    SELECT solution_id ,user_id,language,10000000000000000000+time *100000000000 + memory *100000 + code_length  score, in_date
    FROM solution 
    WHERE problem_id =$id 
    AND result =4

    ORDER BY score, in_date
  )b
  right join
  (

    SELECT count(*) att ,user_id, min(10000000000000000000+time *100000000000 + memory *100000 + code_length ) score
    FROM solution
    WHERE problem_id =$id
    AND result =4
    GROUP BY user_id
    ORDER BY score, in_date

  )c
  on b.score=c.score and b.user_id=c.user_id 
  order by c.score,in_date
  LIMIT  $start, $sz";

$result=mysql_query($sql);


$view_solution=array();
$j=0;
for ($i=$start+1;$row=mysql_fetch_object($result);$i++){
  $sscore=strval($row->score);
  $s_time=intval(substr($sscore,1,8));
  $s_memory=intval(substr($sscore,9,6));
  $s_cl=intval(substr($sscore,15,5));

  $view_solution[$j][0]= "$i";
  $view_solution[$j][1]=  "$row->solution_id";
  if (intval($row->att)>1) $view_solution[$j][1].=  "(".$row->att.")";
  $view_solution[$j][2]=  "<a href='userinfo.php?user=".$row->user_id."'>".$row->user_id."</a>";
  if ($flag) $view_solution[$j][3]=  "$s_memory KB";
  else $view_solution[$j][3]=  "------";

  if ($flag) $view_solution[$j][4]=  "$s_time MS";
  else $view_solution[$j][4]=  "------";

  if (!(isset($_SESSION['user_id'])&&!strcasecmp($row->user_id,$_SESSION['user_id']) ||
    isset($_SESSION['source_browser'])||
    (isset($OJ_AUTO_SHARE)&&$OJ_AUTO_SHARE&&$AC))){
      $view_solution[$j][5]= $language_name[$row->language];
    }else{
      $view_solution[$j][5]=  "<a target=_blank href=showsource.php?id=".$row->solution_id.">".$language_name[$row->language]."</a>";
    }
  if ($flag) $view_solution[$j][6]=  "$s_cl B";
  else $view_solution[$j][6]=  "------";
  $view_solution[$j][7]=  "$row->in_date";
  $j++;
}

mysql_free_result($result);
$view_recommand=Array();
if(isset($_SESSION['user_id'])&&isset($_GET['id'])){
  $id=intval($_GET['id']);
  $user_id=mysql_real_escape_string($_SESSION['user_id']);
  $sql="select problem_id,count(1) people from  (
    SELECT * FROM solution ORDER BY solution_id DESC LIMIT 10000 )solution
    where
    problem_id!=$id and result=4
    and user_id in(select distinct user_id from solution where result=4 and problem_id=$id )
    and problem_id not in (select distinct problem_id from solution where user_id='$user_id' )
    group by `problem_id` order by people desc limit 12";

  $result=mysql_query($sql);
  $i=0;
  while($row=mysql_fetch_object($result)){
    $view_recommand[$i][0]=$row->problem_id;
    $i++;
  }
  mysql_free_result($result);
}

/////////////////////////Template
require("template/".$OJ_TEMPLATE."/problemstatus.php");
/////////////////////////Common foot
if(file_exists('./include/cache_end.php'))
  require_once('./include/cache_end.php');
?>
