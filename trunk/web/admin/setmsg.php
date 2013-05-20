<?php require_once("admin-header.php");
if (!(isset($_SESSION['administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
if(isset($_POST['do'])){
	require_once("../include/check_post_key.php");
	$fp=fopen($OJ_SAE?"saestor://web/msg.txt":"msg.txt","w");
	fputs($fp, stripslashes($_POST['msg']));
	fclose($fp);
	echo "Update At ".date('Y-m-d h:i:s');
}


$msg=file_get_contents($OJ_SAE?"saestor://web/msg.txt":"msg.txt");

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
			<div class="msg col">
				<div class="news-header align-center" style="padding-bottom:10px;">Set Message</div>
				<form action='setmsg.php' method='post'>
					<textarea name='msg' rows=25 class="input input-xxlarge" ><?php echo $msg?></textarea><br>
					<input type='hidden' name='do' value='do'>
					<input class="btn" type='submit' value='change'>
					<?php require_once("../include/set_post_key.php");?>
				</form>
				
			</div>
		</div>
	</div>
</div>

</body>
</html>