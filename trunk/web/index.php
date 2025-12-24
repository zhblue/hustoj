<?php
////////////////////////////Common head
$cache_time = 30;
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/memcache.php');
require_once('./include/setlang.php');
require_once('./include/bbcode.php');
$view_title = "Welcome To Online Judge";
$result = false;
if (isset($OJ_ON_SITE_CONTEST_ID)) {
    header("location:contest.php?cid=" . (int)$OJ_ON_SITE_CONTEST_ID);
    exit();
}
///////////////////////////MAIN	

// NOIP赛制比赛时，本月之星，统计图不计入相关比赛提交
$now = date('Y-m-d H:i', time());
$noip_contests = "";
$OJ_NOIP_KEYWORD = isset($OJ_NOIP_KEYWORD) ? mysql_real_escape_string($OJ_NOIP_KEYWORD) : '';
$sql = "select contest_id from contest where start_time<'$now' and end_time>'$now' and ( title like '%$OJ_NOIP_KEYWORD%' or (contest_type & 20)>0 )  ";
$rows = pdo_query($sql);
$NOIP_flag = 0;
//  echo "->".$sql;
if (!empty($rows)) {
    foreach ($rows as $row) {
        $noip_contests .= (int)$row['contest_id'] . ",";
        $NOIP_flag++;
    }
}
$not_in_noip_contests = "";
if (!empty($noip_contests)) {
    $noip_contests = rtrim($noip_contests, ",");
    $not_in_noip_contests = " and contest_id not in ( $noip_contests )";
}

// 查询并构建新闻列表
$view_news = "";
$sql = "select * "
    . "FROM `news` "
    . "WHERE `defunct`!='Y' AND `title`!='faqs." . (isset($OJ_LANG) ? mysql_real_escape_string($OJ_LANG) : '') . "'"
    . "ORDER BY `importance` ASC,`time` DESC "
    . "LIMIT 50";
$view_news .= "<div class='panel panel-default' style='width:80%;margin:0 auto;'>";
$view_news .= "<div class='panel-heading'><h3>" . (isset($MSG_NEWS) ? $MSG_NEWS : '') . "<h3></div>";
$view_news .= "<div class='panel-body'>";

$result = mysql_query_cache($sql); //mysql_escape_string($sql));
if (!$result) {
    $view_news .= "";
} else {
    foreach ($result as $row) {
        $view_news .= "<div class='panel panel-default'>";
        $view_news .= "<div class='panel-heading'><big>" . htmlentities($row['title'], ENT_QUOTES, 'UTF-8') . "</big>-<small>" . htmlspecialchars($row['user_id'], ENT_QUOTES, 'UTF-8') . "</small></div>";
        $view_news .= "<div class='panel-body'>" . bbcode_to_html($row['content']) . "</div>";
        $view_news .= "</div>";
    }

}
$view_news .= "</div>";
$view_news .= "<div class='panel-footer'></div>";
$view_news .= "</div>";

$view_apc_info = "";
$last_1000_id = 0;
$last_1000_id_result = mysql_query_cache("select min(solution_id) id from solution where in_date >= NOW() - INTERVAL 8 DAY union select max(solution_id)-1000 id from solution order by id desc limit 1");
if (!empty($last_1000_id_result)) $last_1000_id = $last_1000_id_result[0][0];
if ($last_1000_id == NULL) $last_1000_id = 0;

// 获取所有提交数据用于图表显示
$sql = "SELECT date(in_date) md,count(1) c FROM (select * from solution where solution_id > " . (int)$last_1000_id . "  $not_in_noip_contests and result<13 and problem_id>0 and  result>=4 ) solution group by md order by md desc limit 1000";
$result = mysql_query_cache($sql); //mysql_escape_string($sql));
$chart_data_all = array();
//echo $sql;
if (!empty($result))
    foreach ($result as $row) {
        array_push($chart_data_all, array($row['md'], $row['c']));
    }

// 获取AC提交数据用于图表显示
$sql = "SELECT date(in_date) md,count(1) c FROM  (select * from solution where solution_id > " . (int)$last_1000_id . " $not_in_noip_contests and result=4 and problem_id>0) solution group by md order by md desc limit 1000";
$result2 = mysql_query_cache($sql); //mysql_escape_string($sql));
$ac = array();
foreach ($result2 as $row) {
    $ac[$row['md']] = $row['c'];
}
$chart_data_ac = array();
//echo $sql;
if (!empty($result))
    foreach ($result as $row) {
        if (isset($ac[$row['md']]))
            array_push($chart_data_ac, array($row['md'], $ac[$row['md']]));
        else
            array_push($chart_data_ac, array($row['md'], 0));
    }

// 计算提交速度
$speed = 0;
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
    $sql = "select avg(sp) sp from (select  avg(1) sp,judgetime DIV 3600 from solution where result>3 and solution_id >" . (int)$last_1000_id . "  group by (judgetime DIV 3600) order by sp) tt;";
    $result = mysql_query_cache($sql);
    $speed = (isset($result[0][0]) && $result[0][0] ? $result[0][0] : 0) . '/min';
} else {
    if (isset($chart_data_all[0])) $speed = (isset($chart_data_all[0][1]) ? $chart_data_all[0][1] : 0) . '/day';
}
/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/index.php");
if (isset($OJ_LONG_LOGIN)
    && $OJ_LONG_LOGIN
    && (!isset($_SESSION[$OJ_NAME . '_user_id']))
    && isset($_COOKIE[$OJ_NAME . "_user"])
    && isset($_COOKIE[$OJ_NAME . "_check"])) {
    echo "<script>let xhr=new XMLHttpRequest();xhr.open('GET','login.php',true);xhr.send();setTimeout('location.reload()',800);</script>";
}

/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
