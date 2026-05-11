<?php
if(isset($OJ_NO_CONTEST_WATCHER)&&$OJ_NO_CONTEST_WATCHER) require_once("contest-check.php");
if($OJ_MEMCACHE){
		$sql="SELECT
        user_id,nick,solution.result,solution.num,solution.in_date,solution.pass_rate
                FROM
                   solution where solution.contest_id='$cid' and num>=0 and problem_id>0
        ORDER BY user_id,solution_id";
        $result = mysql_query_cache($sql);
        if($result) $rows_cnt=count($result);
        else $rows_cnt=0;
}else{
		$sql="SELECT
        user_id,nick,solution.result,solution.num,solution.in_date,solution.pass_rate
                FROM
                   solution where solution.contest_id=? and num>=0 and problem_id>0
        ORDER BY user_id,solution_id";
        $result = pdo_query($sql,$cid);
        if($result) $rows_cnt=count($result);
        else $rows_cnt=0;
}
?>
