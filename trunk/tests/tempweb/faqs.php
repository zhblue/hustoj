<?php
////////////////////////////Common head
$cache_time = 10;
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = "Welcome To Online Judge";

/////////////////////////Template
$faqs_name = "faqs.$OJ_LANG";
$sql = "select title,content from news where title=? and defunct='N' order by news_id limit 1";
$result = pdo_query($sql, $faqs_name);
if (count($result) > 0 && file_exists("template/" . $OJ_TEMPLATE . "/faqs.news.php")) {
    $view_faqs = $result[0][1];
    require("template/" . $OJ_TEMPLATE . "/faqs.news.php");
} else if (file_exists("template/" . $OJ_TEMPLATE . "/faqs.$OJ_LANG.php")) {
    require("template/" . $OJ_TEMPLATE . "/faqs.$OJ_LANG.php");
} else {
    require("template/" . $OJ_TEMPLATE . "/faqs.php");
}
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
?>

