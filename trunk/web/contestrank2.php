<?php
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
 * TM类用于存储和管理竞赛中用户的成绩信息
 * 包括解题数量、用时、错误提交次数等统计信息
 */
class TM
{
    var $solved = 0;    // 解决的问题数量
    var $time = 0;      // 总用时（包括罚时）
    var $p_wa_num;      // 每个问题的错误提交次数数组
    var $p_ac_sec;      // 每个问题的通过时间数组
    var $user_id;       // 用户ID
    var $nick;          // 用户昵称

    /**
     * TM类的构造函数，初始化用户成绩对象
     */
    function TM()
    {
        $this->solved = 0;
        $this->time = 0;
        $this->p_wa_num = array();
        $this->p_ac_sec = array();
    }

    /**
     * 添加一次提交记录到用户成绩中
     * @param int $pid 问题ID
     * @param int $sec 从比赛开始到提交的时间（秒）
     * @param int $res 提交结果代码
     */
    function Add($pid, $sec, $res)
    {
//              echo "Add $pid $sec $res<br>";
        if (isset($this->p_ac_sec[$pid]))
            return;
        if ($res != 4) {
            //$this->p_ac_sec[$pid]=0;
            if (isset($GLOBALS['OJ_CE_PENALTY']) && !$GLOBALS['OJ_CE_PENALTY'] && $res == 11) return;  // ACM WF punish no ce 
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
 * 用于比较两个TM对象的排序函数
 * 按解题数量降序排列，解题数量相同时按用时升序排列
 * @param TM $A 第一个TM对象
 * @param TM $B 第二个TM对象
 * @return bool 返回比较结果，用于usort排序
 */
function s_cmp($A, $B)
{
//      echo "Cmp....<br>";
    if ($A->solved != $B->solved) return $A->solved < $B->solved;
    else return $A->time > $B->time;
}

// 获取竞赛ID并验证竞赛是否存在
if (!isset($_GET['cid'])) die("No Such Contest!");
$cid = intval($_GET['cid']);

// 验证竞赛ID是否有效
if ($cid <= 0) die("Invalid Contest ID!");

// 查询竞赛基本信息（开始时间、标题、结束时间）
$sql = "SELECT `start_time`,`title`,`end_time` FROM `contest` WHERE `contest_id`=?";
$result = mysql_query_cache($sql, $cid);
if ($result !== false) {
    $rows_cnt = count($result);
} else {
    $rows_cnt = 0;
    $result = array(); // 初始化为空数组以避免后续错误
}

$start_time = 0;
$end_time = 0;
if ($rows_cnt > 0) {
    $row = $result[0];
    $start_time = strtotime($row['start_time']);
    $end_time = strtotime($row['end_time']);
    $title = $row['title'];
} else {
    if (!$OJ_MEMCACHE) {
        $view_errors = "No Such Contest";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }
}

// 检查竞赛是否已经开始
if ($start_time == 0 || $start_time > time()) {
    $view_errors = "$MSG_CONTEST $MSG_Contest_Pending!";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 检查竞赛是否为NOIP类型且需要特殊权限控制
$noip = (time() < $end_time) && (isset($OJ_NOIP_KEYWORD) && stripos($title, $OJ_NOIP_KEYWORD) !== false);
if (isset($_SESSION[$OJ_NAME . '_' . "administrator"]) ||
    isset($_SESSION[$OJ_NAME . '_' . "m$cid"]) ||
    isset($_SESSION[$OJ_NAME . '_' . "source_browser"]) ||
    isset($_SESSION[$OJ_NAME . '_' . "contest_creator"])
) {
    $noip = false;
} else if ($noip || contest_locked($cid, 20)) {
    $view_errors = "<h2> $MSG_NOIP_WARNING</h2>";
    if (isset($contest_locks)) {
        $view_errors .= "<br>" . $contest_locks[2] . $contest_locks[4];  // 2^2 + 2^4 = 20
    }
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 计算排名锁定时间
if (!isset($OJ_RANK_LOCK_PERCENT)) $OJ_RANK_LOCK_PERCENT = 0;
$lock = $end_time - ($end_time - $start_time) * $OJ_RANK_LOCK_PERCENT;

// 获取竞赛中的问题数量
$sql = "SELECT count(1) as pbc FROM `contest_problem` WHERE `contest_id`=?";
$result = mysql_query_cache($sql, $cid);
if ($result !== false) {
    $rows_cnt = count($result);
} else {
    $rows_cnt = 0;
    $result = array(); // 初始化为空数组以避免后续错误
}

$row = $result[0];
$pid_cnt = intval($row['pbc']);

// 加载竞赛解决方案数据
require("./include/contest_solutions.php");

// 处理用户提交数据，构建排名信息
$user_cnt = 0;
$user_name = '';
$U = array();
for ($i = 0; $i < $rows_cnt; $i++) {
    $row = $result[$i];
    $n_user = $row['user_id'];
    if (strcmp($user_name, $n_user) !== 0) { // 明确使用 !== 进行比较
        $user_cnt++;
        $U[$user_cnt] = new TM();

        $U[$user_cnt]->user_id = $row['user_id'];
        $U[$user_cnt]->nick = $row['nick'];

        $user_name = $n_user;
    }
    if (time() < $end_time + (isset($OJ_RANK_LOCK_DELAY) ? $OJ_RANK_LOCK_DELAY : 0) && $lock < strtotime($row['in_date']))
        $U[$user_cnt]->Add($row['num'], strtotime($row['in_date']) - $start_time, 0);
    else
        $U[$user_cnt]->Add($row['num'], strtotime($row['in_date']) - $start_time, intval($row['result']));

}
$solution_json = json_encode($result);

if (!$OJ_MEMCACHE)
    usort($U, "s_cmp");

// 统计每个问题的首杀信息
$first_blood = array();
for ($i = 0; $i < $pid_cnt; $i++) {
    $first_blood[$i] = "";
}

$sql = "select s.num,s.user_id from solution s ,(select num,min(solution_id) minId from solution where contest_id=? and result=4 GROUP BY num ) c where s.solution_id = c.minId";
$fb = mysql_query_cache($sql, $cid);
if ($fb !== false) {
    $rows_cnt = count($fb);
} else {
    $rows_cnt = 0;
    $fb = array(); // 初始化为空数组以避免后续错误
}

foreach ($fb as $row) {
    $first_blood[$row['num']] = $row['user_id'];
}

/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/contestrank2.php");

/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
