<?php
////////////////////////////Common head
$cache_time = 10;
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = "Welcome To Online Judge";

/////////////////////////Template
/**
 * 获取FAQ内容并显示
 * 首先尝试从数据库中获取特定语言的FAQ新闻内容
 * 如果数据库中没有对应内容，则按优先级加载模板文件
 * 加载顺序：faqs.news.php -> faqs.[语言].php -> faqs.php
 */

// 验证模板和语言参数，防止路径遍历
function validateTemplateName($template)
{
    return preg_match('/^[a-zA-Z0-9_-]+$/', $template) ? $template : 'default';
}

function validateLanguage($lang)
{
    $allowed_langs = ['cn', 'cnt', 'en', 'ko', 'fa', 'ru', 'th', 'ug']; // 根据实际支持语言调整
    return in_array($lang, $allowed_langs) ? $lang : 'en';
}

$validated_template = validateTemplateName($OJ_TEMPLATE);
$validated_lang = validateLanguage($OJ_LANG);

$faqs_name = "faqs." . $validated_lang;
$sql = "select title,content from news where title=? and defunct='N' order by news_id limit 1";
$result = pdo_query($sql, $faqs_name);

if (count($result) > 0 && isset($result[0]['content']) &&
    file_exists("template/" . $validated_template . "/faqs.news.php")) {
    $view_faqs = $result[0]['content'];
    require("template/" . $validated_template . "/faqs.news.php");
} else if (file_exists("template/" . $validated_template . "/faqs." . $validated_lang . ".php")) {
    require("template/" . $validated_template . "/faqs." . $validated_lang . ".php");
} else {
    require("template/" . $validated_template . "/faqs.php");
}
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');

