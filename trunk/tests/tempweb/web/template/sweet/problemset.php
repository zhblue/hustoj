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
<!--    --><?php include("template/$OJ_TEMPLATE/css.php");?>


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
<center>
<nav id="page" class="center"><ul class="pagination">
<li class="page-item"><a href="problemset.php?page=1">&lt;&lt;</a></li>
<?php
if(!isset($page)) $page=1;
$page=intval($page);
$section=8;
$start=$page>$section?$page-$section:1;
$end=$page+$section>$view_total_page?$view_total_page:$page+$section;
for ($i=$start;$i<=$end;$i++){
 echo "<li class='".($page==$i?"active ":"")."page-item'>
        <a href='problemset.php?page=".$i."'>".$i."</a></li>";
}
?>
<li class="page-item"><a href="problemset.php?page=<?php echo $view_total_page?>">&gt;&gt;</a></li>
</ul></nav>

<table>
<tr align='center' class='evenrow'><td width='5'></td>
<td  colspan='1'>
<form class=form-inline action=problem.php>
<input class="form-control search-query" type='text' name='id'  placeholder="Problem ID">
<button class="form-control" type='submit' >Go</button></form>
</td>
<td  colspan='1'>
<form class="form-search form-inline">
<input type="text" name=search class="form-control search-query" placeholder="Keywords Title or Source">
<button type="submit" class="form-control"><?php echo $MSG_SEARCH?></button>
</form>
</td></tr>
</table>
<table id='problemset' width='90%'class='table table-striped' lay-filter="demo">
<thead>
<tr class='toprow'>
<th width='5' lay-data="{field:'u1',}"></th>
<th width='20'  class='hidden-xs' lay-data="{field:'u2',}" ><?php echo $MSG_PROBLEM_ID?></th>
<th lay-data="{field:'u3',}"><?php echo $MSG_TITLE?></th>
<th lay-data="{field:'u4',}" class='hidden-xs' width='10%'><?php echo $MSG_SOURCE?></th>
<th lay-data="{field:'u5',}" style="cursor:hand" width=60 ><?php echo $MSG_AC?></th>
<th lay-data="{field:'u6',}" style="cursor:hand" width=60 ><?php echo $MSG_SUBMIT?></th>
</tr>
</thead>
<tbody>
<?php
$cnt=0;
$limit = 0;
foreach($view_problemset as $row){
if ($cnt)
echo "<tr class='oddrow'>";
else
echo "<tr class='evenrow'>";
$i=0;
foreach($row as $table_cell){
	if($i==1||$i==3)echo "<td  class='hidden-xs'>";
	else echo "<td>";
	echo "\t".$table_cell;
	echo "</td>";
	$i++;
}
echo "</tr>";
$cnt=1-$cnt;
$limit++;
}
?>
</tbody>
</table></center>
      </div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>	    
  <script type="text/javascript" src="include/jquery.tablesorter.js"></script>
<script type="text/javascript">
// $(document).ready(function()
// {
// $("#problemset").tablesorter();
// $("#problemset").after($("#page").prop("outerHTML"));
// }
// );
</script>
    <script>
/*
        layui.use('table', function(){
            var table = layui.table;

            //转换静态表格
            table.init('demo', {
                // height: 315 //设置高度
                // ,
                limit: <?php echo $limit ?>//注意：请务必确保 limit 参数（默认：10）是与你服务端限定的数据条数一致
                //支持所有基础参数
            });
            // var element = layui.element;
            //…
        });
*/
    </script>
</body>
</html>
