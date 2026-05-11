<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="../../favicon.ico">

	<title>
		<?php echo $OJ_NAME?>
	</title>
	<?php include("template/$OJ_TEMPLATE/css.php");?>


	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

	<div class="container">
		<?php include("template/$OJ_TEMPLATE/nav.php");?>
<?php $show_title="$view_title - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<hr>
<hr>
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
      echo "<td>".$row['user_id']."</td>";
      echo "<td>".$row['nick']."</td>";
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

	</div>
	<!-- /container -->


	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php include("template/$OJ_TEMPLATE/js.php");?>
      <script src="<?php echo $OJ_CDN_URL?>include/sortTable.js"></script>
      <script>
      	  $(document).ready(function(){
	  	console.log("sort");
    		sortTable('statistics', 2, 'int');	
    		sortTable('statistics', 2, 'int');	
    
    	  });
      </script>
</body>
</html>
