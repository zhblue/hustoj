<?php
/**
 * 根据POST请求中的keyword参数设置缓存时间
 * 如果存在keyword参数则缓存时间为1秒，否则为10秒
 */
if (isset($_POST['keyword']))
    $cache_time = 1;
else
    $cache_time = 10;

/**
 * 设置缓存共享标志为false
 * 注释掉的代码原意是：如果不是竞赛页面或个人页面则启用缓存共享
 */
$OJ_CACHE_SHARE = false;//!(isset($_GET['cid'])||isset($_GET['my']));

/**
 * 包含系统必需的配置文件
 */
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/memcache.php');
require_once('./include/my_func.inc.php');
require_once('./include/const.inc.php');
require_once('./include/setlang.php');

/**
 * 设置页面标题为竞赛标题
 */
$view_title = $MSG_CONTEST;

/**
 * 获取当前时间戳
 */
$now = time();

/**
 * 处理竞赛详情页面逻辑
 * 当URL参数中包含cid时，显示特定竞赛的问题列表
 */
if (isset($_GET['cid'])) {

    require_once("contest-check.php");

    /**
     * 查询竞赛相关的问题信息
     * 包括问题标题、ID、来源、竞赛中的序号、通过数和提交数
     */
    $sql = "select p.title,p.problem_id,p.source,cp.num as pnum,cp.c_accepted accepted,cp.c_submit submit from problem p inner join contest_problem cp on p.problem_id = cp.problem_id and cp.contest_id=? order by cp.num";
    $result = mysql_query_cache($sql,$cid);
    $view_problemset = array();
    $pids = array_column($result, 'problem_id');
    if (!empty($pids)) $pids = implode(",", $pids);
    $cnt = 0;

    /**
     * 判断是否为NOIP模式或竞赛是否锁定
     * noip: 竞赛期间且标题包含NOIP关键词或竞赛被锁定
     * hide_others: 竞赛被锁定以隐藏其他用户信息
     */
    $noip = (time() < $end_time) && (stripos($view_title, $OJ_NOIP_KEYWORD) !== false || contest_locked($cid, 16));
    $hide_others = contest_locked($cid, 8);

    /**
     * 管理员、竞赛管理员、代码浏览器、竞赛创建者不受NOIP模式限制
     */
    if (isset($_SESSION[$OJ_NAME . '_' . "administrator"]) ||
        isset($_SESSION[$OJ_NAME . '_' . "m$cid"]) ||
        isset($_SESSION[$OJ_NAME . '_' . "source_browser"]) ||
        isset($_SESSION[$OJ_NAME . '_' . "contest_creator"])
    ) $noip = false;
    
    /**
     * 遍历查询结果，构建问题列表显示数据
     * 包括AC状态、问题编号链接、问题标题链接、通过数和提交数
     */
    foreach ($result as $row) {
        $view_problemset[$cnt][0] = "";
        if (isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
            $ac = check_ac($cid, $cnt, $noip);
            $sub = "";
            if ($ac != "") $sub = "?";
            if ($noip)
                $view_problemset[$cnt][0] = "$sub";
            else
                $view_problemset[$cnt][0] = "$ac";

        } else
            $view_problemset[$cnt][0] = "";


        if ($now < $end_time) { //竞赛进行期间
            $view_problemset[$cnt][1] = "<a href='problem.php?cid=$cid&pid=$cnt'>" . $PID[$cnt] . "</a>";
            $view_problemset[$cnt][2] = "<a href='problem.php?cid=$cid&pid=$cnt'>" . $row['title'] . "</a>";
        } else {               //竞赛结束后
            /**
             * 检查该问题是否会在其他未开始的竞赛中使用
             * 如果会使用且当前用户不是竞赛管理员，则隐藏问题标题
             */
            $tpid = intval($row['problem_id']);
            $sql = "SELECT `problem_id` FROM `problem` WHERE `problem_id`=? AND `problem_id` IN (
				SELECT `problem_id` FROM `contest_problem` WHERE `contest_id` IN (
					SELECT `contest_id` FROM `contest` WHERE (`defunct`='N' AND now()<`start_time`)
				)
			)";

            $tresult = mysql_query_cache($sql, $tpid);

            if (intval($tresult) != 0 && !isset($_SESSION[$OJ_NAME . '_' . "m$cid"])) {
                $view_problemset[$cnt][1] = $PID[$cnt]; //竞赛结束后隐藏标题
                $view_problemset[$cnt][2] = '--using in another private contest--';
            } else {
                $view_problemset[$cnt][1] = "<a href='problem.php?id=" . $row['problem_id'] . "'>" . $PID[$cnt] . "</a>";
                if ($contest_ok)
                    $view_problemset[$cnt][2] = "<a href='problem.php?cid=$cid&pid=$cnt'>" . $row['title'] . "</a>";
                else
                    $view_problemset[$cnt][2] = $row['title'];
            }
        }

        /**
         * 根据NOIP模式或隐藏设置决定是否显示通过数和提交数
         * 只有管理员和竞赛管理员可以看到真实数据
         */
        if (($noip || $hide_others) && !(isset($_SESSION[$OJ_NAME . 'm' . $cid]) || isset($_SESSION[$OJ_NAME . '_administrator']))) {
            $view_problemset[$cnt][3] = "<span class=red>?</span>";
            $view_problemset[$cnt][4] = "<span class=red>?</span>";
        } else {
            $view_problemset[$cnt][3] = $row['accepted'];
            $view_problemset[$cnt][4] = $row['submit'];
        }


        $cnt++;
    }
} else {
    /**
     * 处理竞赛列表页面逻辑
     * 当URL参数中不包含cid时，显示竞赛列表
     */
    $page = 1;
    if (isset($_GET['page']))
        $page = intval($_GET['page']);

    $page_cnt = 25;
    $pstart = $page_cnt * $page - $page_cnt;
    $pend = $page_cnt;
    $rows = mysql_query_cache("select count(1) from contest where defunct='N'");

    if ($rows)
        $total = $rows[0][0];

    $view_total_page = intval($total / $page_cnt) + 1;
    $keyword = "";

    if (isset($_POST['keyword'])) {
        // 过滤keyword输入，防止SQL注入
        $keyword = trim($_POST['keyword']);
        $keyword = str_replace(['%', '_'], ['\%', '\_'], $keyword);
        $keyword = "%" . $keyword . "%";
    }

    //echo "$keyword";
    $mycontests = "";
    $wheremy = "";

    /**
     * 获取当前用户参与的竞赛列表
     * 用于显示"我的竞赛"功能
     */
    if (isset($_SESSION[$OJ_NAME . '_user_id'])) {
        $sql = "select distinct contest_id from solution where contest_id>0 and user_id=?";
        $result = pdo_query($sql, $_SESSION[$OJ_NAME . '_user_id']);

        foreach ($result as $row) {
            if (intval($row['contest_id']) > 0)
                $mycontests .= "," . $row['contest_id'];
        }

        $len = mb_strlen($OJ_NAME . '_');
        $user_id = $_SESSION[$OJ_NAME . '_' . 'user_id'];

        if ($user_id) {
            /**
             * 刷新用户权限信息
             * 从数据库中获取用户权限并设置到session中
             */
            $sql = "SELECT * FROM `privilege` WHERE `user_id`=?";
            $result = pdo_query($sql, $user_id);

            foreach ($result as $row) {
                if (isset($row['valuestr'])) {
                    $_SESSION[$OJ_NAME . '_' . $row['rightstr']] = $row['valuestr'];
                } else {
                    $_SESSION[$OJ_NAME . '_' . $row['rightstr']] = true;
                }
            }

            /**
             * VIP用户可以访问所有标记为[VIP]的竞赛
             */
            if (isset($_SESSION[$OJ_NAME . '_vip'])) {  // VIP mark can access all [VIP] marked contest
                $sql = "select contest_id from contest where title like '%[VIP]%'";
                $result = pdo_query($sql);
                foreach ($result as $row) {
                    $_SESSION[$OJ_NAME . '_c' . $row['contest_id']] = true;
                }
            };
        }

        /**
         * 收集用户拥有的竞赛管理权限和竞赛访问权限
         */
        foreach ($_SESSION as $key => $value) {
            // 验证键名格式，防止恶意键名
            if (preg_match('/^' . preg_quote($OJ_NAME . '_', '/') . '[mc]\d+$/', $key)) {
                $contest_id = intval(substr($key, $len + 1));
                if ($contest_id > 0) {
                    $mycontests .= "," . $contest_id;
                }
            }
        }

        //echo "=====>$mycontests<====";

        if (strlen($mycontests) > 0)
            $mycontests = substr($mycontests, 1);
        if (isset($_GET['my']) && $mycontests != "") {
            $wheremy = " AND (contest_id IN (" . $mycontests . ") OR user_id=?)";
            $wheremy_params = [$_SESSION[$OJ_NAME . '_user_id']];
        } else {
            $wheremy_params = [];
        }
    }

    // 重构查询以防止SQL注入
    if ($keyword && $wheremy) {
        $sql = "SELECT * FROM contest WHERE contest.defunct='N' AND contest.title LIKE ? " . $wheremy . " ORDER BY contest_id DESC LIMIT ?, ?";
        $params = [$keyword];
        $params = array_merge($params, $wheremy_params);
        $params[] = intval($pstart);
        $params[] = intval($pend);
        $result = pdo_query($sql, ...$params);
    } else if ($keyword) {
        $sql = "SELECT * FROM contest WHERE contest.defunct='N' AND contest.title LIKE ? ORDER BY contest_id DESC LIMIT ?, ?";
        $result = pdo_query($sql, $keyword, intval($pstart), intval($pend));
    } else if ($wheremy) {
        $sql = "SELECT * FROM contest WHERE contest.defunct='N' " . $wheremy . " ORDER BY contest_id DESC LIMIT ?, ?";
        $params = $wheremy_params;
        $params[] = intval($pstart);
        $params[] = intval($pend);
        $result = pdo_query($sql, ...$params);
    } else {
        $sql = "SELECT * FROM contest WHERE contest.defunct='N' ORDER BY contest_id DESC LIMIT ?, ?";
        $result = pdo_query($sql, intval($pstart), intval($pend));
    }

    $view_contest = array();
    $i = 0;

    /**
     * 遍历竞赛查询结果，构建竞赛列表显示数据
     * 包括竞赛ID、标题链接、状态信息（已结束/待开始/进行中）、公开/私有状态、创建者
     */
    foreach ($result as $row) {
        $view_contest[$i][0] = $row['contest_id'];

        if (trim($row['title']) == "")
            $row['title'] = $MSG_CONTEST . $row['contest_id'];

        $view_contest[$i][1] = "<a href='contest.php?cid=" . $row['contest_id'] . "'>" . $row['title'] . "</a>";
        $start_time = strtotime($row['start_time']);
        $end_time = strtotime($row['end_time']);
        $current_time = time();

        $length = $end_time - $start_time;
        $left = $end_time - $current_time;

        if ($end_time <= $current_time) {
            //竞赛已结束
            $view_contest[$i][2] = "<span class=text-muted>$MSG_Ended</span>" . " " . "<span class=text-muted>" . $row['end_time'] . "</span>";

        } else if ($current_time < $start_time) {
            //竞赛待开始
            $view_contest[$i][2] = "<span class=text-success>$MSG_Start</span>" . " " . $row['start_time'] . "&nbsp;";
            $view_contest[$i][2] .= "<span class=text-success>$MSG_TotalTime</span>" . " " . formatTimeLength($length);
        } else {
            //竞赛进行中
            $view_contest[$i][2] = "<span class=text-danger>$MSG_Running</span>" . " " . $row['start_time'] . "&nbsp;";
            $view_contest[$i][2] .= "<span class=text-danger>$MSG_LeftTime</span>" . " " . formatTimeLength($left) . "</span>";
        }

        $private = intval($row['private']);
        if ($private == 0)
            $view_contest[$i][4] = "<span class=text-primary>$MSG_Public</span>";
        else
            $view_contest[$i][5] = "<span class=text-danger>$MSG_Private</span>";

        $view_contest[$i][6] = $row['user_id'];

        $i++;
    }
}

/**
 * 根据URL参数选择加载对应的模板文件
 * 有cid参数时加载竞赛模板，否则加载竞赛列表模板
 */
if (isset($_GET['cid']))
    require("template/" . $OJ_TEMPLATE . "/contest.php");
else
    require("template/" . $OJ_TEMPLATE . "/contestset.php");

/**
 * 包含缓存结束文件，用于处理页面缓存
 */
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
