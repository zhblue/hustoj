<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title><?php echo $OJ_NAME?></title>  
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
      <!-- Main component for a primary marketing message or call to action -->
      <div class="jumbotron">
<center><h3>Contest Statistics</h3>
<table id=cs width=90%>
<thead>
<tr class=toprow><th><th>AC<th>PE<th>WA<th>TLE<th>MLE<th>OLE<th>RE<th>CE<th><th>TR<th>Total
<?php 
  $i=0;
  foreach ($language_name as $lang){
	if(isset($R[$pid_cnt][$i+11]))	
		echo "<th class='center'>$language_name[$i]</th>";
	else
		echo "<th>";
	$i++;
  }


?>

</tr>
</thead>
<tbody>
<?php
for ($i=0;$i<$pid_cnt;$i++){
if(!isset($PID[$i])) $PID[$i]="";
if ($i&1)
echo "<tr align=center class=oddrow><td>";
else
echo "<tr align=center class=evenrow><td>";
echo "<a href='problem.php?cid=$cid&pid=$i'>$PID[$i]</a>";
for ($j=0;$j<count($language_name)+11;$j++) {
if(!isset($R[$i][$j])) $R[$i][$j]="";
echo "<td>".$R[$i][$j];
}
echo "</tr>";
}
echo "<tr align=center class=evenrow><td>Total";
for ($j=0;$j<count($language_name)+11;$j++) {
if(!isset($R[$i][$j])) $R[$i][$j]="";
echo "<td>".$R[$i][$j];
}
echo "</tr>";
?>
</tbody>
<table>
<div id=submission style="width:600px;height:300px" ></div>
</center>

      </div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>	    
<script type="text/javascript" src="include/jquery.tablesorter.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
$("#cs").tablesorter();
}
);
</script>

<script language="javascript" type="text/javascript" src="include/jquery.flot.js"></script>
<script type="text/javascript">
$(function () {
var d1 = [];
var d2 = [];
<?php
foreach($chart_data_all as $k=>$d){
?>
d1.push([<?php echo $k?>, <?php echo $d?>]);
<?php }?>
<?php
foreach($chart_data_ac as $k=>$d){
?>
d2.push([<?php echo $k?>, <?php echo $d?>]);
<?php }?>
//var d2 = [[0, 3], [4, 8], [8, 5], [9, 13]];
// a null signifies separate line segments
var d3 = [[0, 12], [7, 12], null, [7, 2.5], [12, 2.5]];
$.plot($("#submission"), [
{label:"<?php echo $MSG_SUBMIT?>",data:d1,lines: { show: true }},
{label:"<?php echo $MSG_AC?>",data:d2,bars:{show:true}} ],{
xaxis: {
mode: "time"
//, max:(new Date()).getTime()
//,min:(new Date()).getTime()-100*24*3600*1000
},
grid: {
backgroundColor: { colors: ["#fff", "#333"] }
}
});
});
//alert((new Date()).getTime());
</script>
  </body>
</html>
