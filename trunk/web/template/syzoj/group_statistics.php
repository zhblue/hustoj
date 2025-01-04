<?php $show_title="$view_title - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<hr>
<div class='padding'>
<div ><h1><?php echo $MSG_GROUP_NAME.$MSG_STATISTICS ?></h1>
<form action=group_statistics.php method=get ><?php echo $MSG_GROUP_NAME ?>
	<select name=group_name onchange="$('form').submit()">
		<?php
			if(!isset($_GET['group_name'])) echo "<option value='' />";
			$groups=pdo_query("select distinct group_name from users");
			$groups=array_column($groups,'group_name');
			foreach($groups as $group){
				echo "<option value='".htmlentities($group)."' ". ($group==$_GET['group_name']?"selected":"") ."   >$group</option>";
			
			}
		?>
	</select>
	<input type=hidden name=list value='<?php echo $pids ?>' >
</form>
</div>
<?php
if(!empty($result)){
?>

<center>
  <table id="statistics"  width=100% border=1 style="text-align:center;" class="ui table striped">
      <thead>
    <tr>
    <?php
      echo "<th>$MSG_USER_ID</th>";
      echo "<th>$MSG_NICK</th>";
      echo "<th>$MSG_AC</th>";
  	foreach($pida as $pid){
      		echo "<th class='pid' value='$pid'><a href='problem.php?id=$pid' target=_blank >".$ptitle[$pid]."</a>";
      		echo "</th>";
	}
 // var_dump($result);
?> 
   </tr>
      </thead>
      <tbody>
<?php
    foreach($result as $row){
      echo "<tr>";
      echo "<td><a href='userinfo.php?user=".htmlentities($row['user_id'])."'>".$row['user_id']."</a></td>";
      echo "<td><a href='userinfo.php?user=".htmlentities($row['user_id'])."' target='_blank'>".$row['nick']."</a></td>";
        $ac=0;
  	foreach($pida as $pid){
      		if($row["P$pid"]==4) $ac++;
	}
      	echo "<td>$ac</td>";
  	foreach($pida as $pid){
      		echo "<td>";
      		if($row["P$pid"]==4) echo "<span class='label label-success label-sm' >AC</span>";
		else if($row["P$pid"]==15) echo "&nbsp;";
		else echo "<span class='label label-danger label-sm' >WA</span>";
      		echo "</td>";
	}
      echo "</tr>";
    }
  ?>
      </tbody>
</table>
<?php } ?>
<a href="javascript:history.go(-1);" >Back</a>
</center>
<?php include("template/$OJ_TEMPLATE/footer.php");?>
<script src="<?php echo $OJ_CDN_URL?>include/sortTable.js"></script>
      <script>
      	  $(document).ready(function(){
	  	console.log("sort");
    		sortTable('statistics', 2, 'int');	
    		sortTable('statistics', 2, 'int');	
    
    	  });
      </script>