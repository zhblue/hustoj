<!-- /web/bbs.php -->

<?php
require_once("./include/db_info.inc.php");

if($OJ_BBS===false || (isset($OJ_EXAM_CONTEST_ID) && $OJ_EXAM_CONTEST_ID>0))
{
  header("Content-type: text/html; charset=utf-8");
  echo $MSG_BBS_NOT_ALLOWED_FOR_EXAM;
  exit ();
}

$parm = "";

if(isset($_GET['pid']))
{
  $pid = intval($_GET['pid']);
  $parm = "pid=".$pid;
}
else
{
  $pid = 0;
}

if(isset($_GET['cid']))
{
  $cid = intval($_GET['cid']);
  $parm .= "&cid=".$cid;
}
else
{
  $cid = 0;
}

if($OJ_BBS == "discuss")
{
  echo ("<script>location.href='discuss/discuss.php?".$parm."';</script>");
}
else if($OJ_BBS == "discuss3")
{
  if($parm == "")
    echo ("<script>location.href='discuss.php';</script>");
  else
    echo ("<script>location.href='discuss.php?".$parm."';</script>");
}
else
{
  if(isset($_GET['pid']))
  {
    $url = ("bbs/search.php?fid[]=2&keywords=".$pid); //chenge this to your own phpBB search link
  }
  else
  {
    $url = ("bbs/");
  }
  echo ("<script>location.href='".$url."';</script>");
}

?>
