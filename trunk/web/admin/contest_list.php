<?php require("admin-header.php");

require_once("../include/set_get_key.php");
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
			<div class="">
				<center><h2 class="bottom-space">Contest List</h2></center>
				<?php 
					$sql="SELECT max(`contest_id`) as upid, min(`contest_id`) as btid  FROM `contest`";
					$page_cnt=50;
					$result=mysql_query($sql);
					echo mysql_error();
					$row=mysql_fetch_object($result);
					$base=intval($row->btid);
					$cnt=intval($row->upid)-$base;
					$cnt=intval($cnt/$page_cnt)+(($cnt%$page_cnt)>0?1:0);
					if (isset($_GET['page'])){
					        $page=intval($_GET['page']);
					}else $page=$cnt;
					$pstart=$base+$page_cnt*intval($page-1);
					$pend=$pstart+$page_cnt;
					for ($i=1;$i<=$cnt;$i++){
					        if ($i>1) echo '&nbsp;';
					        if ($i==$page) echo "<span class=red>$i</span>";
					        else echo "<a href='contest_list.php?page=".$i."'>".$i."</a>";
					}
					$sql="select `contest_id`,`title`,`start_time`,`end_time`,`private`,`defunct` FROM `contest` where contest_id>=$pstart and contest_id <=$pend order by `contest_id` desc";

					$result=mysql_query($sql) or die(mysql_error());
					echo "<center><table class='table col' width=90%>";
					echo "<thead style='background-color:#eee;'><tr><td>ContestID<td>Title<td>StartTime<td>EndTime<td>Private<td>Status<td>Edit<td>Copy<td>Export<td>Logs"; 
					echo "</tr></thead>";
					for (;$row=mysql_fetch_object($result);){
						echo "<tr>";
						echo "<td>".$row->contest_id;
						echo "<td><a href='../contest.php?cid=$row->contest_id'>".$row->title."</a>";
						echo "<td>".$row->start_time;
						echo "<td>".$row->end_time;
						$cid=$row->contest_id;
						if(isset($_SESSION['administrator'])||isset($_SESSION["m$cid"])){
							echo "<td><a href=contest_pr_change.php?cid=$row->contest_id&getkey=".$_SESSION['getkey'].">".($row->private=="0"?"Public->Private":"Private->Public")."</a>";
							echo "<td><a href=contest_df_change.php?cid=$row->contest_id&getkey=".$_SESSION['getkey'].">".($row->defunct=="N"?"<span class=green>Available</span>":"<span class=red>Reserved</span>")."</a>";
							echo "<td><a href=contest_edit.php?cid=$row->contest_id>Edit</a>";
							echo "<td><a href=contest_add.php?cid=$row->contest_id>Copy</a>";
							if(isset($_SESSION['administrator'])){
								echo "<td><a href=\"problem_export_xml.php?cid=$row->contest_id&getkey=".$_SESSION['getkey']."\">Export</a>";
							}else{
							  echo "<td>";
							}
					     echo "<td> <a href=\"../export_contest_code.php?cid=$row->contest_id&getkey=".$_SESSION['getkey']."\">Logs</a>";
						}else{
							echo "<td colspan=5 align=right><a href=contest_add.php?cid=$row->contest_id>Copy</a><td>";
							
						}
							
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