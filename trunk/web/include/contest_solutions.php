<?php
if(isset($OJ_NO_CONTEST_WATCHER)&&$OJ_NO_CONTEST_WATCHER) require_once("contest-check.php");
$sql="SELECT
        user_id,nick,solution.result,solution.num,solution.in_date,solution.pass_rate
FROM
        solution where solution.contest_id=? and num>=0 and problem_id>0 and in_date >=? and in_date < ?
ORDER BY user_id,solution_id";
if($OJ_MEMCACHE){
        $result = mysql_query_cache($sql,$cid,date("Y-m-d H:i:s",$start_time),date("Y-m-d H:i:s",$end_time));
}else{
        $result = pdo_query($sql,$cid,date("Y-m-d H:i:s",$start_time),date("Y-m-d H:i:s",$end_time));
}
if($result) $rows_cnt=count($result);
else $rows_cnt=0;

?>
