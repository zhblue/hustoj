<?php require_once("admin-header.php");
if (!(isset($_SESSION['administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}?>
<?php if(isset($_POST['do'])){
	require_once("../include/check_post_key.php");
	$from=mysql_real_escape_string($_POST['from']);
	$to=mysql_real_escape_string($_POST['to']);
	$start=intval($_POST['start']);
	$end=intval($_POST['end']);
	$sql="update `solution` set `user_id`='$to' where `user_id`='$from' and problem_id>=$start and problem_id<=$end and result=4";
	echo $sql;
	mysql_query($sql);
	echo mysql_affected_rows()." source file given!";
	
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
			<div class="col give-source">
				<form action='source_give.php' method=post>
					<div class="align-center news-header">Give source:<hr/></div>
					
					<div><h4>From:</h4><input type=text size=10 name="from" value="zhblue"></div>
					<div><h4>To:</h4><input style="right:40px;" type=text size=10 name="to" value="standard"></div>
					<div><h4>start pid:</h4><input type=text size=10 name="start"></div>
					<div><h4>end pid:</h4><input type=text size=10 name="end"></div>

					<input  type='hidden' name='do' value='do'>
					
					<?php require_once("../include/set_post_key.php");?>
					<input style="margin-left:70px;width:150px;margin-top:10px;" class="btn" type=submit value='GiveMySourceToHim'>
				</form>

			</div>
		</div>
	</div>
</div>

</body>
</html>
