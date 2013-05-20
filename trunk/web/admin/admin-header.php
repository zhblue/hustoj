<?php @session_start();?>

<?php if (!(isset($_SESSION['administrator'])||
			isset($_SESSION['contest_creator'])||
			isset($_SESSION['problem_editor']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
require_once("../include/db_info.inc.php");
?>

