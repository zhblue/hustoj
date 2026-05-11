<?php
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
ini_set("display_errors", "Off");

if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $view_errors = "<a href=./loginpage.php>$MSG_Login</a>";
    require_once("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}
$contest_id = intval($_GET['cid']);
if (!(isset($_SESSION[$OJ_NAME . '_' . 'm' . $contest_id]) || isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator']))) {
    $view_errors = "<a href=./loginpage.php>No privileges!</a>";
    require_once("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}
header("content-type:   application/file");
header("content-disposition:   attachment;   filename=\"logs-$contest_id.txt\"");

$sql = "select user_id,problem_id,result,source   from source_code right join
                (select solution_id,problem_id,user_id,result from solution where contest_id=? ) S
                on source_code.solution_id=S.solution_id order by S.solution_id";
require_once("./include/const.inc.php");
#echo $sql;
$result = pdo_query($sql, $contest_id);
foreach ($result as $row) {
    echo $row['user_id'] . ":Problem" . $row['problem_id'] . ":" . $judge_result[$row['result']];
    echo "\r\n" . $row['source'];
    echo "\r\n------------------------------------------------------\r\n";
}

?>
