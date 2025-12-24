<?php
////////////////////////////Common head
$cache_time = 30;
$OJ_CACHE_SHARE = true;
$news_id = intval($_GET["id"]);
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/bbcode.php');
require_once('./include/memcache.php');
require_once('./include/setlang.php');
$view_title = "Welcome To Online Judge";
$result = false;

// 检查是否存在现场比赛ID，如果存在则重定向到比赛页面
if (isset($OJ_ON_SITE_CONTEST_ID)) {
    header("location:contest.php?cid=" . $OJ_ON_SITE_CONTEST_ID);
    exit();
}
///////////////////////////MAIN	

$view_news = "";

// 构建查询新闻的SQL语句，查找未被禁用且ID匹配的新闻
$sql = "select * "
    . "FROM `news` "
    . "WHERE `defunct`!='Y' && `news_id`='$news_id'"
    . "ORDER BY `importance` ASC,`time` DESC "
    . "LIMIT 50";

// 如果启用了菜单新闻功能，则修改SQL查询条件，允许显示菜单新闻
if ($OJ_MENU_NEWS) {
    $sql = "select * "
        . "FROM `news` "
        . "WHERE (`defunct`!='Y' or `menu` = 1) && `news_id`= ? "
        . "ORDER BY `importance` ASC,`time` DESC "
        . "LIMIT 50";
}

// 执行数据库查询，获取新闻数据
$result = mysql_query_cache($sql, $news_id); //mysql_escape_string($sql));
if (!$result) {
    $new_title = $news_content = "公告不存在!";
} else {
    // 遍历查询结果，提取新闻标题、内容、作者和日期信息
    foreach ($result as $row) {
        $news_title = $row['title'];
        $news_content = $row['content'];
        $news_writer = $row['user_id'];
        $news_date = $row['time'];
    }
}

/////////////////////////Template
// 加载新闻查看页面模板
require("template/" . $OJ_TEMPLATE . "/viewnews.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
