<?php
require_once ("admin-header.php");
require_once("../include/check_post_key.php");
if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}

require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");

//contest_id
$title = $_POST['title'];
$content = $_POST['content'];
$showInMenu = $_POST['showInMenu'];
$menu = $showInMenu == "on" ? 1 : 0;

$user_id = $_SESSION[$OJ_NAME.'_'.'user_id'];



$content = str_replace("<p>", "", $content);
$content = str_replace("</p>", "<br />", $content);
//$content = str_replace(",", "&#44;", $content);



$sql = "INSERT INTO news(`user_id`,`title`,`content`,`time`,`menu`) VALUES(?,?,?,now(),?)";
pdo_query($sql,$user_id,$title,$content,$menu);
$sessionDataKey = $OJ_NAME.'_'."_MENU_NEWS_CACHE";
unset($_SESSION[$sessionDataKey]);

echo "<script>window.location.href=\"news_list.php\";</script>";
?>
