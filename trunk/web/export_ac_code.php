<?php
require_once("./include/db_info.inc.php");
header("content-type:   application/file");
header("content-disposition:   attachment;   filename=\"ac-" . $_SESSION[$OJ_NAME . '_' . 'user_id'] . ".txt");
if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $view_errors = "<a href=./loginpage.php>$MSG_Login</a>";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}
$sql = "select distinct source,problem_id from source_code inner join \n 
		(select solution_id,problem_id from solution where user_id=? and result=4) S \n
		on source_code.solution_id=S.solution_id  \n
			where S.problem_id not in (select problem_id from contest_problem where contest_id \n
							in (select contest_id from contest where start_time < now() and end_time >now()) \n
						) order by problem_id";
//echo "$sql";
echo $_SESSION[$OJ_NAME . '_' . 'user_id'] . "\r\n";

$result = pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id']);
foreach ($result as $row) {
    echo "Problem" . $row['problem_id'] . ":\r\n";
    echo preg_replace("(\n)", "\r\n", $row['source']);
    echo "\r\n------------------------------------------------------\r\n";
}

?>
