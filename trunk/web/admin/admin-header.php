<?php 
ini_set("memory_limit", "1024M");  //set this bigger to import big files.
ini_set("max_execution_time", "600");
require_once("../include/db_info.inc.php");
require_once ("../include/my_func.inc.php");
?>
<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel=stylesheet href='../include/hoj.css' type='text/css'>
<script src="../template/bs3/jquery.min.js"></script>
<script>
$("document").ready(function (){
  $("form").append("<div id='csrf' />");
  $("#csrf").load("../csrf.php");
});

</script>
<?php if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])||isset($_SESSION[$OJ_NAME.'_'.'password_setter']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
require_once("../template/$OJ_TEMPLATE/css.php");
if(file_exists("../lang/$OJ_LANG.php")) require_once("../lang/$OJ_LANG.php");
?>
<iframe src="../session.php" height=0px width=0px ></iframe>
