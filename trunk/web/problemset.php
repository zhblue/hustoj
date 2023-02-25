<?php 
$OJ_CACHE_SHARE = false;
$cache_time = 0;

require_once('./include/db_info.inc.php');
require_once('./include/const.inc.php');
//require_once('./include/cache_start.php');
require_once('./include/memcache.php');
require_once('./include/setlang.php');

$view_title = "Problem Set";
// 获取分类摘要
if (isset($_GET['search']) && trim($_GET['search'])!="") {
	$result = pdo_query($sql,$search,$search,$search);
		
	$sql = "SELECT summary FROM `problem_source` WHERE `source`=?";
	$_result = pdo_query($sql, $_GET['search']);
	$summary = isset($_result[0]['summary']) ? $_result[0]['summary'] : '';
}
else {
	$result = mysql_query_cache($sql);
}


//remember page
$page = "1";
if (isset($_GET['page'])) {
	$page = intval($_GET['page']);

	if (isset($_SESSION[$OJ_NAME.'_'.'user_id'])) {
		$sql = "update users set volume=? where user_id=?";
		pdo_query($sql,$page,$_SESSION[$OJ_NAME.'_'.'user_id']);
	}

}
else {
	if (isset($_SESSION[$OJ_NAME.'_'.'user_id'])) {
		$sql = "select volume from users where user_id=?";
		$result = pdo_query($sql,$_SESSION[$OJ_NAME.'_'.'user_id']);
		$row = $result[0];
		$page = intval($row[0]);
	}else{
		$page = 1;
	}

	if (!is_numeric($page) || $page<=0)
		$page = '1';
}
//end of remember page

//Page Setting
$sql = "select count(`problem_id`) as upid FROM `problem` WHERE `defunct`='N'";
$page_cnt = 50;  //50 problems per page
$result = mysql_query_cache($sql);
$row = $result[0];
$cnt = $row['upid'] / $page_cnt;
if ($row['upid'] % $page_cnt == 0) $cnt = $cnt-1;
if ($page > $cnt+1 && ! isset($_SESSION[$OJ_NAME.'_administrator']) ) $page = $cnt+1 ;
$pstart = $page_cnt*intval($page)-$page_cnt+1; //start 1
$pend = $pstart+$page_cnt;

if (isset($_GET['search']) && trim($_GET['search'])!="") {
	$search = "%".($_GET['search'])."%";
	$filter_sql = " ( title like ? or source like ?)";
	$pstart = 0;
	$pend = 100;

}else if (isset($_GET['list']) && trim($_GET['list']!="")){
        $plist= explode(",",$_GET['list']);
	$pids="0";
	foreach($plist as $pid){
	  $pid=intval($pid);
     	  $pids.=",$pid";
	}
	$filter_sql = " problem_id in ($pids) order by FIELD(problem_id,$pids)"; // 如果希望按难度顺序改成 order by accepted desc ;
	$pstart = 0;
	$pend = 100;
	//echo $filter_sql ;

}else {
	//$filter_sql = " `problem_id`>='".strval($pstart)."' AND `problem_id`<'".strval($pend)."' ";
	$filter_sql = "A.ROWNUM >='" . strval($pstart) . "' AND A.ROWNUM < '". strval($pend) . "' ";
}

//all submit
$sub_arr = Array();
if (isset($_SESSION[$OJ_NAME.'_'.'user_id'])){
        $sql = "SELECT distinct `problem_id` FROM `solution` WHERE `user_id`=? ";
        if(isset($pids)&&$pids!="") $sql.=" and problem_id in ($pids)";
        $result = pdo_query($sql,$_SESSION[$OJ_NAME.'_'.'user_id']);
        foreach ($result as $row)
                $sub_arr[$row[0]] = true;
}

//all ac
$acc_arr=Array();
if (isset($_SESSION[$OJ_NAME.'_'.'user_id'])) {
        $sql = "SELECT distinct `problem_id` FROM `solution` WHERE `user_id`=? AND `result`=4 ";
        if(isset($pids)&&$pids!="") $sql.=" and problem_id in ($pids)";
        $result = pdo_query($sql,$_SESSION[$OJ_NAME.'_'.'user_id']);
        foreach ($result as $row)
                $acc_arr[$row[0]] = true;
}

// Problem Page Navigator
//if($OJ_SAE) $first=1;
if ($OJ_FREE_PRACTICE){  // open free practice without limit of contest using
	$sql = "SELECT count(problem_id) as upid FROM `problem` WHERE defunct='N' ";
        $result = mysql_query_cache($sql);
        $row = $result[0];
        $cnt = $row['upid'] / $page_cnt;
        if ($row['upid'] % $page_cnt == 0) $cnt = $cnt-1;
        $sql = "SELECT * FROM (SELECT @ROWNUM := @ROWNUM + 1 AS ROWNUM, `problem_id`,`title`,`source`,`submit`,`accepted`,defunct FROM (select * from `problem` WHERE `defunct`='N'  order by problem_id) problem, (SELECT @ROWNUM := 0) TEMP ORDER BY `problem_id`) A WHERE defunct='N' and  $filter_sql";

}else if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])) {  //all problems
	// Reset Page Count
	$sql = "select count(`problem_id`) as upid FROM `problem`";
	$result = mysql_query_cache($sql);
	$row = $result[0];
	$cnt = $row['upid'] / $page_cnt;

	if ($row['upid'] % $page_cnt == 0) $cnt = $cnt-1;

	$sql = "SELECT * FROM (SELECT @ROWNUM := @ROWNUM + 1 AS ROWNUM, `problem_id`,`title`,`source`,`submit`,`accepted`,defunct FROM (select * from `problem` order by problem_id) problem, (SELECT @ROWNUM := 0) TEMP ORDER BY `problem_id`) A WHERE $filter_sql";

}else {  //page problems (not include in contests period)
	$now = strftime("%Y-%m-%d %H:%M",time());
	$sql = "SELECT * FROM (SELECT @ROWNUM := @ROWNUM + 1 AS ROWNUM, `problem_id`,`title`,`source`,`submit`,`accepted`,defunct " .
	"FROM (select * from `problem` WHERE `defunct`='N' order by problem_id) problem, (SELECT @ROWNUM := 0) TEMP " .
	"WHERE `defunct`='N' AND `problem_id` NOT IN (
		SELECT  `problem_id`
		FROM contest c
			INNER JOIN  `contest_problem` cp ON c.`contest_id` = cp.`contest_id` ".
			 " AND (c.`defunct` = 'N' AND '$now'<c.`end_time`)" .    // option style show all non-running contest
			//"and (c.`end_time` >  '$now'  OR c.private =1)" .    // original style , hidden all private contest problems
	") ORDER BY `problem_id` ) A WHERE $filter_sql";
}
// End Page Setting
pdo_query("SET sort_buffer_size = 1024*1024");   // Out of sort memory, consider increasing server sort buffer size
//echo htmlentities( $sql);
if (isset($_GET['search']) && trim($_GET['search'])!="") {
	$result = pdo_query($sql,$search,$search);
}
else {
	$result = mysql_query_cache($sql);
}

$view_total_page = intval($cnt+1);

$cnt = 0;
$view_problemset = Array();
$i = 0;
foreach ($result as $row) {
	$view_problemset[$i] = Array();

	if (isset($sub_arr[$row['problem_id']])) {
		if (isset($acc_arr[$row['problem_id']])) 
			$view_problemset[$i][0] = "<div class='label label-success'>Y</div>";
		else
			$view_problemset[$i][0] = "<div class='label label-danger'>N</div>";
	}
	else {
		$view_problemset[$i][0] = "<div class=none> </div>";
	}

	$category = array();
	$cate = explode(" ",$row['source']);
	foreach ($cate as $cat) {
		array_push($category,trim($cat));	
	}

	$view_problemset[$i][1] = "<div fd='problem_id' class='center'>".$row['problem_id']."</div>";
	$view_problemset[$i][2] = "<div class='left'><a href='problem.php?id=".$row['problem_id']."'>".$row['title']."</a></div>";;
	$view_problemset[$i][3] = "<div pid='".$row['problem_id']."' fd='source' class='center'>";

	foreach ($category as $cat) {
		if(trim($cat)=="")
			continue;

		$hash_num = hexdec(substr(md5($cat),0,7));
		$label_theme = $color_theme[$hash_num%count($color_theme)];

		if ($label_theme=="")
			$label_theme = "default";

		$view_problemset[$i][3] .= "<a title='".htmlentities($cat,ENT_QUOTES,'UTF-8')."' class='label label-$label_theme' style='display: inline-block;' href='problemset.php?search=".htmlentities(urlencode($cat),ENT_QUOTES,'UTF-8')."'>".mb_substr($cat,0,10,'utf8')."</a>&nbsp;";
	}

	$view_problemset[$i][3] .= "</div >";
	$view_problemset[$i][4] = "<div class='center'><a href='status.php?problem_id=".$row['problem_id']."&jresult=4'>".$row['accepted']."</a></div>";
	$view_problemset[$i][5] = "<div class='center'><a href='status.php?problem_id=".$row['problem_id']."'>".$row['submit']."</a></div>";
	$i++;
}
if(isset($_GET['ajax'])){
	require("template/bs3/problemset.php");
}else{
	require("template/".$OJ_TEMPLATE."/problemset.php");
}
if(file_exists('./include/cache_end.php'))
	require_once('./include/cache_end.php');
?>
