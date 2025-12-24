<?php
/**
 * 排名列表页面
 * 显示用户排名信息，包括解题数、提交数、通过率等统计信息
 * 支持按不同时间范围（日/周/月/年）和用户组进行筛选
 */

/**
 * 全局配置变量
 */
$OJ_CACHE_SHARE = false;  // 是否共享缓存
$cache_time = 30;         // 缓存时间设置

/**
 * 引入必要的包含文件
 */
require_once('./include/cache_start.php');   // 缓存开始配置
require_once('./include/db_info.inc.php');   // 数据库连接信息
require_once("./include/my_func.inc.php");   // 自定义函数库
require_once('./include/setlang.php');       // 语言设置
require_once('./include/memcache.php');      // 内存缓存配置

/**
 * 检查当前是否有NOIP相关竞赛正在进行
 * 如果有NOIP关键词匹配或特定类型竞赛，显示警告信息并退出
 */
$now = date('Y-m-d H:i', time());
$sql = "select count(contest_id) from contest where start_time<'$now' and end_time>'$now' and ( title like '%$OJ_NOIP_KEYWORD%' or (contest_type & 20)>0 )  ";
$rows = pdo_query($sql);
$row = $rows[0];
if ($row[0] > 0) {
    $view_errors = "<h2> $MSG_NOIP_WARNING </h2>";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

/**
 * 设置页面标题和隐藏用户配置
 */
$view_title = $MSG_RANKLIST;
if (!isset($OJ_RANK_HIDDEN)) $OJ_RANK_HIDDEN = "'admin','zhblue'";

/**
 * 处理时间范围参数
 * 支持日(d)、周(w)、月(m)、年(y)四种时间范围筛选
 */
$scope = "";
if (isset($_GET['scope']))
    $scope = $_GET['scope'];
if ($scope != "" && $scope != 'd' && $scope != 'w' && $scope != 'm')
    $scope = 'y';
$where = "";
$param = array();

/**
 * 构建用户查询条件
 * 支持按用户名前缀和用户组名称进行筛选
 */
if (isset($_GET['prefix'])) {
    $prefix = $_GET['prefix'];
    $where = "where user_id like ? and user_id not in (" . $OJ_RANK_HIDDEN . ") and defunct='N' ";
    array_push($param, $prefix . "%");
} else {
    $where = "where user_id not in (" . $OJ_RANK_HIDDEN . ") and defunct='N' ";
}
if (isset($_GET['group_name']) && !empty($_GET['group_name'])) {
    $group_name = $_GET['group_name'];
    $where .= "and group_name like ? ";
    array_push($param, $group_name . '%');
}

/**
 * 获取总用户数量
 */
$rank = 0;
$sql = "SELECT count(1) as `mycount` FROM `users` where defunct='N' ";
$result = mysql_query_cache($sql);
$row = $result[0];
$view_total = $row['mycount'];

/**
 * 处理分页参数
 */
if (isset($_GET ['start']))
    $rank = intval($_GET ['start']);

if (isset($OJ_LANG)) {
    require_once("./lang/$OJ_LANG.php");
}
$page_size = 50;
//$rank = intval ( $_GET ['start'] );
if ($rank < 0)
    $rank = 0;

/**
 * 根据时间范围参数构建不同的查询SQL
 * 如果设置了时间范围，则查询指定时间范围内的用户统计数据
 */
$sql = "SELECT `user_id`,`nick`,`solved`,`submit`,group_name,starred FROM `users` $where ORDER BY `solved` DESC,submit,reg_time  LIMIT  " . strval($rank) . ",$page_size";

if ($scope) {
    $s = "";
    switch ($scope) {
        case 'd':
            $s = date('Y') . '-' . date('m') . '-' . date('d');
            break;
        case 'w':
            $monday = mktime(0, 0, 0, date("m"), date("d") - (date("w") + 6) % 7, date("Y"));
            $s = date('Y-m-d', $monday);
            break;
        case 'm':
            $s = date('Y') . '-' . date('m') . '-01';;
            break;
        default :
            $s = date('Y') . '-01-01';
    }
    $last_id = mysql_query_cache("select solution_id from solution where  in_date<str_to_date('$s','%Y-%m-%d') order by solution_id desc limit 1;");
    if (!empty($last_id) && is_array($last_id)) $last_id = $last_id[0][0]; else $last_id = 0;
    $view_total = mysql_query_cache("select count(distinct(user_id)) from solution where solution_id>$last_id")[0][0];
    $sql = "SELECT users.`user_id`,`nick`,s.`solved`,t.`submit`,group_name,starred FROM `users`
                                        inner join
                                        (select count(distinct (problem_id)) solved ,user_id from solution
                                               where solution_id>$last_id and user_id not in (" . $OJ_RANK_HIDDEN . ") and problem_id>0 and result=4 and first_time=1 
					       group by user_id order by solved desc limit " . strval($rank) . ",$page_size) s
                                        on users.user_id=s.user_id
                                        inner join
                                        (select count( problem_id) submit ,user_id from solution
                                                where solution_id > $last_id
                                                group by user_id order by submit desc ) t
                                        on users.user_id=t.user_id
                                        and users.user_id not in (" . $OJ_RANK_HIDDEN . ") and defunct='N'
                                ORDER BY s.`solved` DESC,t.submit,reg_time  LIMIT  0,50
                         ";
//                      echo $sql;
}

/**
 * 执行查询并获取结果
 */
if (!empty($param)) {
    $result = pdo_query($sql, $param);
} else {
    $result = mysql_query_cache($sql);
}
if ($result) $rows_cnt = count($result);
else $rows_cnt = 0;

/**
 * 构建排名数据数组
 * 包括排名、用户名、昵称、用户组、解题数、提交数、通过率等信息
 */
$view_rank = array();
$i = 0;
for ($i = 0; $i < $rows_cnt; $i++) {

    $row = $result[$i];

    $rank++;

    $view_rank[$i][0] = $rank;
    $view_rank[$i][1] = "<a href='userinfo.php?user=" . htmlentities($row['user_id'], ENT_QUOTES, "UTF-8") . "'>" . $row['user_id'] . "</a>";
    if (isset($row['starred']) && $row['starred'] > 0) $view_rank[$i][1] = "⭐" . $view_rank[$i][1] . "<span title='用同名账户给hustoj项目加星，可以点亮此星' >⭐</span>";     //github starred rewarding
    $view_rank[$i][2] = "<div class=center>" . htmlentities($row['nick'], ENT_QUOTES, "UTF-8") . "</div>";
    $view_rank[$i][3] = "<div class=center>" . htmlentities($row['group_name'], ENT_QUOTES, "UTF-8") . "</div>";
    $view_rank[$i][4] = "<div class=center><a href='status.php?user_id=" . htmlentities($row['user_id'], ENT_QUOTES, "UTF-8") . "&jresult=4'>" . $row['solved'] . "</a>" . "</div>";
    $view_rank[$i][5] = "<div class=center><a href='status.php?user_id=" . htmlentities($row['user_id'], ENT_QUOTES, "UTF-8") . "'>" . $row['submit'] . "</a>" . "</div>";

    if ($row['submit'] == 0)
        $view_rank[$i][6] = "0.00%";
    else
        $view_rank[$i][6] = sprintf("%.02lf%%", 100 * $row['solved'] / $row['submit']);

//                      $i++;
}


/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/ranklist.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');


