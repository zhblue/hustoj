<?php
require("admin-header.php");

//if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])|| isset($_SESSION[$OJ_NAME.'_'.'contest_creator']))) {
//
//	exit(1);
//}

if (isset($OJ_LANG)) {
	require_once("../lang/$OJ_LANG.php");
}
?>

<title>Suspect List</title>
<hr>
<center><h3><?php echo $MSG_IP_VERIFICATION?></h3></center>

<div class='container'>

	<?php
	require_once("../include/set_get_key.php");

	$contest_id = intval($_GET['cid']);

	$sql = "select * from (select count(distinct user_id) c,ip from solution where contest_id=? group by ip) suspect inner join (select distinct ip, user_id, in_date from solution where contest_id=? ) u on suspect.ip=u.ip and suspect.c>1 order by c desc, u.ip, in_date, user_id";

	$result = pdo_query($sql,$contest_id,$contest_id);
	?>

	<div>
		<center>
			<?php echo $MSG_CONTEST_SUSPECT1?>
			<table width=90% border=1 style="text-align:center;">
				<tr>
					<td>IP address</td>
					<td colspan=2>Used ID</td>
          <td>Time</td>
					<td>IP address count</td>
				</tr>

				<?php
				foreach ($result as $row) {
					echo "<tr>";
						echo "<td>".$row['ip']."</td>";
						echo "<td>".$row['user_id']."</td>";
						echo "<td>";
							echo "<a href='../userinfo.php?user=".$row['user_id']."'><sub>".$MSG_USERINFO."</sub></a> <sub>/</sub> ";
							echo "<a href='../status.php?cid=$contest_id&user_id=".$row['user_id']."'><sub>".$MSG_CONTEST." ".$MSG_SUBMIT."</sub></a>";
						echo "</td>";
            echo "<td>".$row['in_date'];
						echo "<td>".$row['c']."</td>";
					echo "</tr>";
				}
				?>

			</table>
		</center>
	</div>

	<br><br>
	
	<?php
	$start = pdo_query("select start_time from contest where contest_id=?",$contest_id)[0][0];
	$end = pdo_query("select end_time from contest where contest_id=?",$contest_id)[0][0];
	$sql = "select * from (select count(distinct ip) c,user_id from loginlog where time>=? and time<=? group by user_id) suspect inner join (select distinct user_id from solution where contest_id=? ) u on suspect.user_id=u.user_id and suspect.c>1 inner join (select distinct ip, user_id, time from loginlog where time>=? and time<=? ) ips on ips.user_id=u.user_id order by c desc, u.user_id, ips.time, ip";
	$result = pdo_query($sql,$start,$end,$contest_id,$start,$end);
	?>

	<div>
		<center>
			<?php echo $MSG_CONTEST_SUSPECT2?>
			<table width=90% border=1 style="text-align:center;">
				<tr>
					<td colspan=2>User ID</td>
					<td>Used IP address</td>
					<td>Time</td>
					<td>IP address count</td>
				</tr>

				<?php
				foreach ($result as $row) {
					echo "<tr>";
						echo "<td>".$row['user_id']."</td>";
						echo "<td>";
							echo "<a href='../userinfo.php?user=".$row['user_id']."'><sub>".$MSG_USERINFO."</sub></a> <sub>/</sub> ";
							echo "<a href='../status.php?cid=$contest_id&user_id=".$row['user_id']."'><sub>".$MSG_CONTEST." ".$MSG_SUBMIT."</sub></a>";
						echo "</td>";
						echo "<td>".$row['ip'];
						echo "<td>".$row['time'];
						echo "<td>".$row['c'];
						echo "</tr>";
				}
				?>

			</table>
		</center>
	</div>

</div>

<?php
require("../oj-footer.php");
?>
