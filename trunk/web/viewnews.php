<?php
////////////////////////////Common head
$cache_time = 30;
$OJ_CACHE_SHARE = true;
$news_id=intval($_GET["id"]);
require_once( './include/cache_start.php' );
require_once( './include/db_info.inc.php' );
require_once( './include/bbcode.php' );
require_once( './include/memcache.php' );
require_once( './include/setlang.php' );
$view_title = "Welcome To Online Judge";
$result = false;
if ( isset( $OJ_ON_SITE_CONTEST_ID ) ) {
	header( "location:contest.php?cid=" . $OJ_ON_SITE_CONTEST_ID );
	exit();
}
///////////////////////////MAIN	

$view_news = "";
$sql = "select * "
. "FROM `news` "
. "WHERE `defunct`!='Y' && `news_id`='$news_id'"
. "ORDER BY `importance` ASC,`time` DESC "
. "LIMIT 50";
if ($OJ_MENU_NEWS) {
	$sql = "select * "
	. "FROM `news` "
	. "WHERE (`defunct`!='Y' or `menu` = 1) && `news_id`= ? "
	. "ORDER BY `importance` ASC,`time` DESC "
	. "LIMIT 50";
}
$result = mysql_query_cache( $sql,$news_id ); //mysql_escape_string($sql));
if ( !$result ) {
	$new_title = $news_content = "公告不存在!";
} else {
	foreach ( $result as $row ) {
		$news_title=$row['title'];
		$news_content=$row['content'];
		$news_writer=$row['user_id'];
		$news_date=$row['time'];
	}
}

/////////////////////////Template
require( "template/" . $OJ_TEMPLATE . "/viewnews.php" );
/////////////////////////Common foot
if ( file_exists( './include/cache_end.php' ) )
	require_once( './include/cache_end.php' );
?>
