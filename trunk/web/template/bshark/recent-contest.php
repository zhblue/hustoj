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
<div id="main" style="margin: 10px;">
    <div class="padding">

    <table class="ui very basic center aligned table">
      <thead>
        <tr>
        <th>OJ</th>
        <th><?php echo $MSG_CONTEST_NAME ?></th>
        <th><?php echo $MSG_START_TIME ?></th>
        <th>星期</th>
        <th><?php echo $MSG_CONTEST_OPEN ?></th>
        </tr>
      </thead>
        <tbody id="contest-list">
<?php foreach ($contests as $val): ?>
        <tr>
          <td class="column-1"><?php echo rc_h($val['oj'] ?? ''); ?></td>
          <td class="column-2"><a target="_blank" href="<?php echo rc_href($val['link'] ?? ''); ?>"><?php echo rc_h($val['name'] ?? ''); ?></a></td>
          <td class="column-3"><?php echo rc_h($val['start_time'] ?? ''); ?></td>
          <td class="column-4"><?php echo rc_h($val['week'] ?? ''); ?></td>
          <td class="column-5"><?php echo rc_h($val['access'] ?? ''); ?></td>
        </tr>
<?php endforeach; ?>
<?php if (empty($contests)): ?>
        <tr><td colspan="5" class="column-err" style="color:#bc5042"><?php echo rc_h($fetchError); ?><a href="<?php echo $jsonUrl; ?>" target="_blank" style="color:inherit;text-decoration:underline"><?php echo $jsonUrl; ?></a></td></tr>
<?php endif; ?>
        </tbody>
    </table>
    <div>数据来源：<a href="https://algcontest.rainng.com/contests.json" target="_blank">https://algcontest.rainng.com/contests.json</a>&nbsp;&nbsp;&nbsp;&nbsp;作者：<a href="https://www.rainng.com/"  target="_blank" >Azure99</a></div>
    </div>
    </div>
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
		});
	</script>		
  </body>
</html>
