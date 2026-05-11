<?php require_once("admin-header.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
	exit(1);
}?>
<?php if(isset($_POST['do'])){
	require_once("../include/check_post_key.php");
	$from=$_POST['from'];
	$to=$_POST['to'];
	$start=intval($_POST['start']);
	$end=intval($_POST['end']);
	$sql="update `solution` set `user_id`=? where `user_id`=? and problem_id>=? and problem_id<=? and result=4";
	//echo $sql;
	echo pdo_query($sql,$to,$from,$start,$end)." source file given!";
	
}
?>
<div class="container">
<form action='source_give.php' method=post>
	<b>Give source:</b><br />
	From:<input type=text size=10 name="from" value="zhblue"><br />
	To:<input type=text size=10 name="to" value="standard"><br />
	start pid:<input type=text size=10 name="start"><br />
	end pid:<input type=text size=10 name="end"><br />
	<input type='hidden' name='do' value='do'>
	
	<?php require_once("../include/set_post_key.php");?>
	<input type=submit value='GiveMySourceToHim'>
</form>
</div>
