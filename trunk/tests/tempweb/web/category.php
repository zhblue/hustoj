<?php
////////////////////////////Common head
$cache_time = 10;
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/const.inc.php');
require_once('./include/curl.php');
require_once('./include/memcache.php');
require_once('./include/setlang.php');
$view_title = "Welcome To Online Judge";
$result = false;
///////////////////////////MAIN	

/**
 * 获取题目分类信息并生成分类标签显示
 * 从problem表中获取所有有效的题目来源(source)，处理后生成带颜色标签的分类显示
 */
$view_category = "";
$sql = "select distinct source "
    . "FROM `problem` where defunct='N' order by source "
    . "LIMIT 5000";
$result = mysql_query_cache($sql);//mysql_escape_string($sql));
$category = array();

/**
 * 遍历查询结果，将每个题目的source字段按空格分割，并处理URL类型的分类
 */
foreach ($result as $row) {
    $cate = explode(" ", $row['source']);
    foreach ($cate as $cat) {
        $cat = trim($cat);
        if (mb_ereg("^http", $cat)) {
            $cat = get_domain($cat);
        }
        array_push($category, trim($cat));
    }

}
$category = array_unique($category);
sort($category);

/**
 * 根据查询结果生成分类标签HTML内容
 * 如果没有分类数据则显示提示信息，否则生成带颜色主题的分类标签链接
 */
if (!$result) {
    $view_category = "<h3>No Category Now!</h3>";
} else {
    $view_category .= "<div style='word-wrap:break-word;'>";
    foreach ($category as $cat) {
        if (trim($cat) == "") continue;
        $hash_num = hexdec(substr(md5($cat), 0, 7));
        $label_theme = $color_theme[$hash_num % count($color_theme)];
        if ($label_theme == "") $label_theme = "default";
        $view_category .= "<a class='label label-$label_theme' style='display: inline-block; margin:5px ' href='problemset.php?search=" . htmlentities(urlencode($cat), ENT_QUOTES, 'utf-8') . "'>" . htmlentities($cat, ENT_QUOTES, 'utf-8') . "</a>&nbsp;";

    }

    $view_category .= "</div>";
}

/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/category.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');

