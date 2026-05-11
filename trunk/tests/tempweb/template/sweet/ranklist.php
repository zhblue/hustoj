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
<!--    --><?php include("template/$OJ_TEMPLATE/css.php");?><!--	    -->


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
          <tr><td colspan=3 align=left >
                  <form class="form-inline" action="ranklist.php">
                      <?php echo $MSG_USER?><input class="form-control" name="prefix" value="<?php echo htmlentities(isset($_GET['prefix'])?$_GET['prefix']:"",ENT_QUOTES,"utf-8") ?>" >
                      <input type=submit class="form-control" value=Search >
                  </form></td><td colspan=3 align=right>
                  <a href=ranklist.php?scope=d>Day</a>
                  <a href=ranklist.php?scope=w>Week</a>
                  <a href=ranklist.php?scope=m>Month</a>
                  <a href=ranklist.php?scope=y>Year</a>
              </td></tr>
          <table align=center width=90% lay-filter="demo">
<thead>
<tr class='toprow'>
    <th width=5% align=center lay-data="{field:'u1',}"><b><?php echo $MSG_Number?></b></th>
    <th width=10% align=center lay-data="{field:'u2',}"><b><?php echo $MSG_USER?></b></th>
    <th width=55% align=center lay-data="{field:'u3',}"><b><?php echo $MSG_NICK?></b></th>
    <th width=10% align=center lay-data="{field:'u4',}"><b><?php echo $MSG_AC?></b></th>
    <th width=10% align=center lay-data="{field:'u5',}"><b><?php echo $MSG_SUBMIT?></b></th>
    <th width=10% align=center lay-data="{field:'u6',}"><b><?php echo $MSG_RATIO?></b></th>
</tr>
</thead>
<tbody>
<?php
$cnt=0;
$limit = 0;
foreach($view_rank as $row){
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
$limit++;
}
?>
</tbody>
</table>
<?php
echo "<center>";
for($i = 0; $i <$view_total ; $i += $page_size) {
echo "<a href='./ranklist.php?start=" . strval ( $i ).($scope?"&scope=$scope":"") . "'>";
echo strval ( $i + 1 );
echo "-";
echo strval ( $i + $page_size );
echo "</a>&nbsp;";
if ($i % 250 == 200)
echo "<br>";
}
echo "</center>";
?>
      </div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>	    
  </body>
</html>
