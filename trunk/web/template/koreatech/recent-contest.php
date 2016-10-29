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
	<table width=80% align=center>
<thead class=toprow>
	<tr>
		<th class="column-1">OJ</th><th class="column-2">Name</th><th class="column-3">Start Time</th><th class="column-4">Week</th><th class="column-5">Access</th>
	</tr>
</thead>
<tbody class="row-hover">
<?php
$odd=true;
 foreach($rows as $row) {
   $odd=!$odd;
?>
  <tr class="<?php echo $odd?"oddrow":"evenrow"  ?>">
		<td class="column-1"><?php echo$row['oj']?></td><td class="column-2"><a id="name_<?php echo$row['id']?>" href="<?php echo$row['link']?>" target="_blank"><?php echo$row['name']?></a></td><td class="column-3"><?php echo$row['start_time']?></td><td class="column-4"><?php echo$row['week']?></td><td class="column-5"><?php echo$row['access']?></td>
	</tr>
<?php } ?>
</tbody>
</table>
</div>
<div align=center>DataSource:http://contests.acmicpc.info/contests.json  Spider Author:<a href="http://contests.acmicpc.info" >doraemonok</a></div>

<div id=foot>
	<?php require_once("oj-footer.php");?>

</div><!--end foot-->
</div><!--end main-->
</div><!--end wrapper-->
	<?php require_once("include-bottom.php");?>
</body>
</html>
