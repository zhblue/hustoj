<?php require_once("admin-header.php");
if (!(isset($_SESSION['administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
?>
<?php require_once("../include/db_info.inc.php");?>
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
			<div class=" news-content">
				<?php
				include_once("../fckeditor/fckeditor.php") ;
				?>

				<form method="POST" action="news_add.php">

				<p align="center" class="news-header">Post a News</p>
				<p align="center">Title:<input type="text" name="title" size="71"></p>

				<p align="center">Content:<br>
					<?php
					$description = new FCKeditor('content') ;
					$description->BasePath = '../fckeditor/' ;
					$description->Height = 450 ;
					$description->Width=700;

					$description->Value = '<p></p>' ;
					$description->Create() ;
					?>
				</p>
				<?php require_once("../include/set_post_key.php");?>
				<div style="text-align:center;"><input type="submit" value="Submit" name="submit" class="btn"></div>
				</div></form>
				<p>
			</div>
		</div>
	</div>
</div>

</body>
</html>
