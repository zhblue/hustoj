<?php
/**
 * 在线评测系统问题页面处理脚本
 * 处理问题查看请求，支持练习模式和比赛模式两种访问方式
 * 验证用户权限，检查问题可用性，并渲染问题页面
 */

/**
 * 缓存时间设置（秒）
 * @var int
 */
$cache_time = 10;

/**
 * 缓存共享开关
 * @var bool
 */
$OJ_CACHE_SHARE = false;

// 引入系统必需的配置文件和函数库
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/bbcode.php');
require_once('./include/const.inc.php');
require_once('./include/my_func.inc.php');
require_once('./include/setlang.php');

// 根据语言设置加载对应的语言文件
if (isset($OJ_LANG)) {
    require_once("./lang/$OJ_LANG.php");
}

// 获取当前时间，格式为 Y-m-d H:i
$now = date("Y-m-d H:i", time());

// 处理比赛ID参数
if (isset($_GET['cid']))
    $ucid = "&cid=" . intval($_GET['cid']);
else
    $ucid = "";

/**
 * 问题访问模式标识
 * @var bool $pr_flag 练习模式标识
 * @var bool $co_flag 比赛模式标识
 */
$pr_flag = false;
$co_flag = false;

// 根据请求参数判断访问模式：练习模式或比赛模式
if (isset($_GET['id'])) {
    // 练习模式：直接通过问题ID访问
    $id = intval($_GET['id']);

    // 查询问题是否被比赛使用
    $sql = "select c.contest_id,c.title from contest c inner join contest_problem cp on c.contest_id=cp.contest_id and cp.problem_id=?  WHERE ( c.`end_time`>'$now' and c.defunct='N' ) or c.`private`='1' ";
    $used_in_contests = pdo_query($sql, $id);

    // 根据用户权限和系统设置构建查询语句
    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'problem_verifiter']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']) || isset($_SESSION[$OJ_NAME . '_' . 'problem_editor']))
        $sql = "SELECT * FROM `problem` WHERE `problem_id`=?";
    else if ($OJ_FREE_PRACTICE)
        $sql = "SELECT * FROM `problem` WHERE defunct='N' and `problem_id`=?";
    else
        $sql = "SELECT * FROM `problem` WHERE `problem_id`=? AND `defunct`='N' AND `problem_id` NOT IN (
				SELECT `problem_id` FROM `contest_problem` WHERE `contest_id` IN (
					SELECT `contest_id` FROM `contest` WHERE ( `end_time`>'$now' and defunct='N' ) or `private`='1'    
				)
			)";        //////////  people should not see the problem used in contest before they end by modifying url in browser address bar
    /////////   if you give students opportunities to test their result out side the contest ,they can bypass the penalty time of 20 mins for
    /////////   each non-AC sumbission in contest. if you give them opportunities to view problems before exam ,they will ask classmates to write
    /////////   code for them in advance, if you want to share private contest problem to practice you should modify the contest into public

    $pr_flag = true;
    $result = pdo_query($sql, $id);
} else if (isset($_GET['cid']) && isset($_GET['pid'])) {
    // 比赛模式：通过比赛ID和题目编号访问
    $cid = intval($_GET['cid']);
    $pid = intval($_GET['pid']);
    require_once("contest-check.php");
    
    // 根据用户权限构建比赛查询语句
    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']) || isset($_SESSION[$OJ_NAME . '_' . 'problem_editor']))
        $sql = "SELECT langmask,private,defunct FROM `contest` WHERE `contest_id`=?";
    else
        $sql = "SELECT langmask,private,defunct FROM `contest` WHERE `defunct`='N' AND `contest_id`=? AND (`start_time`<='$now' AND ('$now'<`end_time` or private='N') )";

    $result = pdo_query($sql, $cid);
    $rows_cnt = empty($result) ? 0 : count($result);
    
    // 检查比赛是否存在
    if (empty($result) && !$OJ_FREE_PRACTICE && !isset($_SESSION[$OJ_NAME . '_administrator']) && !isset($_SESSION[$OJ_NAME . "_c" . $cid])) {
        $view_errors = "<title>$MSG_CONTEST</title><h2>No such Contest!</h2>";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }

    $row = ($result[0]);
    $contest_ok = true;

    // 检查比赛访问权限
    if ($row[1] && !isset($_SESSION[$OJ_NAME . '_' . 'c' . $cid]))
        $contest_ok = false;

    if ($row[2] == 'Y')
        $contest_ok = false;

    if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']) || isset($_SESSION[$OJ_NAME . '_' . 'problem_editor']))
        $contest_ok = true;

    $ok_cnt = $rows_cnt == 1;
    $langmask = $row[0];

    if (!$contest_ok) {
        // 比赛未开始或无权限访问
        $view_errors = "No such Contest!";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    } else {
        // 比赛已开始，获取题目信息
        $sql = "SELECT * FROM `problem` WHERE `problem_id`=(
			SELECT `problem_id` FROM `contest_problem` WHERE `contest_id`=? AND `num`=?
		)";

        $result = pdo_query($sql, $cid, $pid);
        $id = $result[0]['problem_id'];
    }

    // 检查比赛公开性
    if (!$contest_ok) {
        $view_errors = "Not Invited!";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }

    $co_flag = true;
} else {
    // 无效的请求参数
    $view_errors = "<title>$MSG_NO_SUCH_PROBLEM</title><h2>$MSG_NO_SUCH_PROBLEM</h2>";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 处理查询结果
if (count($result) != 1) {
    $view_errors = "";

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        if (count($used_in_contests) > 0) {

            if (!(isset($OJ_EXAM_CONTEST_ID) || isset($OJ_ON_SITE_CONTEST_ID))) {
                $view_errors .= "<hr><br>$MSG_PROBLEM_USED_IN:";
                foreach ($used_in_contests as $contests) {
                    $view_errors .= "<a class='label label-warning' href='contest.php?cid=" . $contests[0] . "'>" . $contests[1] . " </a><br>";

                }
                //echo "</div>";
            }

        } else {
            $view_title = "<title>$MSG_NO_SUCH_PROBLEM!</title>";
            $view_errors .= "<h2>$MSG_NO_SUCH_PROBLEM!</h2>";
        }
    } else {
        $view_title = "<title>$MSG_NO_SUCH_PROBLEM!</title>";
        $view_errors .= "<h2>$MSG_NO_SUCH_PROBLEM!</h2>";
    }
    if (!(isset($_SESSION[$OJ_NAME . '_administrator']) || isset($_SESSION[$OJ_NAME . '_problem_editor']))) {
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }
} else {
    $row = $result[0];
    $view_title = $row['title'];
}

// 检查NOIP模式比赛中的题目显示限制
$flag = false;
if (isset($OJ_NOIP_KEYWORD) && $OJ_NOIP_KEYWORD) {
    //检查当前题目是不是在NOIP模式比赛中，如果是则不显示AC数量 2020.7.11 by ivan_zhou
    //$now =  date('Y-m-d H:i', time());
    $sql = "select 1 from `contest_problem` where (`problem_id`= ? ) and `contest_id` IN (select `contest_id` from `contest` where `start_time` < ? and `end_time` > ? and `title` like ?)";
    $rrs = pdo_query($sql, $id, $now, $now, "%$OJ_NOIP_KEYWORD%");
    $flag = !empty($rrs);
}

// 根据NOIP模式或题目锁定状态隐藏提交和通过数量
if ($flag || problem_locked($id, 28)) {
    $row['accepted'] = '<font color="red"> ? </font>';
    $row['submit'] = '<font color="red"> ? </font>';

    // 使用$OJ_NOIP_TISHI 条件语句确定是否显示提示信息
    if (isset($OJ_NOIP_HINT) && $OJ_NOIP_HINT) {
        //$row['hint'] = $MSG_NOIP_NOHINT;
    } else if (!(isset($_SESSION[$OJ_NAME . '_administrator']) || isset($_SESSION[$OJ_NAME . '_contest_creator']))) {
        $row['hint'] = $MSG_NOIP_NOHINT;
    }
}

// 读取题目输出文件名
$solution_file = "$OJ_DATA/$id/output.name";

if (file_exists($solution_file)) {
    // 读取文件内容
    $content = file_get_contents($solution_file);

    // 提取文件名部分（去掉扩展名）
    $filename = pathinfo($content, PATHINFO_FILENAME);

}
//if($row['spj']<=1) $row['description']=aaiw($row['description']);
/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/problem.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');

