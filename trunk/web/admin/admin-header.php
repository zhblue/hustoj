<?php 
require_once(dirname(__FILE__) ."/../include/db_info.inc.php");
;?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel=stylesheet href='../include/hoj.css' type='text/css'>
<script src="../template/bs3/jquery.min.js"></script>
<script>
$("document").ready(function (){
  $("form").append("<div id='csrf' />");
  $("#csrf").load("../csrf.php");
});

</script>
<?php if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||
			isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])||
			isset($_SESSION[$OJ_NAME.'_'.'problem_editor']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
require_once(dirname(__FILE__) ."/../template/$OJ_TEMPLATE/css.php");
if(file_exists(dirname(__FILE__) ."/../lang/$OJ_LANG.php")) require_once(dirname(__FILE__) ."/../lang/$OJ_LANG.php");
?>

