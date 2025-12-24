<?php
$OJ_CACHE_SHARE = false;
$cache_time = 10;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = $MSG_CONTEST . $MSG_RANKLIST;
$show_title = $view_title;
$title = "";
require_once("./include/const.inc.php");
require_once("./include/my_func.inc.php");
require_once("./include/memcache.php");

/**
 * 竞赛用户成绩统计类
 * 用于记录和计算竞赛中用户的解题情况、时间、错误次数等信息
 */
class TM
{
    var $solved = 0;      // 已解决题目数量
    var $time = 0;        // 总用时
    var $p_wa_num;        // 每道题的错误提交次数
    var $p_ac_sec;        // 每道题的通过时间
    var $p_pass_rate;     // 每道题的通过率
    var $user_id;         // 用户ID
    var $nick;            // 用户昵称
    var $total;           // 总分

    function __construct()
    {
        $this->solved = 0;
        $this->time = 0;
        $this->p_wa_num = array();
        $this->p_ac_sec = array();
        $this->p_pass_rate = array();
        $this->total = 0;
    }

    /**
     * 添加题目提交记录
     * 根据提交结果更新用户的解题统计信息
     *
     * @param int $pid 题目编号
     * @param int $sec 通过时间（秒）
     * @param float $res 通过率
     * @param int $result 提交结果（4表示通过）
     */
    function Add($pid, $sec, $res, $result)
    {
//              echo "Add $pid $sec $res<br>";
        if (isset($this->p_ac_sec[$pid]))
            return;
        if ($result != 4) {
            //$this->p_ac_sec[$pid]=0;
            if (isset($this->p_pass_rate[$pid])) {
                if ($res > $this->p_pass_rate[$pid]) {
                    $this->total -= $this->p_pass_rate[$pid] * 100;
                    $this->p_pass_rate[$pid] = $res;
                    $this->total += $this->p_pass_rate[$pid] * 100;
                }
            } else {
                $this->p_pass_rate[$pid] = $res;
                $this->total += $res * 100;
            }
            if (isset($this->p_wa_num[$pid])) {
                $this->p_wa_num[$pid]++;
            } else {
                $this->p_wa_num[$pid] = 1;
            }

        } else {
            $this->p_ac_sec[$pid] = $sec;
            $this->solved++;
            if (!isset($this->p_wa_num[$pid])) $this->p_wa_num[$pid] = 0;
            if (isset($this->p_pass_rate[$pid])) {
                $this->total -= $this->p_pass_rate[$pid] * 100;
            } else {
                $this->p_pass_rate[$pid] = $res * 100;
            }

            $this->total += $res * 100;
            $this->p_pass_rate[$pid] = $res;
            $this->time += $sec + $this->p_wa_num[$pid] * 1200;
//                      echo "Time:".$this->time."<br>";
//                      echo "Solved:".$this->solved."<br>";
        }
    }
}

/**
 * 排序比较函数
 * 用于对竞赛用户进行排名比较，按总分、解题数、用时排序
 *
 * @param TM $A 用户A对象
 * @param TM $B 用户B对象
 * @return bool 比较结果
 */
function s_cmp($A, $B)
{
//      echo "Cmp....<br>";
    if ($A->total != $B->total) return $A->total < $B->total;
    else {
        if ($A->solved != $B->solved)
            return $A->solved < $B->solved;
        else
            return $A->time > $B->time;
    }
}

// 获取竞赛ID并验证竞赛是否存在
if (!isset($_GET['cid']) || !is_numeric($_GET['cid'])) die("No Such Contest!");
$cid = intval($_GET['cid']);

// 查询竞赛基本信息
$sql = "SELECT `start_time`,`title`,`end_time` FROM `contest` WHERE `contest_id`=?";
$result = mysql_query_cache($sql, $cid);
if ($result) $rows_cnt = count($result);
else $rows_cnt = 0;

$start_time = 0;
$end_time = 0;
if ($rows_cnt > 0) {
//       $row=$result[0];

    if ($OJ_MEMCACHE)
        $row = $result[0];
    else
        $row = $result[0];
    $start_time = strtotime($row['start_time']);
    $end_time = strtotime($row['end_time']);
    $title = $row['title'];
    $view_title = $title;
    if (isset($_GET['down']) && $_GET['down'] === '1') {
        header("Content-type:   application/excel");
        $ftitle = rawurlencode(preg_replace('/\.|\\\|\\/|\:|\*|\?|\"|\<|\>|\|/', '', str_replace(' ', '', $title)));
        header("content-disposition:   attachment;   filename=contest" . $cid . "_" . $ftitle . ".xls");
    }

}
if ($start_time == 0) {
    $view_errors = "No Such Contest";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 检查竞赛是否已经开始
if ($start_time > time()) {
    $view_errors = "$MSG_CONTEST $MSG_Contest_Pending!";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 检查竞赛是否被锁定或需要特殊权限
$noip = (time() < $end_time) && (stripos($title, $OJ_NOIP_KEYWORD) !== false);
if (isset($_SESSION[$OJ_NAME . '_' . "administrator"]) ||
    isset($_SESSION[$OJ_NAME . '_' . "m$cid"]) ||
    isset($_SESSION[$OJ_NAME . '_' . "source_browser"]) ||
    isset($_SESSION[$OJ_NAME . '_' . "contest_creator"])
) {
    $noip = false;
} else if ($noip || contest_locked($cid, 20)) {
    $view_errors = "<h2>$MSG_NOIP_WARNING</h2>";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 计算排名锁定时间
if (!isset($OJ_RANK_LOCK_PERCENT))
    $OJ_RANK_LOCK_PERCENT = 1;
$lock = $end_time - ($end_time - $start_time) * $OJ_RANK_LOCK_PERCENT;

//echo $lock.'-'.date("Y-m-d H:i:s",$lock);
$view_lock_time = $start_time + ($end_time - $start_time) * (1 - $OJ_RANK_LOCK_PERCENT);
$locked_msg = "";
if (time() > $view_lock_time && time() < $end_time + $OJ_RANK_LOCK_DELAY) {
    $locked_msg = "The board has been locked.";
}

// 获取竞赛题目数量
$sql = "SELECT count(1) as pbc FROM `contest_problem` WHERE `contest_id`=?";
$result = mysql_query_cache($sql, $cid);
if ($result) $rows_cnt = count($result);
else $rows_cnt = 0;

if ($result && isset($result[0])) {
    $row = $result[0];
    $pid_cnt = intval($row['pbc']);
} else {
    $pid_cnt = 0;
}

require("./include/contest_solutions.php");

// 统计用户提交记录并生成排名
$user_cnt = 0;
$user_name = '';
$U = array();
if (isset($result) && is_array($result)) {
    foreach ($result as $row) {
        $n_user = $row['user_id'];
        if (strcmp($user_name, $n_user)) {
            $user_cnt++;
            $U[$user_cnt] = new TM();
            $U[$user_cnt]->user_id = $row['user_id'];
            $U[$user_cnt]->nick = $row['nick'];
            $user_name = $n_user;
        }
        if ($row['result'] != 4 && $row['pass_rate'] >= 0.95) $row['pass_rate'] = 0.95;
        if ($row['result'] == 4 && !(isset($OJ_CONTEST_TOTAL_100) && $OJ_CONTEST_TOTAL_100))
            $row['pass_rate'] = 1.0;
        if (time() < $end_time + $OJ_RANK_LOCK_DELAY && $lock < strtotime($row['in_date']))
            $U[$user_cnt]->Add($row['num'], strtotime($row['in_date']) - $start_time, 0, 0);
        else
            $U[$user_cnt]->Add($row['num'], strtotime($row['in_date']) - $start_time, $row['pass_rate'], $row['result']);
    }
}
usort($U, "s_cmp");

// 获取一血信息
$first_blood = array();
for ($i = 0; $i < $pid_cnt; $i++) {
    $first_blood[$i] = "";
}

$sql = "select s.num,s.user_id from solution s ,
(select num,min(solution_id) minId from solution where contest_id=? and result=4 GROUP BY num ) c where s.solution_id = c.minId";
$fb = mysql_query_cache($sql, $cid);
if ($fb) $rows_cnt = count($fb);
else $rows_cnt = 0;

foreach ($fb as $row) {
    $first_blood[$row['num']] = $row['user_id'];
}

// 获取未提交用户列表
$absentList = mysql_query_cache("select user_id,nick from users where user_id in (select user_id from privilege where rightstr='c$cid' and user_id not in (select distinct user_id from solution where contest_id=?))", $cid);
if ($absentList && is_array($absentList)) {
    foreach ($absentList as $row) {
        $user_cnt++;
        $U[$user_cnt] = new TM();
        $U[$user_cnt]->user_id = $row['user_id'];
        $U[$user_cnt]->nick = $row['nick'];
    }
}


/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/contestrank-oi.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
