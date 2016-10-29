<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $view_title?></title>
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
</head>
<body>
<div id="wrapper">
	<?php require_once("oj-header.php");?>
<div id=main>
	<center>
		<h3>current online user: <?php echo $on->get_num()?></h3>
		<table style="margin:auto;width:98%">
		<thead>
		<tr class=toprow><th style="width: 50px">ip</th><th>uri</th><th>refer</th><th style="width:100px">stay time</th><th>user agent</th></tr>
		</thead>
		<tbody>
		<?php 
		foreach($users as $u):
				 if(is_object($u)){
				 ?>
				<tr><td class="ip">
				<?php $l = $ip->getlocation($u->ip);
				   
					echo $u->ip.'<br />';
					if(strlen(trim($l['area']))==0)
						echo $l['country'];
					else
						echo $l['area'].'@'.$l['country'];
					?></td><td><?php echo $u->uri?></td><td><?php echo $u->refer?></td>
				<td class="time"><?php echo sprintf("%dmin %dsec",($u->lastmove-$u->firsttime)/60,($u->lastmove-$u->firsttime) % 60)?></td><td><?php echo $u->ua?></td></tr>
				<?php 
				}
		endforeach;
		
		if(isset($_SESSION['administrator'])){
		
			echo "<tr><td width='100%' colspan='5'><form>IP<input type='text' name='search'><input type='submit' value='$MSG_SEARCH' ></form></td></tr>";
	  
			
			?>
			</tbody>
			</table>
			<table>
			<tbody>
         <tr  class=toprow align=center ><td>UserID<td>Password<td>IP<td>Time</tr>
				<?php 
				$cnt=0;
				foreach($view_online as $row){
					if ($cnt) 
						echo "<tr class='oddrow'>";
					else
						echo "<tr class='evenrow'>";
					foreach($row as $table_cell){
						echo "<td>";
						echo "\t".$table_cell;
						echo "</td>";
					}
					
					echo "</tr>";
					
					$cnt=1-$cnt;
				}
				?>
				</tbody>
		<?php
		}
		?>
		</table>
		</center>
<div id=foot>
	<?php require_once("oj-footer.php");?>

</div><!--end foot-->
</div><!--end main-->
</div><!--end wrapper-->
	<?php require_once("include-bottom.php");?>
</body>
</html>
