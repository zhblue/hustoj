<?php
 $cache_time=10; 
 $OJ_CACHE_SHARE=false;
	require_once('include/cache_start.php');
        require_once('include/db_info.inc.php');
	require_once('include/setlang.php');
	require_once("include/const.inc.php");
	require_once("include/my_func.inc.php");
	require_once("include/memcache.php");
        require_once("include/iplocation.php");
 // check user
$user=$_GET['user'];
if (!is_valid_user_name($user)){
	echo "No such User!";
	exit(0);
}
$iplocation = new IpLocation();

function extractPlistBlocks($inputString) {
    // 定义正则表达式模式
    $pattern = '/\[plist=[^\]]*\][^[]*?\[\/plist\]/';

    // 使用 preg_match_all 函数查找所有匹配项
    preg_match_all($pattern, $inputString, $matches);

    return $matches[0];
}
function extractPlistData($inputString) {
    // 定义正则表达式模式
    $pattern = '/\[plist=([^]]*)\](.*?)\[\/plist\]/s';

    // 使用 preg_match 函数查找匹配项
    if (preg_match($pattern, $inputString, $matches)) {
        // 返回匹配到的 example1 和 内容1
        return [
            'name' => $matches[2],
            'list' => str_replace("&#44;", ",", trim($matches[1]))
        ];
    }

    return null;
}
$sql="select content from news where content like '%[plist=%' and defunct='N' ";
// 示例输入字符串
$news=array_column(mysql_query_cache($sql),'content');
$news=array_unique($news);
$plista=array();
$bible=array();
foreach($news as $plists){
// 提取 plist 块
	$plistBlocks = extractPlistBlocks($plists);
	foreach($plistBlocks as $plistB){
		$plist=extractPlistData($plistB);
//		print_r($plist);
//		echo "<br>";
		 if(!empty($pid))  array_push($plista,$plist);
	}
// 输出结果
	//$plista=array_merge($plist,$plistBlocks);
}
foreach($plista as $plist){
	$name=$plist["name"];
	$list=explode(",",$plist['list']);
	foreach($list as $pid){
		array_push($bible,$pid);
	}
}
$bible=array_unique($bible);

if(!empty($bible)){
	$bible=mysql_query_cache("select problem_id,title from problem where problem_id in (".implode(",",$bible).")");
  // 提取 id 列作为键
	$keys = array_column($bible, 'problem_id');

	// 提取 name 列作为值
	$values = array_column($bible, 'title');

	// 使用 array_combine 将键和值组合成一个映射
	$bible= array_combine($keys, $values);
  // var_dump($ptitle);

//	print_r($bible);
}

$exceptions=array();
if(isset($OJ_NOIP_KEYWORD)&&$OJ_NOIP_KEYWORD && !isset($_SESSION[$OJ_NAME."_administrator"])){  // && !isset($_SESSION[$OJ_NAME."_administrator"])   管理员不受限
		                     $now =  date('Y-m-d H:i', time());
				     $sql="select contest_id from contest c where  c.start_time<'$now' and c.end_time>'$now' and ( c.title like '%$OJ_NOIP_KEYWORD%' or ((c.contest_type & 20) >0 and end_time>now() ) )";
		                     $row=pdo_query($sql);
				     if(count($row)>0){
				        $exceptions=array_column($row,'contest_id');
				     }
}
if(!empty($exceptions)){
   $not_in_noip= " and contest_id not in (".implode(",",$exceptions).") "; 
}else{
   $not_in_noip="";
}
$view_title=$user ."@".$OJ_NAME;
if(isset($_SESSION[$OJ_NAME.'_'.'administrator']))
    $sql="select * FROM `users` WHERE `user_id`=? ";
else 
    $sql="select * FROM `users` WHERE `user_id`=? and user_id not in ($OJ_RANK_HIDDEN) ";
$result=mysql_query_cache($sql,$user);
$row_cnt=count($result);
if ($row_cnt==0){ 
	$view_errors= "No such User!";
	require("template/".$OJ_TEMPLATE."/error.php");
	exit(0);
}

$row=$result[0];
$school=$row['school'];
$group_name=$row['group_name'];
if(empty($group_name)) $group_name="[".getMappedSpecial($user)."]";
$email=$row['email'];
$nick=$row['nick'];
$starred=$row['starred'];
if(!$starred && starred($user)){
        mysql_query_cache("update users set starred=1 where user_id=?",$user);
        $starred=1;
}


// count solved
$sql="select count(DISTINCT problem_id) as `ac` FROM `solution` WHERE `user_id`=? AND `result`=4 and problem_id>0 $not_in_noip ";
$result=mysql_query_cache($sql,$user) ;
$row=$result[0];
$AC=$row['ac'];

// count submission
$sql="select count(DISTINCT problem_id) as `Submit` FROM `solution` WHERE `user_id`=? and  problem_id>0  $not_in_noip ";
$result=mysql_query_cache($sql,$user) ;
 $row=$result[0];
$Submit=$row['Submit'];

// update solved 
$sql="UPDATE `users` SET `solved`='".strval($AC)."',`submit`='".strval($Submit)."' WHERE `user_id`=?";
$result=mysql_query_cache($sql,$user);
$sql="select count(*) as `Rank` FROM `users` WHERE `solved`>? and defunct='N' and user_id not in (".$OJ_RANK_HIDDEN.") ";
$result=mysql_query_cache($sql,$AC);
$row=$result[0];
$Rank=intval($row[0])+1;

 if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])){
$sql="select user_id,password,ip,`time` FROM `loginlog` WHERE `user_id`=? order by `time` desc LIMIT 0,50";
$view_userinfo=mysql_query_cache($sql,$user) ;
}
$sql="select result,count(1) FROM solution WHERE `user_id`=? AND result>=4 $not_in_noip group by result order by result";
	$result=mysql_query_cache($sql,$user);
	$view_userstat=array();
	$i=0;
	 foreach($result as $row){
		$view_userstat[$i++]=$row;
	}
	

$sql=	"select UNIX_TIMESTAMP(date(in_date))*1000 md,count(1) c FROM `solution` where  `user_id`=? $not_in_noip group by md order by md desc ";
	$result=mysql_query_cache($sql,$user);//mysql_escape_string($sql));
	$chart_data_all= array();
//echo $sql;
    
	 foreach($result as $row){
		$chart_data_all[$row['md']]=$row['c'];
    }
    
$sql=	"select UNIX_TIMESTAMP(date(in_date))*1000 md,count(1) c FROM `solution` where  `user_id`=? and result=4 $not_in_noip group by md order by md desc ";
	$result=mysql_query_cache($sql,$user);//mysql_escape_string($sql));
	$chart_data_ac= array();
//echo $sql;
    
	 foreach($result as $row){
		$chart_data_ac[$row['md']]=$row['c'];
    }
  
$acc_arr=Array();
if (isset($_SESSION[$OJ_NAME.'_'.'user_id'])) {
        $sql = "select distinct `problem_id` FROM `solution` WHERE `user_id`=? AND `result`=4 $not_in_noip";
        if(isset($pids)&&$pids!="") $sql.=" and problem_id in ($pids)";
        $result = mysql_query_cache($sql,$_SESSION[$OJ_NAME.'_'.'user_id']);
        foreach ($result as $row)
                $acc_arr[$row[0]] = true;
}

    
/////////////////////////Template
require("template/".$OJ_TEMPLATE."/userinfo.php");
/////////////////////////Common foot
if(file_exists('./include/cache_end.php'))
	require_once('./include/cache_end.php');
?>

