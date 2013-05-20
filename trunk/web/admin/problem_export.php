


<?php require_once("admin-header.php");
if (!(isset($_SESSION['administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
?>

<?php require_once("admin-header.php");
	if(isset($OJ_LANG)){
		require_once("../lang/$OJ_LANG.php");
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
			<div class="content-center" >
				<div style="width:50%;" class="padding-center bk">
				<form action='problem_export_xml.php' method=post>
					<div style="font-size:22px;padding-bottom:10px;">Export Problem:</div>
					<div>from pid:<input type=text size=10 name="start" value=1000>
					to pid:<input type=text size=10 name="end" value=1000></div>
					<div>or in<input type=text size=40 name="in" value=""></div>
					<div><input type='hidden' name='do' value='do'>
					<input type=submit name=submit value='Export'>
				   <input type=submit value='Download'></div>
				   <?php require_once("../include/set_post_key.php");?>
				</form>
				<p>* from-to will working will empty IN </p>
				<p>* if using IN,from-to will not working.</p>
				<p>* IN can go with "," seperated problem_ids like [1000,1020]</p>
				</div>
			</div>
		</div>
	</div>
</div>

</body>
</html>




