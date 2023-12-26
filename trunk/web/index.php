<?php
////////////////////////////Common head
$cache_time = 30;
$OJ_CACHE_SHARE = false;
require_once( './include/cache_start.php' );
require_once( './include/db_info.inc.php' );
require_once( './include/memcache.php' );
require_once( './include/setlang.php' );
require_once( './include/bbcode.php' );
$view_title = "Welcome To Online Judge";
$result = false;
if ( isset( $OJ_ON_SITE_CONTEST_ID ) ) {
	header( "location:contest.php?cid=" . $OJ_ON_SITE_CONTEST_ID );
	exit();
}
///////////////////////////MAIN	
//NOIP赛制比赛时，暂时屏蔽本月之星
if(isset($OJ_NOIP_KEYWORD)&&$OJ_NOIP_KEYWORD){
		                     $now =  date('Y-m-d H:i', time());
        	                 $sql="select count(contest_id) from contest where start_time<'$now' and end_time>'$now' and title like '%$OJ_NOIP_KEYWORD%'";
		                     $row=pdo_query($sql);
		                     $NOIP_flag=$row[0];	
}
$view_news = "";
$sql = "select * "
. "FROM `news` "
. "WHERE `defunct`!='Y' AND `title`!='faqs.$OJ_LANG'"
. "ORDER BY `importance` ASC,`time` DESC "
. "LIMIT 50";
$view_news .= "<div class='panel panel-default' style='width:80%;margin:0 auto;'>";
	$view_news .= "<div class='panel-heading'><h3>" . $MSG_NEWS . "<h3></div>";
	$view_news .= "<div class='panel-body'>";
	
$result = mysql_query_cache( $sql ); //mysql_escape_string($sql));
if ( !$result ) {
	$view_news.= "";
} else {
	foreach ( $result as $row ) {
		$view_news .= "<div class='panel panel-default'>";
		$view_news .= "<div class='panel-heading'><big>" . $row[ 'title' ] . "</big>-<small>" . $row[ 'user_id' ] . "</small></div>";
		$view_news .= "<div class='panel-body'>" . bbcode_to_html($row[ 'content' ]) . "</div>";
		$view_news .= "</div>";
	}
	
}
$view_news .= "</div>";
$view_news .= "<div class='panel-footer'></div>";
$view_news .= "</div>";

$view_apc_info = "";
$last_1000_id=0;
$last_1000_id=pdo_query("select max(solution_id)-1000 from solution");
if(is_array($last_1000_id))  $last_1000_id=$last_1000_id[0][0];

$sql = "SELECT UNIX_TIMESTAMP(date(in_date))*1000 md,count(1) c FROM (select * from solution where solution_id > $last_1000_id) solution  where result<13 and solution_id > $last_1000_id group by md order by md desc limit 200";
$result = mysql_query_cache( $sql ); //mysql_escape_string($sql));
$chart_data_all = array();
//echo $sql;
if(is_array($result))
foreach ( $result as $row ) {
        array_push( $chart_data_all, array( $row[ 'md' ], $row[ 'c' ] ) );
}

$sql = "SELECT UNIX_TIMESTAMP(date(in_date))*1000 md,count(1) c FROM  (select * from solution where solution_id > $last_1000_id) solution where result=4 and solution_id > $last_1000_id group by md order by md desc limit 200";
$result = mysql_query_cache( $sql ); //mysql_escape_string($sql));
$chart_data_ac = array();
//echo $sql;
if(is_array($result))
foreach ( $result as $row ) {
        array_push( $chart_data_ac, array( $row[ 'md' ], $row[ 'c' ] ) );
}
$speed=0;
if ( isset( $_SESSION[ $OJ_NAME . '_' . 'administrator' ] ) ) {
        $sql = "select avg(sp) sp from (select  avg(1) sp,judgetime DIV 3600 from solution where result>3 and solution_id >$last_1000_id  group by (judgetime DIV 3600) order by sp) tt;";
        $result = mysql_query_cache( $sql );
        $speed = ( $result[ 0 ][ 0 ] ? $result[ 0 ][ 0 ] : 0 ) . '/min';
} else {
        if(isset($chart_data_all[ 0 ])) $speed = ( isset($chart_data_all[ 0 ][ 1 ]) ? $chart_data_all[ 0 ][ 1 ] : 0 ) . '/day';
}
/////////////////////////Template
require( "template/" . $OJ_TEMPLATE . "/index.php" );
if( isset($OJ_LONG_LOGIN)
    && $OJ_LONG_LOGIN 
    &&(!isset($_SESSION[$OJ_NAME.'_user_id']))
    &&isset($_COOKIE[$OJ_NAME."_user"])
    &&isset($_COOKIE[$OJ_NAME."_check"])){
          echo"<script>let xhr=new XMLHttpRequest();xhr.open('GET','login.php',true);xhr.send();setTimeout('location.reload()',800);</script>";
}

/////////////////////////Common foot
if ( file_exists( './include/cache_end.php' ) )
	require_once( './include/cache_end.php' );
?>
