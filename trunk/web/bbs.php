<!-- /web/bbs.php -->

<?php
require_once("./include/db_info.inc.php");

if ($OJ_BBS === false || (isset($OJ_EXAM_CONTEST_ID) && $OJ_EXAM_CONTEST_ID > 0)) {
    header("Content-type: text/html; charset=utf-8");
    echo $MSG_BBS_NOT_ALLOWED_FOR_EXAM;
    exit ();
}

$parm = "";

if (isset($_GET['pid'])) {
    $pid = intval($_GET['pid']);
    $parm = "pid=" . urlencode($pid);
} else {
    $pid = 0;
}

if (isset($_GET['cid'])) {
    $cid = intval($_GET['cid']);
    if ($parm !== "") {
        $parm .= "&";
    }
    $parm .= "cid=" . urlencode($cid);
} else {
    $cid = 0;
}

if ($OJ_BBS == "discuss") {
    $redirect_url = 'discuss/discuss.php?' . $parm;
    echo("<script>location.href='" . addslashes($redirect_url) . "';</script>");
} else if ($OJ_BBS == "discuss3") {
    $redirect_url = $parm == "" ? 'discuss.php' : 'discuss.php?' . $parm;
    echo("<script>location.href='" . addslashes($redirect_url) . "';</script>");
} else {
    if (isset($_GET['pid'])) {
        $url = ("bbs/search.php?fid[]=2&keywords=" . urlencode($pid)); //chenge this to your own phpBB search link
    } else {
        $url = ("bbs/");
    }
    echo("<script>location.href='" . addslashes($url) . "';</script>");
}


