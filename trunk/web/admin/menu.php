<?php require_once("admin-header.php");

	if(isset($OJ_LANG)){
		require_once("../lang/$OJ_LANG.php");
	}
	

?>

<ul class="nav">
<?php if (isset($_SESSION['administrator'])){
	?>

	<li>
		<div class='menu-btn'><a class="a-color" href="news_add_page.php"><?php echo $MSG_ADD.$MSG_NEWS?></a></div>
	<li>
		<div class='menu-btn'><a class="a-color" href="news_list.php"><?php echo $MSG_NEWS.$MSG_LIST?></a></div>
		
<?php }
if (isset($_SESSION['administrator'])||isset($_SESSION['problem_editor'])){
?>
	<li>
		<div class='menu-btn'><a class="a-color" href="problem_add_page.php"><?php echo $MSG_ADD.$MSG_PROBLEM?></a></div>
<?php }
if (isset($_SESSION['administrator'])||isset($_SESSION['contest_creator'])||isset($_SESSION['problem_editor'])){
?>
	<li>
		<div class='menu-btn' ><a class="a-color" href="problem_list.php"><?php echo $MSG_PROBLEM.$MSG_LIST?></a></div>
<?php }
if (isset($_SESSION['administrator'])||isset($_SESSION['contest_creator'])){
?>		
<li>
	<div class='menu-btn'><a class="a-color" href="contest_add.php"><?php echo $MSG_ADD.$MSG_CONTEST?></a></div>
<?php }
if (isset($_SESSION['administrator'])||isset($_SESSION['contest_creator'])){
?>
<li>
	<div class='menu-btn' ><a class="a-color" href="contest_list.php"><?php echo $MSG_CONTEST.$MSG_LIST?></a></div>
<?php }
if (isset($_SESSION['administrator'])){
?>
<li>
	<div class='menu-btn' ><a class="a-color" href="team_generate.php"><?php echo $MSG_TEAMGENERATOR?></a></div>
<li>
	<div   class="menu-btn" ><a class="a-color" href="setmsg.php"><?php echo $MSG_SETMESSAGE?></a></div>
<?php }
if (isset($_SESSION['administrator'])||isset( $_SESSION['password_setter'] )){
?><li>
	<div class='menu-btn'><a class="a-color" href="changepass.php"><?php echo $MSG_SETPASSWORD?></a></div>
<?php }
if (isset($_SESSION['administrator'])){
?><li>
	<div class='menu-btn' ><a class="a-color" href="rejudge.php"><?php echo $MSG_REJUDGE?></a></div>
<?php }
if (isset($_SESSION['administrator'])){
?><li>
	<div class='menu-btn'><a class="a-color" href="privilege_add.php"><?php echo $MSG_ADD.$MSG_PRIVILEGE?></a></div>
<?php }
if (isset($_SESSION['administrator'])){
?><li>
	<div class='menu-btn' ><a class="a-color" href="privilege_list.php"><?php echo $MSG_PRIVILEGE.$MSG_LIST?></a></div>
<?php }
if (isset($_SESSION['administrator'])){
?><li>
	<div class='menu-btn' ><a class="a-color" href="source_give.php"><?php echo $MSG_GIVESOURCE?></a></div>
<?php }
if (isset($_SESSION['administrator'])){
?><li>
	<div class='menu-btn' ><a class="a-color" href="problem_export.php"><?php echo $MSG_EXPORT.$MSG_PROBLEM?></a></div>
<?php }
if (isset($_SESSION['administrator'])){
?><li>
	<div class='menu-btn' ><a class="a-color" href="problem_import.php"><?php echo $MSG_IMPORT.$MSG_PROBLEM?></a></div>
<?php }
if (isset($_SESSION['administrator'])){
?><li>
	<div class='menu-btn' ><a class="a-color" href="update_db.php"><?php echo $MSG_UPDATE_DATABASE?></a></div>
<?php }
if (isset($OJ_ONLINE)&&$OJ_ONLINE){
?><li>
	<div class='menu-btn'require("../online.php") ><a class="a-color" href=""><?php echo $MSG_ONLINE?></a></div>
<?php }
?>

<?php 
//<li><div class='menu-btn'require("http://code.google.com/p/hustoj/") target="_blank"><a class="a-color" href="">HUSTOJ</a></div>
//<li><div class='menu-btn'require("http://code.google.com/p/freeproblemset/") target="_blank"><a class="a-color" href="">FreeProblemSet</a></div>
?>

<?php if (isset($_SESSION['administrator'])&&!$OJ_SAE){
?>
	<li><div   title="Create your own data" class='btn menu-btn'>
		<a class="a-color" href="problem_copy.php">CopyProblem</a></div>
	<li><div   title="Danger,Use it on your own risk" class='btn menu-btn'>
		<a class="a-color" href="problem_changeid.php">ReOrderProblem</a></div>
<?php }
?>
</ul>
