<!-- /web/post.php -->

<?php
require_once("discuss_func.inc.php");
require_once("include/db_info.inc.php");

if(!isset($_SESSION[$OJ_NAME.'_'.'user_id']))
{
  require_once("oj-header.php");

  echo "<a href=loginpage.php>Please Login First</a>";
  
  require_once("../oj-footer.php");
  exit(0);
}

if(strlen($_POST['content'])>5000)
{
  require_once("oj-header.php");
  echo "Your contents is too long!";
  require_once("../oj-footer.php");
  exit(0);
}

if(has_bad_words($_POST['content']))
{
  require_once("oj-header.php");
  echo "Your contents is too Bad!";
  require_once("../oj-footer.php");
  exit(0);
}
if(has_bad_words($_POST['title']))
{
  require_once("oj-header.php");
  echo "Your title is too Bad!";
  require_once("../oj-footer.php");
  exit(0);
}

if(strlen($_POST['title'])>60)
{
  require_once("oj-header.php");
  echo "Your title is too long!";
  require_once("../oj-footer.php");
  exit(0);
}

$vcode = "";
if($OJ_VCODE)
{
  if(isset($_POST['vcode']))
    $vcode = trim($_POST['vcode']);

  if($OJ_VCODE && ($vcode!=$_SESSION[$OJ_NAME.'_'."vcode"] || $vcode=="" || $vcode==null))
  {
    $_SESSION[$OJ_NAME.'_'."vfail"] = true;
    echo "<script language='javascript'>\n";
    echo "alert('Verify Code Wrong!');\n";
    echo "history.go(-1);\n";
    echo "</script>";
    exit( 0 );
  }
}


$tid = null;
if($_REQUEST['action']=='new')
{
  if(isset($_POST['title']) && isset($_POST['content']) && $_POST['title']!='' && $_POST['content']!='')
  {
    if(isset($_REQUEST['pid']) && $_REQUEST['pid']!='')
      $pid = intval($_REQUEST['pid']);
    else
      $pid = 0;

    if(isset($_REQUEST['cid'])&&$_REQUEST['cid']!='')
      $cid = intval($_REQUEST['cid']);
    else
      $cid = 0;

    if($pid==0)
    {
      if($cid>0)
      {
        $problem_id = htmlentities($_POST['pid'],ENT_QUOTES,'UTF-8');
        //echo "problem_id:".$problem_id;
        $num = strpos($PID,$problem_id);
        //echo "num:$num";
        $pid = pdo_query("select problem_id from contest_problem where contest_id=? and num=?",$cid,$num)[0][0];
        //echo "pid:$pid";
      }
    }

    $sql = "INSERT INTO `topic` (`title`, `author_id`, `cid`, `pid`) VALUES(?,?,?,?)";
    //echo $sql;

    $rows = pdo_query($sql,$_POST['title'],$_SESSION[$OJ_NAME.'_'.'user_id'],$cid,$pid);
    
    if(!$rows)
    {
      //echo $sql;
      echo('Unable to post new.');
    }
    else
    {
      $tid = $rows;
    }
  }
  else
    echo('Error!');
}

if($_REQUEST['action']=='reply' || !is_null($tid))
{
  if(is_null($tid))
    $tid = intval($_POST['tid']);

  if(!is_null($tid) && isset($_POST['content']) && $_POST['content']!='')
  {
    $rows = pdo_query("SELECT tid FROM topic WHERE tid=?",$tid);

    if(isset($rows[0]))
    {
      $ip = ($_SERVER['REMOTE_ADDR']);

      if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
      {
        $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $tmp_ip = explode(',',$REMOTE_ADDR);
        $ip = (htmlentities($tmp_ip[0],ENT_QUOTES,"UTF-8"));
      }

      $sql = "INSERT INTO `reply` (`author_id`, `time`, `content`, `topic_id`,`ip`) VALUES(?,NOW(),?,?,?)";

      if(pdo_query($sql, $_SESSION[$OJ_NAME.'_'.'user_id'],$_POST['content'],$tid,$ip))
      {
        if(isset($_REQUEST['cid']))
        {
          $cid = intval($_REQUEST['cid']);
          header('Location: thread.php?cid='.$cid.'&tid='.$tid);
        }
        else
        {
          header('Location: thread.php?tid='.$tid);
        }
        exit(0);
      }
      else
      {
        echo('Unable to post.');
      }
    }
    else
    {
      echo "reply non-exists topic";
    }
  }
  else
    echo('Error!');
}

require_once("../oj-footer.php");
?>
