<?php require("admin-header.php");
require_once("../include/set_get_key.php");
if (!isset($_SESSION['administrator'])){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
echo "<title>Problem List</title>";
echo "<center><h2>news List</h2></center>";
$sql="select `news_id`,`user_id`,`title`,`time`,`defunct` FROM `news` order by `news_id` desc";
$result=mysql_query($sql) or die(mysql_error());
echo "<center><table width=90% border=1>";

echo "<tr><td>PID<td>Title<td>Date<td>Status<td>Edit</tr>";
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

echo "</tr></form>";
echo "</table></center>";
require("../oj-footer.php");
?>
