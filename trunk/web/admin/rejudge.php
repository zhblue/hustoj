<?php require("admin-header.php");

if (!(isset($_SESSION['administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}?>
<?php if(isset($_POST['do'])){
	require_once("../include/check_post_key.php");
	if (isset($_POST['rjpid'])){
		$rjpid=intval($_POST['rjpid']);
		$sql="UPDATE `solution` SET `result`=1 WHERE `problem_id`=".$rjpid;
		mysql_query($sql) or die(mysql_error());
		$sql="delete from `sim` WHERE `s_id` in (select solution_id from solution where `problem_id`=".$rjpid.")";
		mysql_query($sql) or die(mysql_error());
		$url="../status.php?problem_id=".$rjpid;
		echo "Rejudged Problem ".$rjpid;
		echo "<script>location.href='$url';</script>";
	}
	else if (isset($_POST['rjsid'])){
		$rjsid=intval($_POST['rjsid']);
		$sql="UPDATE `solution` SET `result`=1 WHERE `solution_id`=".$rjsid;
		mysql_query($sql) or die(mysql_error());
		$sql="delete from `sim` WHERE `s_id`=".$rjsid;
		mysql_query($sql) or die(mysql_error());
		$url="../status.php?top=".($rjsid+1);
		echo "Rejudged Runid ".$rjsid;
		echo "<script>location.href='$url';</script>";
	}
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
			<div class="rejudge col">
			<div class="news-header align-center" style="padding:15px 0 15px 0;">Rejudge<hr/></div>
				
				<ol>
				<li>Problem</li>
				<form action='rejudge.php' method=post>
					<input type=input name='rjpid'>	<input type='hidden' name='do' value='do'>
					<input type=submit value=submit>
					<?php require_once("../include/set_post_key.php");?>
				</form>
				<li>Solution</li>
				<form action='rejudge.php' method=post>
					<input type=input name='rjsid'>	<input type='hidden' name='do' value='do'>
					<input type=hidden name="postkey" value="<?php echo $_SESSION['postkey']?>">
					<input type=submit value=submit>
				</form>
			</div>
		</div>
	</div>
</div>

</body>
</html>