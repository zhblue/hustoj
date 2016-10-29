<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?php echo $view_title?></title>
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
</head>
<body>
<?php 
  $navigation_tab = "problem";
  require_once("oj-header.php");
?>
<div class="container">
<?php
  $problem_tab = "problemstatus";
  require_once("./template/$OJ_TEMPLATE/problem-header.php");
?>
  <br>
	<table class='table'><tr><td>
	<table id=statics class='table'>
<?php 
	$cnt=0;
	foreach($view_problem as $row){
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
	<tr id=pie bgcolor=white><td colspan=2><div id='PieDiv' style='position:relative;height:150px;width:200px;'></div></tr>
	</table><br>
	<?php if(isset($view_recommand)){?>
	<table id=recommand class='table'><tr><td>
			Recommanded Next Problem<br>
<?php 
	$cnt=1;
	foreach($view_recommand as $row){
		echo "<a href=problem.php?id=$row[0]>$row[0]</a>&nbsp;";
		if($cnt%3==0) echo "<br>";
		$cnt++;
	}
?>
		</td></tr>
	</table>
	<?php }?>
	</td><td>
	<table id=problemstatus class='table'><thead>
		<tr class=toprow><th style="cursor:hand" onclick="sortTable('problemstatus', 0, 'int');"><?php echo $MSG_Number?>
			<th>RunID
			<th><?php echo $MSG_USER?>
			<th ><?php echo $MSG_MEMORY?>
			<th  ><?php echo $MSG_TIME?>
			<th><?php echo $MSG_LANG?>
			<th ><?php echo $MSG_CODE_LENGTH?>
			<th><?php echo $MSG_SUBMIT_TIME?></tr></thead><tbody>
			<?php 
			$cnt=0;
			foreach($view_solution as $row){
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
	</table>
	<?php
	echo "<a href='problemstatus.php?id=$id'>[TOP]</a>";
	echo "&nbsp;&nbsp;<a href='status.php?problem_id=$id'>[STATUS]</a>";
	if ($page>$pagemin){
		$page--;
		echo "&nbsp;&nbsp;<a href='problemstatus.php?id=$id&page=$page'>[PREV]</a>";
		$page++;
	}
	if ($page<$pagemax){
		$page++;
		echo "&nbsp;&nbsp;<a href='problemstatus.php?id=$id&page=$page'>[NEXT]</a>";
		$page--;
	}
	
	?>
	</table>

</div>
<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
<script type="text/javascript" src="include/wz_jsgraphics.js"></script>
<script type="text/javascript" src="include/pie.js"></script>
<script type="text/javascript" src="include/jquery.tablesorter.js"></script>
<script type="text/javascript">
  $(document).ready(function()
    {
        $("#problemstatus").tablesorter();
    }
  );
	var y= new Array ();
	var x = new Array ();
	var dt=document.getElementById("statics");
	var data=dt.rows;
	var n;

	for(var i=3;dt.rows[i].id!="pie";i++){
			x.push(dt.rows[i].cells[0].innerHTML);
			n=dt.rows[i].cells[1];
			n=n.innerText || n.textContent;
			//alert(n);
			n=parseInt(n);
			y.push(n);
	}
	var mypie=  new Pie("PieDiv");
	mypie.drawPie(y,x);
	//mypie.clearPie();
</script>

</body>
</html>
