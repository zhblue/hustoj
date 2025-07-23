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
		<!-- Main component for a primary marketing message or call to action -->
		<div class="jumbotron">
			<p>
				<center> Recent submission :
					<?php echo $speed?> .
					<div id=submission style="width:80%;height:300px"></div>
				</center>
			</p>
			<?php echo $view_news?>
		</div>

	</div>
	<!-- /container -->


	<!-- Bootstrap core JavaScript
    ================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<?php include("template/$OJ_TEMPLATE/js.php");?>
	<script  src="<?php echo $OJ_CDN_URL.$path_fix."template/syzoj"?>/js/echarts.min.js"></script>
	<script type="text/javascript">
			$( function () {
			var all=<?php echo json_encode(array_column($chart_data_all,1))?> ;
			var sub_echarts= echarts.init( $( "#submission" )[0]);
			var maxY=Math.max(all);
			var option = {
			tooltip: {
				trigger: 'axis',
				formatter: '{b0}({a0}): {c0}<br />{b1}({a1}): {c1}'
			},
			legend: {
			data: ['<?php echo $MSG_SUBMIT?>','<?php echo $MSG_AC?>' ]
			},
			xAxis: {
			data: <?php echo json_encode(array_column($chart_data_ac,0))?> 
			,
			inverse:true
			},
			yAxis: [
				{
					type: 'value',
					name: '<?php echo $MSG_SUBMIT?>'
				}
			],
			series: [
			{
				name: '<?php echo $MSG_SUBMIT?>',
				type: 'bar',
				data: all
				}
			,
			{
				name: '<?php echo $MSG_AC?>',
				type: 'bar',
				data: <?php echo json_encode(array_column($chart_data_ac,1))?> 
			}]
			};
			sub_echarts.setOption(option);


                } );
                //alert((new Date()).getTime());
        </script>
</body>
</html>
