<?php
/**
 * 根据POST参数设置缓存时间
 * 如果存在keyword参数则缓存时间设为1秒，否则设为10秒
 */
if (isset($_POST['keyword']))
    $cache_time = 1;
else
    $cache_time = 10;

/**
 * 设置缓存共享标志为false
 * 注释掉的代码原本用于判断是否在比赛或个人页面中禁用缓存
 */
$OJ_CACHE_SHARE = false;//!(isset($_GET['cid'])||isset($_GET['my']));

/**
 * 包含必要的系统文件
 * 包括缓存、数据库、内存缓存、自定义函数、常量定义和语言设置文件
 */
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/memcache.php');
require_once('./include/my_func.inc.php');
require_once('./include/const.inc.php');
require_once('./include/setlang.php');

/**
 * 设置页面标题为比赛标题
 */
$view_title = $MSG_CONTEST;

/**
 * 获取当前时间戳
 */
$now = time();

/**
 * 处理比赛详情页面逻辑
 * 当URL参数中包含cid时，显示特定比赛的问题列表
 */
if (isset($_GET['cid'])) {

    require_once("contest-check.php");

    /**
     * 查询比赛相关的问题信息
     * 使用内连接获取问题标题、ID、来源以及比赛中的提交和通过统计
     */
    $sql = "select p.title,p.problem_id,p.source,cp.num as pnum,cp.c_accepted accepted,cp.c_submit submit from problem p inner join contest_problem cp on p.problem_id = cp.problem_id and cp.contest_id=$cid order by cp.num";
    $result = mysql_query_cache($sql);
    $view_problemset = array();
    $pids = array_column($result, 'problem_id');
    if (!empty($pids)) $pids = implode(",", $pids);
    $cnt = 0;
    
    /**
     * 判断是否为NOIP模式或比赛是否锁定
     * 根据比赛结束时间、标题关键词和锁定状态决定显示模式
     */
    $noip = (time() < $end_time) && (stripos($view_title, $OJ_NOIP_KEYWORD) !== false || contest_locked($cid, 16));
    $hide_others = contest_locked($cid, 8);
    
    /**
     * 管理员、比赛管理员、源码浏览器或比赛创建者不受NOIP模式限制
     */
    if (isset($_SESSION[$OJ_NAME . '_' . "administrator"]) ||
        isset($_SESSION[$OJ_NAME . '_' . "m$cid"]) ||
        isset($_SESSION[$OJ_NAME . '_' . "source_browser"]) ||
        isset($_SESSION[$OJ_NAME . '_' . "contest_creator"])
    ) $noip = false;
    
    /**
     * 遍历查询结果，构建问题列表显示数组
     * 根据比赛状态（进行中/已结束）和用户权限设置显示内容
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


        if ($now < $end_time) { //比赛进行中
            $view_problemset[$cnt][1] = "<a href='problem.php?cid=$cid&pid=$cnt'>" . $PID[$cnt] . "</a>";
            $view_problemset[$cnt][2] = "<a href='problem.php?cid=$cid&pid=$cnt'>" . $row['title'] . "</a>";
        } else {               //比赛已结束
            //检查该问题是否会在其他未开始的比赛中使用
            $tpid = intval($row['problem_id']);
            $sql = "SELECT `problem_id` FROM `problem` WHERE `problem_id`=? AND `problem_id` IN (
				SELECT `problem_id` FROM `contest_problem` WHERE `contest_id` IN (
					SELECT `contest_id` FROM `contest` WHERE (`defunct`='N' AND now()<`start_time`)
				)
			)";

            $tresult = pdo_query($sql, $tpid);

            if (intval($tresult) != 0 && !isset($_SESSION[$OJ_NAME . '_' . "m$cid"])) {
                //如果问题将在其他私有比赛中使用，则对其他教师和学生隐藏
                $view_problemset[$cnt][1] = $PID[$cnt]; //比赛结束后隐藏标题
                $view_problemset[$cnt][2] = '--using in another private contest--';
            } else {
                $view_problemset[$cnt][1] = "<a href='problem.php?id=" . $row['problem_id'] . "'>" . $PID[$cnt] . "</a>";
                if ($contest_ok)
                    $view_problemset[$cnt][2] = "<a href='problem.php?cid=$cid&pid=$cnt'>" . $row['title'] . "</a>";
                else
                    $view_problemset[$cnt][2] = $row['title'];
            }
        }

        //$view_problemset[$cnt][3] = $row['source'];

        /**
         * 根据NOIP模式或隐藏设置决定是否显示通过数和提交数
         * 管理员不受此限制
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
     * 处理比赛列表页面逻辑
     * 当URL参数中不包含cid时，显示比赛列表
     */
    $page = 1;
    if (isset($_GET['page']))
        $page = intval($_GET['page']);

    $page_cnt = 25;
    $pstart = $page_cnt * $page - $page_cnt;
    $pend = $page_cnt;
    $rows = pdo_query("select count(1) from contest where defunct='N'");

    if ($rows)
        $total = $rows[0][0];

    $view_total_page = intval($total / $page_cnt) + 1;
    $keyword = "";

    if (isset($_POST['keyword'])) {
        $keyword = "%" . $_POST['keyword'] . "%";
    }

    //echo "$keyword";
    $mycontests = "";
    $wheremy = "";
    
    /**
     * 构建用户参与的比赛列表
     * 根据用户ID获取其参与过的比赛ID，并处理权限设置
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
            // 已登录的
            $sql = "SELECT * FROM `privilege` WHERE `user_id`=?";
            $result = pdo_query($sql, $user_id);

            // 刷新各种权限
            foreach ($result as $row) {
                if (isset($row['valuestr'])) {
                    $_SESSION[$OJ_NAME . '_' . $row['rightstr']] = $row['valuestr'];
                } else {
                    $_SESSION[$OJ_NAME . '_' . $row['rightstr']] = true;
                }
            }
            if (isset($_SESSION[$OJ_NAME . '_vip'])) {  // VIP mark can access all [VIP] marked contest
                $sql = "select contest_id from contest where title like '%[VIP]%'";
                $result = pdo_query($sql);
                foreach ($result as $row) {
                    $_SESSION[$OJ_NAME . '_c' . $row['contest_id']] = true;
                }
            };
        }

        /**
         * 遍历会话变量，收集用户有权限的比赛ID
         */
        foreach ($_SESSION as $key => $value) {
            if ((mb_substr($key, $len, 1) == 'm' || mb_substr($key, $len, 1) == 'c') && intval(mb_substr($key, $len + 1)) > 0) {
                //echo substr($key,1)."<br>";
                $mycontests .= "," . intval(mb_substr($key, $len + 1));
            }
        }

        //echo "=====>$mycontests<====";

        if (strlen($mycontests) > 0)
            $mycontests = substr($mycontests, 1);
        if (isset($_GET['my']) && $mycontests != "")
            if (isset($_GET['my'])) $wheremy = " and( contest_id in ($mycontests) or user_id='" . $_SESSION[$OJ_NAME . '_user_id'] . "')";
    }

    $sql = "SELECT * FROM `contest` WHERE `defunct`='N' ORDER BY `contest_id` DESC LIMIT 1000";

    if ($keyword) {
        $sql = "SELECT *  FROM contest WHERE contest.defunct='N' AND contest.title LIKE ? $wheremy  ORDER BY contest_id DESC";
        $sql .= " limit " . strval($pstart) . "," . strval($pend);

        $result = pdo_query($sql, $keyword);
    } else {
        $sql = "SELECT *  FROM contest WHERE contest.defunct='N' $wheremy  ORDER BY contest_id DESC";
        $sql .= " limit " . strval($pstart) . "," . strval($pend);
        //echo $sql;
        $result = mysql_query_cache($sql);
    }

    $view_contest = array();
    $i = 0;

    /**
     * 遍历比赛查询结果，构建比赛列表显示数组
     * 根据比赛状态（已结束/待开始/进行中）设置不同的显示内容
     */
    foreach ($result as $row) {
        $view_contest[$i][0] = $row['contest_id'];

        if (trim($row['title']) == "")
            $row['title'] = $MSG_CONTEST . $row['contest_id'];

        $view_contest[$i][1] = "<a href='contest.php?cid=" . $row['contest_id'] . "'>" . $row['title'] . "</a>";
        $start_time = strtotime($row['start_time']);
        $end_time = strtotime($row['end_time']);
        $now = time();

        $length = $end_time - $start_time;
        $left = $end_time - $now;

        if ($end_time <= $now) {
            //已结束
            $view_contest[$i][2] = "<span class=text-muted>$MSG_Ended</span>" . " " . "<span class=text-muted>" . $row['end_time'] . "</span>";

        } else if ($now < $start_time) {
            //待开始
            $view_contest[$i][2] = "<span class=text-success>$MSG_Start</span>" . " " . $row['start_time'] . "&nbsp;";
            $view_contest[$i][2] .= "<span class=text-success>$MSG_TotalTime</span>" . " " . formatTimeLength($length);
        } else {
            //进行中
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

/////////////////////////Template
/**
 * 根据URL参数加载相应的模板文件
 * 包含比赛详情模板或比赛列表模板
 */
if (isset($_GET['cid']))
    require("template/" . $OJ_TEMPLATE . "/contest.php");
else
    require("template/" . $OJ_TEMPLATE . "/contestset.php");
/////////////////////////Common foot
/**
 * 包含缓存结束文件（如果存在）
 */
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
