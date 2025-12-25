<?php
/**
 * 问题集页面处理脚本
 *
 * 该脚本负责处理在线评测系统的问题集页面，包括分页、搜索、权限控制等功能
 * 显示问题列表，记录用户访问页面，处理问题搜索和筛选
 */

/**
 * 全局配置变量
 */
$OJ_CACHE_SHARE = false;  // 是否共享缓存
$cache_time = 10;         // 缓存时间设置

/**
 * 引入必要的包含文件
 * 包括数据库连接、常量定义、缓存、cURL、Memcache、语言设置等功能模块
 */
require_once('./include/db_info.inc.php');
require_once('./include/const.inc.php');
require_once('./include/cache_start.php');
require_once('./include/curl.php');
require_once('./include/memcache.php');
require_once('./include/setlang.php');
require_once("./include/set_get_key.php");

/**
 * 页面标题设置
 */
$view_title = "Problem Set";

/**
 * 记住用户访问页面功能
 *
 * 根据GET参数或用户会话记录用户的当前页面位置
 * 管理员和普通用户有不同的页面记忆逻辑
 */
$page = "1";
if (isset($_GET['page'])) {
    $page = intval($_GET['page']);
    if (isset($_SESSION[$OJ_NAME . '_' . 'user_id']) && !isset($_GET['search'])) {
        $sql = "update users set volume=? where user_id=?";
        pdo_query($sql, $page, $_SESSION[$OJ_NAME . '_' . 'user_id']);
    }
} else {
    if (isset($_SESSION[$OJ_NAME . '_' . 'user_id']) && !isset($_GET['search'])) {
        $sql = "select volume from users where user_id=?";
        $result = pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id']);
        $row = $result[0];
        $page = intval($row[0]);
    } else {
        $page = 1;
    }

    if (!is_numeric($page) || $page <= 0)
        $page = '1';
}
//end of remember page

/**
 * 分页设置模块
 *
 * 根据不同的请求参数（搜索、列表、普通浏览）设置不同的过滤条件和排序方式
 * 支持按标题和来源搜索、按指定问题列表显示、普通分页浏览
 */
$page_cnt = 50;  //50 problems per page

$postfix = "";
$filter_sql = "";
$limit_sql = "";
$order_by = " order by problem_id ";
if (isset($_GET['search']) && trim($_GET['search']) != "") {
    $search = "%" . ($_GET['search']) . "%";
    $filter_sql = " ( title like ? or source like ?)";
    $limit_sql = " LIMIT " . ($page - 1) * $page_cnt . "," . $page_cnt;
    $postfix = "&search=" . urlencode($_GET['search']);
} else if (isset($_GET['list']) && trim($_GET['list'] != "")) {
    $plist = explode(",", $_GET['list']);
    $pids = "";
    foreach ($plist as $pid) {
        $pid = intval($pid);
        if ($pids == "")
            $pids .= $pid;
        else
            $pids .= ",$pid";
    }
    $filter_sql = " problem_id in ($pids) ";
    $order_by = "order by FIELD(problem_id,$pids)"; // 如果希望按难度顺序改成 order by accepted desc ;
    //$limit_sql = " LIMIT ".($page-1)*$page_cnt.",".$page_cnt;
    $limit_sql = "";  // list 不翻页
} else {
    $filter_sql = " problem_id > 0";
    $limit_sql = " LIMIT " . ($page - 1) * $page_cnt . "," . $page_cnt;
}

/**
 * 用户提交状态统计
 *
 * 获取当前登录用户的提交记录，统计已提交和已通过的问题
 * 用于在问题列表中标记用户对该问题的完成状态
 */
$sub_arr = array();
$acc_arr = array();
if (isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $sql = "select problem_id, MIN(result) AS result FROM solution WHERE user_id=? and result>=4 GROUP BY problem_id ";
    $result = pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id']);
    foreach ($result as $row) {
        $sub_arr[$row['problem_id']] = true;
        if ($row['result'] == 4) $acc_arr[$row['problem_id']] = true;
    }
}

/**
 * 问题页面导航权限控制
 *
 * 根据用户权限（管理员、普通用户、自由练习模式）设置不同的问题过滤条件
 * 管理员可查看所有问题，普通用户不能查看竞赛期间的问题
 */
//if($OJ_SAE) $first=1;
if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {  //all problems
    //$limit = $limit_sql;
} else if ($OJ_FREE_PRACTICE) {  // open free practice without limit of contest using
    $filter_sql .= " and defunct='N' ";
} else {  //page problems (not include in contests period)
    $now = date('Y-m-d H:i', time());
    $filter_sql .= " and `defunct`='N' AND `problem_id` NOT IN (
					SELECT  `problem_id`
						FROM contest c
						INNER JOIN  `contest_problem` cp ON c.`contest_id` = cp.`contest_id` " .
        " AND (c.`defunct` = 'N' AND '$now'<c.`end_time`)" .    // option style show all non-running contest
        //"and (c.`end_time` >  '$now'  OR c.private =1)" . // original style , hidden all private contest problems
        ") ";
}
// End Page Setting

/**
 * 数据库查询执行
 *
 * 执行问题列表查询，根据搜索条件使用不同的查询方式
 * 统计总页数，获取当前页的问题数据
 */
pdo_query("SET sort_buffer_size = 1024*1024");   // Out of sort memory, consider increasing server sort buffer size
$sql = "select `problem_id`,`title`,`source`,`submit`,`accepted`,defunct FROM problem A WHERE $filter_sql $order_by $limit_sql ";
$count_sql = "select count(1) from problem where  $filter_sql ";
//echo htmlentities( $sql);
if (isset($_GET['search']) && trim($_GET['search']) != "") {
    $total = pdo_query($count_sql, $search, $search);
    $cnt = $total[0][0] / $page_cnt;
    $result = pdo_query($sql, $search, $search);
} else {
    $total = mysql_query_cache($count_sql);
    $cnt = $total[0][0] / $page_cnt;
    $result = mysql_query_cache($sql);
}
//echo "$cnt $count_sql";

$view_total_page = ceil($cnt * 1.0);

/**
 * 问题列表数据处理
 *
 * 遍历查询结果，构建问题列表显示数据
 * 处理问题完成状态标记、问题来源分类、链接生成等
 */
$cnt = 0;
$view_problemset = array();
$i = 0;
foreach ($result as $row) {
    $view_problemset[$i] = array();

    if (isset($sub_arr[$row['problem_id']])) {
        if (isset($acc_arr[$row['problem_id']]))
            $view_problemset[$i][0] = "<div class='label label-success'>Y</div>";
        else
            $view_problemset[$i][0] = "<div class='label label-danger'>N</div>";
    } else {
        $view_problemset[$i][0] = "<div class=none> </div>";
    }

    $category = array();
    $cate = explode(" ", $row['source']);
    foreach ($cate as $cat) {
        $cat = trim($cat);
        if (mb_ereg("^http", $cat)) {
            $cat = get_domain($cat);
        }
        array_push($category, trim($cat));
    }
    $view_problemset[$i][1] = "<div fd='problem_id' class='center'>" . $row['problem_id'] . "</div>";
    $view_problemset[$i][2] = "<div class='left'><a href='problem.php?id=" . $row['problem_id'] . "'>" . $row['title'] . "</a></div>";;
    $view_problemset[$i][3] = "<div pid='" . $row['problem_id'] . "' fd='source' class='center'>";

    foreach ($category as $cat) {
        if (trim($cat) == "" || trim($cat) == "&nbsp")
            continue;

        $hash_num = hexdec(substr(md5($cat), 0, 7));
        $label_theme = $color_theme[$hash_num % count($color_theme)];

        if ($label_theme == "")
            $label_theme = "default";

        $view_problemset[$i][3] .= "<a title='" . htmlentities($cat, ENT_QUOTES, 'UTF-8') . "' class='label label-$label_theme' style='display: inline-block;' href='problemset.php?search=" . htmlentities(urlencode($cat), ENT_QUOTES, 'UTF-8') . "'>" . mb_substr($cat, 0, 10, 'utf8') . "</a>&nbsp;";
    }

    $view_problemset[$i][3] .= "</div >";
    $view_problemset[$i][4] = "<div class='center'><a href='status.php?problem_id=" . $row['problem_id'] . "&jresult=4'>" . $row['accepted'] . "</a></div>";
    $view_problemset[$i][5] = "<div class='center'><a href='status.php?problem_id=" . $row['problem_id'] . "'>" . $row['submit'] . "</a></div>";
    $i++;
}

/**
 * 模板渲染
 *
 * 根据请求类型（AJAX或普通请求）选择不同的模板文件
 * 输出最终的问题列表页面
 */
if (isset($_GET['ajax'])) {
    require("template/bs3/problemset.php");
} else {
    require("template/" . $OJ_TEMPLATE . "/problemset.php");
}

/**
 * 缓存结束处理
 *
 * 如果存在缓存结束文件则执行缓存处理
 */
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
