<?php
$cache_time = 90;
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/setlang.php');
$view_title = "Source Code";

require_once("./include/const.inc.php");
if (!isset($_GET['left'])) {
    $view_errors = "No such code!\n";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}
$ok = false;
$id = intval($_GET['left']);
$sql = "SELECT * FROM `solution` WHERE `solution_id`=?";
$result = pdo_query($sql, $id);
$row = $result[0];
$slanguage = $row['language'];
$sresult = $row['result'];
$stime = $row['time'];
$smemory = $row['memory'];
$sproblem_id = $row['problem_id'];
$view_user_id = $suser_id = $row['user_id'];


if (isset($OJ_AUTO_SHARE) && $OJ_AUTO_SHARE && isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $sql = "SELECT 1 FROM solution where 
			result=4 and problem_id=? and user_id=?";
    $rrs = pdo_query($sql, $sproblem_id, $_SESSION[$OJ_NAME . '_' . 'user_id']);
    $ok = (count($rrs) > 0);

}
$view_source = "No source code available!";
if (isset($_SESSION[$OJ_NAME . '_' . 'user_id']) && $row && $row['user_id'] == $_SESSION[$OJ_NAME . '_' . 'user_id']) $ok = true;
if (isset($_SESSION[$OJ_NAME . '_' . 'source_browser'])) $ok = true;

$sql = "SELECT `source` FROM `source_code` WHERE `solution_id`=?";
$result = pdo_query($sql, $id);
$row = $result[0];
if ($row)
    $view_source = $row['source'];

/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/comparesource.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
?>

