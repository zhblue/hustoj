<?php
$OJ_CACHE_SHARE = true;
$cache_time = 10;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
require_once("./include/const.inc.php");
require_once("./include/my_func.inc.php");
require_once("./include/memcache.php");
$view_title = $MSG_CONTEST . $MSG_RANKLIST;
$title = "";

/**
 * TM类 - 用于存储和计算用户在比赛中的成绩信息
 * 包含解题数量、总时间、错误提交次数、通过时间等信息
 */
class TM
{
    var $solved = 0;      // 解题数量
    var $time = 0;        // 总时间（包含罚时）
    var $p_wa_num;        // 每道题的错误提交次数数组
    var $p_ac_sec;        // 每道题的通过时间数组
    var $user_id;         // 用户ID
    var $nick;            // 用户昵称

    /**
     * 构造函数 - 初始化TM对象的属性
     */
    function __construct()
    {
        $this->solved = 0;
        $this->time = 0;
        $this->p_wa_num = array();
        $this->p_ac_sec = array();
    }

    /**
     * 添加提交记录到用户成绩中
     * @param int $pid 题目编号
     * @param int $sec 通过时间（相对于比赛开始的时间）
     * @param int $res 提交结果代码
     */
    function Add($pid, $sec, $res)
    {
        global $OJ_CE_PENALTY;
        //echo "Add $pid $sec $res<br>";
        if ($sec < 0) return;  // restarted contest ignore previous submission
        if (isset($this->p_ac_sec[$pid]))  // already solved  ignore later submission
            return;
        if ($res != 4) {
            //$this->p_ac_sec[$pid]=0;
            if (isset($OJ_CE_PENALTY) && !$OJ_CE_PENALTY && $res == 11)
                return;  // ACM WF punish no ce
            if (isset($this->p_wa_num[$pid])) {
                $this->p_wa_num[$pid]++;
            } else {
                $this->p_wa_num[$pid] = 1;
            }
        } else {
            $this->p_ac_sec[$pid] = $sec;
            $this->solved++;

            if (!isset($this->p_wa_num[$pid]))
                $this->p_wa_num[$pid] = 0;

            $this->time += $sec + $this->p_wa_num[$pid] * 1200;   // 每次错误提交罚时20分钟
            //echo "Time:".$this->time."<br>";
            //echo "Solved:".$this->solved."<br>";
        }
    }
}

/**
 * 排序比较函数 - 用于对用户成绩进行排序
 * 先按解题数量降序，再按总时间升序
 * @param object $A 第一个TM对象
 * @param object $B 第二个TM对象
 * @return int 比较结果（用于usort）
 */
function s_cmp($A, $B)
{
    //echo "Cmp....<br>";
    if ($A->solved != $B->solved)
        return $A->solved < $B->solved;
    else
        return $A->time > $B->time;
}

// contest start time
if (!isset($_GET['cid']))
    die("No Such Contest!");

$cid = intval($_GET['cid']);

// 查询比赛信息
$sql = "select `start_time`,`title`,`end_time` FROM `contest` WHERE `contest_id`=? ";
$result = mysql_query_cache($sql, $cid);
if ($result)
    $rows_cnt = count($result);
else
    $rows_cnt = 0;


$start_time = 0;
$end_time = 0;

if ($rows_cnt > 0) {
    //$row=$result[0];

    $row = $result[0];

    $start_time = strtotime($row['start_time']);
    $end_time = strtotime($row['end_time']);
    $title = $row['title'];
    $view_title = $title;
    if (isset($_GET['down'])) {
        header("Content-type:   application/excel");
        $ftitle = rawurlencode(preg_replace('/\.|\\\|\\/|\:|\*|\?|\"|\<|\>|\|/', '', $title));
        header("content-disposition:   attachment;   filename=contest" . $cid . "_" . $ftitle . ".xls");
    }

}

if (!$OJ_MEMCACHE)
    if ($start_time == 0) {
        $view_errors = "Wrong $MSG_CONTEST id";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }

// 检查比赛是否已经开始
if ($start_time > time()) {
    $view_errors = "$MSG_CONTEST $MSG_Contest_Pending!";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 检查是否为NOIP比赛并进行权限验证
$noip = (time() < $end_time) && (stripos($title, $OJ_NOIP_KEYWORD) !== false);
if (isset($_SESSION[$OJ_NAME . '_' . "administrator"]) ||
    isset($_SESSION[$OJ_NAME . '_' . "m$cid"]) ||
    isset($_SESSION[$OJ_NAME . '_' . "source_browser"]) ||
    isset($_SESSION[$OJ_NAME . '_' . "contest_creator"])
) {
    $noip = false;
} else if ($noip || contest_locked($cid, 20)) {   // 20 = 2^2 + 2^4
    $view_errors = "<h2>$MSG_NOIP_WARNING</h2>";
    $view_errors .= "<br>" . $contest_locks[2] . $contest_locks[4];  // 2^2 + 2^4 = 20
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

if (!isset($OJ_RANK_LOCK_PERCENT))
    $OJ_RANK_LOCK_PERCENT = 0;

$lock = $end_time - ($end_time - $start_time) * $OJ_RANK_LOCK_PERCENT;

//echo $lock.'-'.date("Y-m-d H:i:s",$lock);
$view_lock_time = $start_time + ($end_time - $start_time) * (1 - $OJ_RANK_LOCK_PERCENT);
$locked_msg = "";

if (time() > $view_lock_time && time() < $end_time + $OJ_RANK_LOCK_DELAY) {
    $locked_msg = "The board has been locked.";
}

// 获取比赛题目数量
$sql = "SELECT count(1) as pbc FROM `contest_problem` WHERE `contest_id`=?";
$result = mysql_query_cache($sql, $cid);

if ($result)
    $rows_cnt = count($result);
else
    $rows_cnt = 0;

$row = $result[0];

//$row=$result[0];
$pid_cnt = intval($row['pbc']);

require("./include/contest_solutions.php");

$user_cnt = 0;
$user_name = '';
$U = array();

//$U[$user_cnt]=new TM();
for ($i = 0; $i < $rows_cnt; $i++) {
    $row = $result[$i];
    $n_user = $row['user_id'];

    if (strcmp($user_name, $n_user)) {
        $user_cnt++;
        $U[$user_cnt] = new TM();

        $U[$user_cnt]->user_id = $row['user_id'];
        $U[$user_cnt]->nick = $row['nick'];

        $user_name = $n_user;
    }

    if (time() < $end_time + $OJ_RANK_LOCK_DELAY && $lock < strtotime($row['in_date']))
        $U[$user_cnt]->Add($row['num'], strtotime($row['in_date']) - $start_time, 0);
    else
        $U[$user_cnt]->Add($row['num'], strtotime($row['in_date']) - $start_time, intval($row['result']));
}

usort($U, "s_cmp");

////firstblood
$first_blood = array();
for ($i = 0; $i < $pid_cnt; $i++) {
    $first_blood[$i] = "";
}

// 查询每道题的首杀信息
$sql = "select s.num,s.user_id from solution s ,
(select num,min(solution_id) minId from solution where contest_id=? and result=4 GROUP BY num ) c where s.solution_id = c.minId";
$fb = mysql_query_cache($sql, $cid);

if ($fb)
    $rows_cnt = count($fb);
else
    $rows_cnt = 0;


for ($i = 0; $i < $rows_cnt; $i++) {
    $row = $fb[$i];
    $first_blood[$row['num']] = $row['user_id'];
}

// 获取只注册但未提交的参赛用户
$absent = mysql_query_cache("select user_id from privilege where rightstr='c$cid' and user_id not in (select distinct user_id from solution where contest_id=?)", $cid);
$absentList = mysql_query_cache("select user_id,nick from users where user_id in (select user_id from privilege where rightstr='c$cid' and user_id not in (select distinct user_id from solution where contest_id=?))", $cid);
foreach ($absentList as $row) {
    $U[$user_cnt] = new TM();
    $U[$user_cnt]->user_id = $row['user_id'];
    $U[$user_cnt]->nick = $row['nick'];
    $user_cnt++;
}

/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/contestrank.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');

