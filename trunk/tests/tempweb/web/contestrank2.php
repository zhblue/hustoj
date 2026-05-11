<?php
/*
 * 滚榜工具
 * 仅限管理员或比赛组织者使用
 * 需要缓存、lock percent设定，锁定多少滚多少。
 * example: contestrank2.php?cid=10000&lock_percent=0.5
 */
$OJ_CACHE_SHARE = true;
$cache_time = 10;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = $MSG_CONTEST . $MSG_RANKLIST;
$title = "";
require_once("./include/const.inc.php");
require_once("./include/my_func.inc.php");
require_once("./include/memcache.php");

/**
 * 比赛用户信息类
 * 用于存储比赛中的用户解题统计信息
 */
class TM
{
    var $solved = 0;      // 解决的问题数量
    var $time = 0;        // 总用时（包括罚时）
    var $p_wa_num;        // 每个问题的错误提交次数
    var $p_ac_sec;        // 每个问题的AC时间
    var $user_id;         // 用户ID
    var $nick;            // 用户昵称

    /**
     * 构造函数，初始化用户统计信息
     */
    function TM()
    {
        $this->solved = 0;
        $this->time = 0;
        $this->p_wa_num = array();
        $this->p_ac_sec = array();
    }

    /**
     * 添加提交记录到用户统计
     * @param int $pid 问题ID
     * @param int $sec 提交时间（相对于比赛开始的秒数）
     * @param int $res 提交结果（4表示AC，其他表示错误）
     */
    function Add($pid, $sec, $res)
    {
//              echo "Add $pid $sec $res<br>";
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
            if (!isset($this->p_wa_num[$pid])) $this->p_wa_num[$pid] = 0;
            $this->time += $sec + $this->p_wa_num[$pid] * 1200;
//                      echo "Time:".$this->time."<br>";
//                      echo "Solved:".$this->solved."<br>";
        }
    }
}

/**
 * 比赛排名比较函数
 * 按解题数降序，用时升序排序
 * @param object $A 用户A对象
 * @param object $B 用户B对象
 * @return bool 排序结果
 */
function s_cmp($A, $B)
{
//      echo "Cmp....<br>";
    if ($A->solved != $B->solved) return $A->solved < $B->solved;
    else return $A->time > $B->time;
}

// contest start time
if (!isset($_GET['cid'])) die("No Such Contest!");
$cid = intval($_GET['cid']);

// 查询比赛基本信息
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

}
if (!$OJ_MEMCACHE)
    if ($start_time == 0) {
        $view_errors = "No Such Contest";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }

// 检查比赛是否已经开始
if ($start_time > time()) {
    $view_errors = "$MSG_CONTEST $MSG_Contest_Pending!";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 检查用户权限和比赛锁定状态
$noip = (time() < $end_time) && (stripos($title, $OJ_NOIP_KEYWORD) !== false);
if (isset($_SESSION[$OJ_NAME . '_' . "administrator"]) ||
    isset($_SESSION[$OJ_NAME . '_' . "m$cid"]) ||
    isset($_SESSION[$OJ_NAME . '_' . "source_browser"]) ||
    isset($_SESSION[$OJ_NAME . '_' . "contest_creator"])
) {
    $noip = false;
} else if ($noip || contest_locked($cid, 20)) {
    $view_errors = "<h2> $MSG_NOIP_WARNING</h2>";
    $view_errors .= "<br>" . $contest_locks[2] . $contest_locks[4];  // 2^2 + 2^4 = 20
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 计算锁定时间点
if (!isset($OJ_RANK_LOCK_PERCENT)) $OJ_RANK_LOCK_PERCENT = 0;
$lock = $end_time - ($end_time - $start_time) * $OJ_RANK_LOCK_PERCENT;

// 获取比赛题目数量
$sql = "SELECT count(1) as pbc FROM `contest_problem` WHERE `contest_id`=?";
$result = mysql_query_cache($sql, $cid);
if ($result) $rows_cnt = count($result);
else $rows_cnt = 0;

$row = $result[0];
$pid_cnt = intval($row['pbc']);

require("./include/contest_solutions.php");

// 处理用户提交数据，构建排名统计
$user_cnt = 0;
$user_name = '';
$U = array();
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
$solution_json = json_encode($result);

if (!$OJ_MEMCACHE)
    usort($U, "s_cmp");

////firstblood
// 统计首杀信息
$first_blood = array();
for ($i = 0; $i < $pid_cnt; $i++) {
    $first_blood[$i] = "";
}

$sql = "select s.num,s.user_id from solution s ,(select num,min(solution_id) minId from solution where contest_id=? and result=4 GROUP BY num ) c where s.solution_id = c.minId";
$fb = mysql_query_cache($sql, $cid);
if ($fb) $rows_cnt = count($fb);
else $rows_cnt = 0;

foreach ($fb as $row) {
    $first_blood[$row['num']] = $row['user_id'];
}

/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/contestrank2.php");

/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
?>
