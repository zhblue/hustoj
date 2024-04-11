<!-- /web/threadadmin.php -->

<?php
session_start();
require_once("include/db_info.inc.php");
require_once("discuss_func.inc.php");

$tid = intval($_REQUEST['tid']);
$cid = pdo_query("SELECT cid FROM topic WHERE tid=?",$tid)[0][0];

if($_REQUEST['target']=='reply')
{
  $rid = intval($_REQUEST['rid']);
  $stat = -1;

  if($_REQUEST['action']=='resume') $stat = 0;
  if($_REQUEST['action']=='disable') $stat = 1;
  if($_REQUEST['action']=='delete') $stat = 2;

  if($stat==-1) err_msg("Wrong action.");

  $rid = intval($rid);
  $sql = "UPDATE reply SET status=? WHERE `rid`=?";

  if(!isset($_SESSION[$OJ_NAME.'_'.'administrator']))
  {
    if($stat!=2)
    { 
      $sql .= " AND ?!=''";
      err_msg("<a href=\"../loginpage.php\">Please Login First</a>");
    }
    else
    { 
      $sql .= " AND author_id=?";
    }
    pdo_query($sql, $stat,$rid,$_SESSION[$OJ_NAME.'_'.'user_id']);
  }
  else
  {
    echo "$sql";
    pdo_query($sql, $stat,$rid);
  }

  header('Location: thread.php?tid='.$tid."&cid=$cid");
  exit();
}


if($_REQUEST['target']=='thread')
{
  $tid = intval($_REQUEST['tid']);
  $toplevel = -1;
  $stat = -1;
  
  if($_REQUEST['action']=='sticky')
    if(isset($_REQUEST['level'])&&is_numeric($_REQUEST['level']) &&$_REQUEST['level']>=0 &&$_REQUEST['level']<4)
      $toplevel = intval($_REQUEST['level']);
    else
      err_msg("Invalid sticky level.");

  if($_REQUEST['action']=='resume') $stat = 0;
  if($_REQUEST['action']=='lock') $stat = 1;
  if($_REQUEST['action']=='delete') $stat = 2;

  if(!isset($_SESSION[$OJ_NAME.'_'.'administrator']) && !isset($_SESSION[$OJ_NAME.'_'.'user_id']))
    errmsg("<a href=./loginpage.php>Please Login First</a>");

  if($toplevel==-1 && $stat==-1)
    errmsg("Wrong action.");

  $tid = intval($tid);

  if($stat==-1)
    $sql = "UPDATE topic SET top_level = $toplevel WHERE `tid` = '$tid'";
  else
    $sql = "UPDATE topic SET status = $stat WHERE `tid` = '$tid'";

  pdo_query($sql);
   if($stat!=2){
      header('Location: thread.php?tid='.$tid."&cid=$cid");
   }else{
        pdo_query("update reply set status=2 where topic_id=?",$tid);
        header('Location: discuss.php'."?cid=$cid");
   }

}
?>
