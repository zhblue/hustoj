<?php
$cache_time = 30;
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/memcache.php');
require_once('./include/setlang.php');
require_once('./include/bbcode.php');
$view_title = "$MSG_PROBLEM$MSG_LIST - $MSG_GROUP_NAME$MSG_STATISTICS";
$result = false;
if (isset($OJ_ON_SITE_CONTEST_ID)) {
    header("location:contest.php?cid=" . $OJ_ON_SITE_CONTEST_ID);
    exit();
}

/**
 * 提取输入字符串中所有的[plist]标签块
 * 
 * @param string $inputString 包含plist标签的输入字符串
 * @return array 匹配到的所有plist标签块数组
 */
function extractPlistBlocks($inputString)
{
    // 定义正则表达式模式
    $pattern = '/\[plist=[^\]]*\][^[]*?\[\/plist\]/';

    // 使用 preg_match_all 函数查找所有匹配项
    preg_match_all($pattern, $inputString, $matches);

    return $matches[0];
}

/**
 * 从plist标签块中提取名称和列表数据
 * 
 * @param string $inputString 包含单个plist标签的字符串
 * @return array|null 包含name和list的数组，如果未匹配到则返回null
 */
function extractPlistData($inputString)
{
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

// 查询包含plist标签的新闻内容
$sql = "select content from news where content like '%[plist=%' and defunct='N' ";
// 示例输入字符串
$news = array_column(pdo_query($sql), 'content');
$news = array_unique($news);
$plista = array();
$bible = array();
foreach ($news as $plists) {
// 提取 plist 块
    $plistBlocks = extractPlistBlocks($plists);
    foreach ($plistBlocks as $plistB) {
        $plist = extractPlistData($plistB);
        if (!empty($plist)) array_push($plista, $plist);
    }
// 输出结果
    //$plista=array_merge($plist,$plistBlocks);
}
foreach ($plista as $plist) {
    $name = $plist["name"];
    $list = explode(",", $plist['list']);
    foreach ($list as $pid) {
        if (!empty($pid)) array_push($bible, $pid);
    }
}
$bible = array_unique($bible);
if (!empty($bible)) {
    $bible = pdo_query("select problem_id,title from problem where problem_id in (" . implode(",", $bible) . ")  and defunct='N' ");
    // 提取 id 列作为键
    $keys = array_column($bible, 'problem_id');
    // 提取 name 列作为值
    $values = array_column($bible, 'title');
    // 使用 array_combine 将键和值组合成一个映射
    $bible = array_combine($keys, $values);
}

///////////////////////////MAIN	
//NOIP赛制比赛时，移除相关题目

// 获取NOIP赛制比赛中的题目异常列表
$exceptions = array();
if (isset($OJ_NOIP_KEYWORD) && $OJ_NOIP_KEYWORD && !isset($_SESSION[$OJ_NAME . "_administrator"])) {  // 管理员不受限
    $now = date('Y-m-d H:i', time());
    $sql = "select contest_id from contest c where  c.start_time<'$now' and c.end_time>'$now' and ( c.title like '%$OJ_NOIP_KEYWORD%' or ( c.contest_type & 20 ) > 0 )";
    $row = pdo_query($sql);
    if (count($row) > 0) {
        $exceptions = array_column($row, 'contest_id');
        //      var_dump($exceptions);
    }
}
if (isset($_GET['group_name'])) $group_name = basename($_GET['group_name']);
else $group_name = $_SESSION[$OJ_NAME . '_group_name'];
if (isset($_GET['down'])) {
    header("Content-type:   application/excel");
    header("content-disposition:   attachment;   filename=$MSG_GROUP_NAME.$MSG_STATISTICS" . "_" . $group_name . ".xls");
}
$limit = 10;
if (isset($_SESSION[$OJ_NAME . '_contest_creator'])) $limit += 70;
if (isset($_SESSION[$OJ_NAME . '_administrator'])) $limit += 100;
$users = pdo_query("select user_id,nick from users where group_name=? and defunct='N'  order by solved desc  limit $limit ", $group_name);
$user_ida = array_column($users, 0);
/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/" . basename(__FILE__));
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
