<?php
$cur_path = "template/$OJ_TEMPLATE/"
?>
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
<div id=main>

<!--
	<table lay-filter="demo">
<thead class=toprow>
	<tr>
		<th lay-data="{field:'username',}">OJ</th><th lay-data="{field:'username1',}">Name</th><th class="column-3" lay-data="{field:'username2', width:100}">Start Time</th><th class="column-4" lay-data="{field:'username3', width:100}">Week</th><th class="column-5" lay-data="{field:'username4', width:100}">Access</th>
	</tr>
</thead>
<tbody class="row-hover">
<?php
//echo $json;
$odd=true;
$limit = 0;
 foreach($rows as $row) {
   $odd=!$odd;
?>
  <tr>
		<td><?php echo$row['oj']?></td><td class="column-2"><a id="name_<?php echo$row['id']?>" href="<?php echo$row['link']?>" target="_blank"><?php echo$row['name']?></a></td><td class="column-3"><?php echo$row['start_time']?></td><td class="column-4"><?php echo$row['week']?></td><td class="column-5"><?php echo$row['access']?></td>
  </tr>
<?php
 $limit++;
 } ?>
</tbody>
</table>
-->

<table lay-filter="demo">
<thead class=toprow>
	<tr>
		<th lay-data="{field:'username',}">OJ</th>
		<th lay-data="{field:'username1',}">Name</th>
		<th class="column-3" lay-data="{field:'username2', width:100}">Start Time</th>
		<th class="column-4" lay-data="{field:'username3', width:100}">Week</th>
		<th class="column-5" lay-data="{field:'username4', width:100}">Access</th>
	</tr>
</thead>
<tbody class="row-hover" id="contest-list"></tbody>

</table>
</div>
  
<div align=center>DataSource:http://algcontest.rainng.com/contests.json  Spider Author:<a href="https://github.com/Azure99/AlgContestInfo" >Azure99</a></div>

      </div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>
	<script>
		var contestList = $("#contest-list");
		$.get("https://algcontest.rainng.com/contests.json",function(response){
			response.map(function(val){
				var item = "<tr><td class='column-1'>"+val.oj+"</td>"+
					"<td class='column-2'><a target='_blank' href='"+val.link+"'>"+val.name+"</a></td>"+
					"<td class='column-3'>"+val.start_time+"</td>"+
					"<td class='column-4'>"+val.week+"</td>"+
					"<td class='column-5'>"+val.access+"</td></tr>"
				contestList.append(item);
			});
			layui.use('table', function(){
				var table = layui.table;
				table.init('demo', {
					limit: response.length
				});
			});
		});

  </script>	
  </body>
</html>
