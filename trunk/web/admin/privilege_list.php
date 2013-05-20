
<?php require("admin-header.php");
require_once("../include/set_get_key.php");
if (!(isset($_SESSION['administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
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
			<div class="bk marginTop">
				<?php
				echo "<title>Privilege List</title>"; 
				echo "<center><h2>Privilege List</h2></center>";
				$sql="select * FROM privilege where rightstr in ('administrator','source_browser','contest_creator','http_judge','problem_editor') ";
				$result=mysql_query($sql) or die(mysql_error());
				echo "<center><table class='table table-striped' width=60%>";
				echo "<thead style='background-color:#eee'><tr><td>user<td>right<td>defunc</tr></thead>";
				for (;$row=mysql_fetch_object($result);){
					echo "<tr>";
					echo "<td>".$row->user_id;
					echo "<td>".$row->rightstr;
					echo "<td><a href=privilege_delete.php?uid=$row->user_id&rightstr=$row->rightstr&getkey=".$_SESSION['getkey'].">Delete</a>";

					echo "</tr>";
				}
				echo "</table></center>";
				?>
			</div>
		</div>
	</div>
</div>

</body>
</html>



