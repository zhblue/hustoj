<?php
ini_set("display_errors", "Off");
header("content-type:   application/excel");
require_once("./include/db_info.inc.php");
global $mark_base, $mark_per_problem, $mark_per_punish;
$mark_start = 60;
$mark_end = 100;
$mark_sigma = 5;
if (isset($OJ_LANG)) {
    require_once("./lang/$OJ_LANG.php");
}
require_once("./include/const.inc.php");
require_once("./include/my_func.inc.php");

/**
 * 竞赛选手类，用于记录和计算选手的解题情况、时间和分数
 */
class TM
{
    var $solved = 0;      // 解决的问题数量
    var $time = 0;        // 总用时
    var $p_wa_num;        // 每个问题的错误提交次数
    var $p_ac_sec;        // 每个问题的通过时间
    var $user_id;         // 用户ID
    var $nick;            // 用户昵称
    var $mark = 0;        // 得分

    function TM()
    {
        $this->solved = 0;
        $this->time = 0;
        $this->p_wa_num = array();
        $this->p_ac_sec = array();
    }

    /**
     * 添加一次提交记录
     * @param int $pid 问题ID
     * @param int $sec 提交时间（秒）
     * @param int $res 提交结果
     * @param int $mark_base 基础分数
     * @param int $mark_per_problem 每题分数
     * @param int $mark_per_punish 惩罚分数
     */
    function Add($pid, $sec, $res, $mark_base, $mark_per_problem, $mark_per_punish)
    {
        global $OJ_CE_PENALTY;
//		echo "Add $pid $sec $res<br>";

        if (isset($this->p_ac_sec[$pid]))
            return;
        if ($res != 4) {
            //$this->p_ac_sec[$pid]=0;
            if (isset($OJ_CE_PENALTY) && !$OJ_CE_PENALTY && $res == 11) return;  // ACM WF punish no ce
            if (isset($this->p_wa_num[$pid])) {
                $this->p_wa_num[$pid]++;
            } else {
                $this->p_wa_num[$pid] = 1;
            }
        } else {
            $this->p_ac_sec[$pid] = $sec;
            $this->solved++;
            $this->time += $sec + $this->p_wa_num[$pid] * 1200;
            if ($this->mark == 0) {
                $this->mark = $mark_base;
            } else {
                $this->mark += $mark_per_problem;
            }
            if ($this->p_wa_num[$pid] == "") $this->p_wa_num[$pid] = 0;
            $punish = intval($this->p_wa_num[$pid] * $mark_per_punish);
            if ($punish < intval($mark_per_problem * .8))
                $this->mark -= $punish;
            else
                $this->mark -= intval($mark_per_problem * .8);
        }
    }
}

/**
 * 比较函数，用于排序选手
 * @param object $A 选手A
 * @param object $B 选手B
 * @return bool 排序结果
 */
function s_cmp($A, $B)
{
//	echo "Cmp....<br>";
    if ($A->solved != $B->solved) return $A->solved < $B->solved;
    else return $A->time > $B->time;
}

/**
 * 计算正态分布值
 * @param float $x 输入值
 * @param float $u 均值
 * @param float $s 标准差
 * @return float 正态分布概率密度值
 */
function normalDistribution($x, $u, $s)
{

    $ret = 1 / ($s * sqrt(2 * M_PI))
        * pow(M_E, -pow($x - $u, 2) / (2 * $s * $s));

    return $ret;

}

/**
 * 根据正态分布为用户分配分数
 * @param array $users 用户数组
 * @param int $start 分数起始值
 * @param int $end 分数结束值
 * @param int $s 分布参数
 * @return int 返回用户数量
 */
function getMark($users, $start, $end, $s)
{
    $accum = 0;
    $p = 0;
    $ret = 0;
    $cn = count($users);


    for ($i = $end; $i > $start; $i--) {

        $prob = $cn
            * normalDistribution($i, ($start + $end) / 2 + 10, ($end - $start)
                / $s);
        $accum += $prob;


    }

    $p = $accum / $cn;
    $accum = 0;
    $i = 0;

    for ($i = $end; $i > $start; $i--) {
        $prob = $cn
            * normalDistribution($i, ($start + $end) / 2 + 10, ($end - $start)
                / $s);
        $accum += $prob;
        while ($accum > $p / 2) {
            if ($ret < $cn)
                $users[$ret]->mark = $i;
            $accum -= $p;
            $ret++;
        }
    }
    while ($ret < $cn) {
        $users[$ret]->mark = $users[$ret - 1]->mark;
        $ret++;
    }
    return $ret;

}


// contest start time
if (!isset($_GET['cid'])) die("No Such Contest!");
$cid = intval($_GET['cid']);
if (isset($OJ_NO_CONTEST_WATCHER) && $OJ_NO_CONTEST_WATCHER) require_once("contest-check.php");

// 获取竞赛信息
$sql = "SELECT `start_time`,`title`,`end_time` FROM `contest` WHERE `contest_id`=?";
$result = mysql_query_cache($sql, $cid);
$rows_cnt = count($result);
$start_time = 0;
$end_time = 0;
if ($rows_cnt > 0) {
    $row = $result[0];
    $start_time = strtotime($row[0]);
    $title = $row[1];
    $end_time = strtotime($row[2]);
    $ftitle = mb_substr($title, 0, 32);
    $ftitle = rawurlencode(preg_replace('/\.|\\\|\\/|\:|\*|\?|\"|\<|\>|\|/', '', str_replace(' ', '', "C$cid-" . $ftitle)) . ".xls");
    header("Content-Disposition: attachment; filename*=utf-8''" . $ftitle);

}

if ($start_time == 0) {
    echo "No Such Contest";
    //require_once("oj-footer.php");
    exit(0);
}

if ($start_time > time()) {
    echo "Contest Not Started!";
    //require_once("oj-footer.php");
    exit(0);
}
$noip = (time() < $end_time) && (stripos($title, $OJ_NOIP_KEYWORD) !== false);
if (isset($_SESSION[$OJ_NAME . '_' . "administrator"]) ||
    isset($_SESSION[$OJ_NAME . '_' . "m$cid"]) ||
    isset($_SESSION[$OJ_NAME . '_' . "source_browser"]) ||
    isset($_SESSION[$OJ_NAME . '_' . "contest_creator"])
) {
    $noip = false;
} else if ($noip || contest_locked($cid, 20)) {
    $view_errors = "<h2>$MSG_NOIP_WARNING</h2>";
    $view_errors .= "<br>" . $contest_locks[2] . $contest_locks[4];  // 2^2 + 2^4 = 20
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}
if (!isset($OJ_RANK_LOCK_PERCENT)) $OJ_RANK_LOCK_PERCENT = 0;
$lock = $end_time - ($end_time - $start_time) * $OJ_RANK_LOCK_PERCENT;

// 获取竞赛题目数量并设置分数参数
$sql = "SELECT count(1) FROM `contest_problem` WHERE `contest_id`=?";
$result = mysql_query_cache($sql, $cid);
$row = $result[0];
$pid_cnt = intval($row[0]);
if ($pid_cnt == 1) {
    $mark_base = 100;
    $mark_per_problem = 0;
} else {
    $mark_per_problem = (100 - $mark_base) / ($pid_cnt - 1);
}
$mark_per_punish = $mark_per_problem / 5;

// 获取竞赛提交记录
$sql = "select
        user_id,nick,solution.result,solution.num,solution.in_date
                        from solution where solution.contest_id=? and num>=0 and problem_id>0
        ORDER BY user_id,solution_id";

//echo $sql;
$result = mysql_query_cache($sql, $cid);
$user_cnt = 0;
$user_name = '';
$U = array();
foreach ($result as $row) {
    $n_user = $row['user_id'];
    if (strcmp($user_name, $n_user)) {
        $user_cnt++;
        $U[$user_cnt] = new TM();
        $U[$user_cnt]->user_id = $row['user_id'];
        $U[$user_cnt]->nick = $row['nick'];

        $user_name = $n_user;
    }

    if (time() < $end_time + $OJ_RANK_LOCK_DELAY && $lock < strtotime($row['in_date']) && !isset($_SESSION[$OJ_NAME . '_' . 'administrator']))
        $U[$user_cnt]->Add($row['num'], strtotime($row['in_date']) - $start_time, 0, $mark_base, $mark_per_problem, $mark_per_punish);
    else
        $U[$user_cnt]->Add($row['num'], strtotime($row['in_date']) - $start_time, intval($row['result']), $mark_base, $mark_per_problem, $mark_per_punish);
}

usort($U, "s_cmp");
// 获取未提交的用户列表
$absentList = mysql_query_cache("select user_id,nick from users where user_id in (select user_id from privilege where rightstr='c$cid' and user_id not in (select distinct user_id from solution where contest_id=?))", $cid);
foreach ($absentList as $row) {
    $U[$user_cnt] = new TM();
    $U[$user_cnt]->user_id = $row['user_id'];
    $U[$user_cnt]->nick = $row['nick'];
    $user_cnt++;
}

$rank = 1;
//echo "<style> td{font-size:14} </style>";
//echo "<title>Contest RankList -- $title</title>";
echo "<center><h3>Contest RankList -- $title</h3></center>";
echo "<table border=1><tr><td>Rank<td>User<td>Nick<td>Solved<td>Mark<td>Penalty";
for ($i = 0; $i < $pid_cnt; $i++)
    echo "<td>$PID[$i]";
echo "</tr>";
getMark($U, $mark_start, $mark_end, $mark_sigma);

for ($i = 0; $i < $user_cnt; $i++) {
    if ($i & 1) echo "<tr class=oddrow align=center>";
    else echo "<tr class=evenrow align=center>";
    // don't count rank while nick start with *
    if ($U[$i]->nick[0] == '*') {
        echo "<td>*";
    } else {
        echo "<td>$rank";
        $rank++;
    }

    $uuid = $U[$i]->user_id;

    $usolved = $U[$i]->solved;
    echo "<td>$uuid";
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
        $U[$i]->nick = iconv("utf8", "gbk", $U[$i]->nick);
    }
    $nick = $row['nick'];
    if (preg_match('/^[=+-@]/', $nick)) {
    $nick = "'" . $nick;
    }
    echo "<td>" . htmlspecialchars($nick). "</td>";
    echo "<td>$usolved</td>";
    echo "<td>";
    if ($usolved == 0) $U[$i]->mark = 0;

    echo $U[$i]->mark > 0 ? intval($U[$i]->mark) : 0;
    echo "</td>";
    echo "<td>" . sec2str($U[$i]->time) . "</td>";
    for ($j = 0; $j < $pid_cnt; $j++) {
        echo "<td>";
        if (isset($U[$i])) {
            if (isset($U[$i]->p_ac_sec[$j]) && $U[$i]->p_ac_sec[$j] > 0)
                echo sec2str($U[$i]->p_ac_sec[$j]);
            if (isset($U[$i]->p_wa_num[$j]) && $U[$i]->p_wa_num[$j] > 0)
                echo "(-" . $U[$i]->p_wa_num[$j] . ")";
        }
        echo "</td>";
    }
    echo "</tr>";
}
echo "</table>";


