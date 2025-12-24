<?php
require_once('../include/db_info.inc.php');
require_once('../include/setlang.php');
require_once('../include/const.inc.php');
if (!$OJ_BBS || $OJ_BBS != "discuss3") {
    header("location: ../index.php");
    exit();
}
if (isset($OJ_EXAM_CONTEST_ID) && $OJ_EXAM_CONTEST_ID > 0) {
    header("Content-type: text/html; charset=utf-8");
    echo $MSG_BBS_NOT_ALLOWED_FOR_EXAM;
    exit ();

}
if (isset($_GET['tid']) && !isset($_GET['cid'])) {
    $tid = intval($_GET['tid']);
    $cid = pdo_query("select cid from topic where tid=?", $tid)[0][0];
    if ($cid > 0) $_GET['cid'] = $cid;
//		echo "cid:".$cid;

}
ob_start();
?>
