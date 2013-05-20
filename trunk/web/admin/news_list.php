<?php require_once("admin-header.php");
require_once("../include/set_get_key.php");
if (!isset($_SESSION['administrator'])){
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
			<div class="align-center">
				<h2 class="h2-head">NEWS LIST</h2>
				<div class="news-content">
					<table width=90% class="table">
					<thead>
						<tr>
							<td>PID</td>
							<td>Title</td>
							<td>Date</td>
							<td>Status</td>
							<td>Edit</td>
						</tr>
					</thead>
					<tbody>
					<?php
					$sql="select `news_id`,`user_id`,`title`,`time`,`defunct` FROM `news` order by `news_id` desc";
					$result=mysql_query($sql) or die(mysql_error());
					for (;$row=mysql_fetch_object($result);){
						echo "<tr>";
						echo "<td>".$row->news_id;
						//echo "<input type=checkbox name='pid[]' value='$row->problem_id'>";
						echo "<td><a href='news_edit.php?id=$row->news_id'>".$row->title."</a>";
						echo "<td>".$row->time;
						echo "<td><a href=news_df_change.php?id=$row->news_id&getkey=".$_SESSION['getkey'].">".($row->defunct=="N"?"<span class=green>Available</span>":"<span class=red>Reserved</span>")."</a>";
							echo "<td><a href=news_edit.php?id=$row->news_id>Edit</a>";
						
						echo "</tr>";
					}

					echo "</tr>";
					?>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

</body>
</html>

