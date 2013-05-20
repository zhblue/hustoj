<?php require_once("admin-header.php");?>
<?php if (!(isset($_SESSION['administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
if(isset($_POST['do'])){
	require_once("../include/check_post_key.php");
	$user_id=mysql_real_escape_string($_POST['user_id']);
	$rightstr =$_POST['rightstr'];
	$sql="insert into `privilege` values('$user_id','$rightstr','N')";
	mysql_query($sql);
	if (mysql_affected_rows()==1) echo "$user_id $rightstr added!";
	else echo "No such user!";
}
?>

<html>
	<head>
		<title>OJ Administration</title>
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Cache-Control" content="no-cache">
		<meta http-equiv="Content-Language" content="zh-cn">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel=stylesheet href='admin.css' type='text/css'>
	</head>
<body>



<div class="container-fluid">
	<?php require_once("admin-bar.php"); ?>
	<div class="row-fluid top-space">
		<div class="span2" >
			<div class="menu-group"  >
				<?php require_once("menu.php") ?>
			</div>
		</div>
		<div class="span10">
			<div class="text-notcenter">
				<div class="padding-center bk" style="width:50%">
				<form method=post>
				<?php require("../include/set_post_key.php");?>
					<div  style="font-size:24px;margin-bottom: 10px;margin-top: 15px;"><strong>Add privilege for User:</strong></div>
					<p>User:</p><input type=text size=10 name="user_id"><br />
					<p>Privilege:</p>
					<select name="rightstr">
				<?php
				$rightarray=array("administrator","problem_editor","source_browser","contest_creator","http_judge","password_setter" );
				while(list($key, $val)=each($rightarray)) {
					if (isset($rightstr) && ($rightstr == $val)) {
						echo '<option value="'.$val.'" selected>'.$val.'</option>';
					} else {
						echo '<option value="'.$val.'">'.$val.'</option>';
					}
				}
				?></select><br />
					<input type='hidden' name='do' value='do'>
					<input class="btn"  type=submit value='Add'>
				</form>
				<form method=post>
					<div  style="font-size:24px;margin-bottom: 10px;"><strong>Add contest for User:</strong></div>
					<p>User:</p><input type=text size=10 name="user_id"><br />
					<p>ontest:</p><input type=text size=10 name="rightstr"><div>c1000 for Contest1000</div>
					<input type='hidden' name='do' value='do'>
					<input class="btn"  type=submit value='Add'>
					<input type=hidden name="postkey" value="<?php echo $_SESSION['postkey']?>">
				</form>

			</div>
			</div>
		</div>
	</div>
</div>

</body>
</html>

