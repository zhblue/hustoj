<?php $show_title="$MSG_HELP_ONLINE - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="padding">
	<?php	
		if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])){
	?>
  <form class="ui mini form" role="form" style="margin-bottom: 25px; text-align: right; ">
    <div class="ui action left icon input inline" style="width: 180px; margin-right: 77px; ">
      <i class="search icon"></i><input name="search" placeholder="IP" type="text">
      <button class="ui mini button" type="submit"><?php echo $MSG_SEARCH ?></button>
    </div>
  </form>
	<?php
		}
	?>

	    <table class="ui very basic center aligned table" style="table-layout: fixed; ">
	        <thead>
	        <tr>
			<th>ip</th>
			<th>uri</th>
			<th>refer</th>
			<th>stay time</th>
			<th>user agent</th>
	        </tr>
	        </thead>
	        <tbody>

		<?php 
		foreach($users as $u){
			 if(!empty($u)){
		?>
		<tr>
			<td><?php $l = $ip->getlocation($u['ip']);
				echo $u['ip'].'<br />';
				if(strlen(trim($l['area']))==0)
					echo $l['country'];
				else
					echo $l['area'].'@'.$l['country'];
		            ?>
			</td>
			<td><?php echo $u['uri']?></td>
			<td><?php echo $u['refer']?></td>
			<td><?php echo sprintf("%dmin %dsec",($u['lastmove']-$u['firsttime'])/60,($u['lastmove']-$u['firsttime']) % 60)?></td>
			<td><?php echo $u['ua']?></td>
		</tr>
		<?php 
				}
			}
		?>	

	        </tbody>
	    </table>



	    <table class="ui very basic center aligned table" style="table-layout: fixed; ">
	        <thead>
	        <tr>
         		<th>UserID</th>
			<th>Password</th>
			<th>IP</th>
			<th>Time</th>
	        </tr>
	        </thead>
	        <tbody>
			<?php 
				foreach($view_online as $row){
					echo "<tr>";
					foreach($row as $table_cell){
						echo "<td>";
						echo "\t".$table_cell;
						echo "</td>";
					}
					echo "</tr>";
				}
			?>

	        </tbody>
	    </table>


</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
