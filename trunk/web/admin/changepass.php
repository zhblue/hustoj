<?php require_once("admin-header.php");?>
<?php if (!(isset($_SESSION['administrator'])|| isset($_SESSION['password_setter']) )){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
if(isset($_POST['do'])){
	//echo $_POST['user_id'];
	require_once("../include/check_post_key.php");
	//echo $_POST['passwd'];
	require_once("../include/my_func.inc.php");
	
	$user_id=$_POST['user_id'];
    $passwd =$_POST['passwd'];
    if (get_magic_quotes_gpc ()) {
		$user_id = stripslashes ( $user_id);
		$passwd = stripslashes ( $passwd);
	}
	$user_id=mysql_real_escape_string($user_id);
	$passwd=pwGen($passwd);
	$sql="update `users` set `password`='$passwd' where `user_id`='$user_id'  and user_id not in( select user_id from privilege where rightstr='administrator') ";
	mysql_query($sql);
	if (mysql_affected_rows()==1) echo "Password Changed!";
  else echo "No such user! or He/Her is an administrator!";
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
			<div class="changepass  button-align">

					<div class="change">Change Password:</div><hr/>
					<form action='changepass.php' method="post">
						
						<div ><strong class="in-title">USER </strong><input class="in" type=text name="user_id"></div>
						<div ><strong class="in-title">PASS </strong><input class="in" type=text name="passwd"></div>
						<?php require_once("../include/set_post_key.php");?>
						<input type='hidden' name='do' value='do'>
						<input class="btn" type=submit value='Change'>
					</form>
			</div>
		</div>
	</div>
</div>

</body>
</html>