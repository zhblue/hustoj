<?php
$cache_time = 30;
$OJ_CACHE_SHARE = false;
require_once( './include/cache_start.php' );
require_once( './include/db_info.inc.php' );
require_once( './include/memcache.php' );
require_once( './include/setlang.php' );
require_once( './include/bbcode.php' );
$view_title = "Hello skeleton";
/////////////////////////Template


$view_content="这是一个骨架页，如果您想二次开发一个自己的页面，可以分别在web目录和template/syzoj目录，复制两个skeleton.php到新的文件名，`cp skeleton.php myfile.php` ，然后开始修改自己的myfile.php";
require( "template/" . $OJ_TEMPLATE . "/".basename(__FILE__));
/////////////////////////Common foot
if ( file_exists( './include/cache_end.php' ) )
	require_once( './include/cache_end.php' );
