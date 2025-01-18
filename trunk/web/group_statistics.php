<?php
$cache_time = 30;
$OJ_CACHE_SHARE = false;
require_once( './include/cache_start.php' );
require_once( './include/db_info.inc.php' );
require_once( './include/memcache.php' );
require_once( './include/setlang.php' );
require_once( './include/bbcode.php' );
$view_title = "$MSG_PROBLEM$MSG_LIST - $MSG_GROUP_NAME$MSG_STATISTICS";
$result = false;
if ( isset( $OJ_ON_SITE_CONTEST_ID ) ) {
	header( "location:contest.php?cid=" . $OJ_ON_SITE_CONTEST_ID );
	exit();
}
///////////////////////////MAIN	
//NOIP赛制比赛时，移除相关题目
$exceptions=array();
if(isset($OJ_NOIP_KEYWORD)&&$OJ_NOIP_KEYWORD && !isset($_SESSION[$OJ_NAME."_administrator"])){  // 管理员不受限
                                     $now =  date('Y-m-d H:i', time());
                                     $sql="select contest_id from contest c where  c.start_time<'$now' and c.end_time>'$now' and (c.title like '%$OJ_NOIP_KEYWORD%' or (c.contest_type & 20 )> 0 )";
                                     $row=pdo_query($sql);
                                     if(count($row)>0){
                                        $exceptions=array_column($row,'contest_id');
                                //      var_dump($exceptions);
                                     }
}

if(isset($_GET['list'])){
   if(isset($_GET['group_name']))  $group_name=basename($_GET['group_name']);
   else 						     $group_name=$_SESSION[$OJ_NAME.'_group_name'];
   if(isset($_GET['down'])){
	        header ( "Content-type:   application/excel" );
	        header ( "content-disposition:   attachment;   filename=$MSG_GROUP_NAME.$MSG_STATISTICS"."_".$group_name.".xls" );
   }
  $limit=10;
  if(isset($_SESSION[$OJ_NAME.'_contest_creator'])) $limit+=70;
  if(isset($_SESSION[$OJ_NAME.'_administrator'])) $limit+=100;
  if(!empty($group_name)){
        $users=pdo_query("select user_id from users where group_name=? and defunct='N'  order by solved desc  limit $limit ",$group_name);  // 预防出现DoS攻击
        $user_ida = array_column($users,0);
  }else{
  	$user_ida=['admin'];
  }
  $user_ids="";
  if(!empty($user_ida) && strlen($user_ida[0])>0){
    $len=count($user_ida);	  
    for($i=0; $i<$len; $i++){
      if($user_ids) $user_ids.=",";
      $user_ids.="?";
      $user_ida[$i]=trim($user_ida[$i]);
    }
  }
  //echo implode(",",$user_ida),"<br>";
  
	  $sql="select user_id,nick ";

	  $pida=array_unique(explode(',',$_GET['list']));
	  $len=count($pida);
	  for($i=0;$i<$len;$i++){
		$pida[$i]=intval($pida[$i]);
	  }

  $pida=array_unique($pida);
  $pida=array_diff($pida,$exceptions);
  $pids=implode(",",$pida);

  foreach($pida as $pid){
  	$sql.=" ,min(case problem_id when $pid then result else 15 end) P$pid";
  }
  //$user_ids=implode("','",$user_ida);
  if(empty($exceptions))   $sql.=" from solution where user_id in ($user_ids) group by user_id,nick ";
  else                                  $sql.=" from solution where user_id in ($user_ids) and contest_id not in (".implode(",",$exceptions).") group by user_id,nick ";
 $result = pdo_query($sql,$user_ida);

//  echo $sql;
  $ptitle=pdo_query("select problem_id,title from problem where problem_id in (".$pids.")");
  // 提取 id 列作为键
	$keys = array_column($ptitle, 'problem_id');

	// 提取 name 列作为值
	$values = array_column($ptitle, 'title');

	// 使用 array_combine 将键和值组合成一个映射
	$ptitle= array_combine($keys, $values);
  // var_dump($ptitle);

}
/////////////////////////Template
require( "template/" . $OJ_TEMPLATE . "/".basename(__FILE__));
/////////////////////////Common foot
if ( file_exists( './include/cache_end.php' ) )
	require_once( './include/cache_end.php' );
