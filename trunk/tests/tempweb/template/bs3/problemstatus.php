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

			<center>
			<h3><?php echo $MSG_PROBLEM." : ".$id." ".$MSG_STATISTICS?></h3>
				<hr>
				<table align=center width=70%>
					<tr>
						<td>

							<table id="statics" class="table-hover table-striped" align=center width=90%>
								<?php
								$cnt = 0;
								foreach ($view_problem as $row) {
									if ($cnt)
										echo "<tr class='oddrow'>";
									else
										echo "<tr class='evenrow'>";
									$i=1;
									foreach ($row as $table_cell) {
										if ($i==1)
											echo "<td class='text-center'>";
										else
											echo "<td class='text-right'>";

										echo $table_cell;
										echo "</td>";
										$i++;
									}
									echo "</tr>";
									$cnt = 1-$cnt;
								}
								?>

								<tr id=pie bgcolor=white>
									<td colspan=2>
										<center>
											<div id='PieDiv' style='position:relative; height:150px; width:200px;'></div>
										</center>
									</td>
								</tr>
							</table>

							<br>

							<?php if (isset($view_recommand)) {?>
							<table id=recommand class="table-hover table-striped" align=center width=90%>
								<tr>
									<td class='text-center'>
										Recommanded Next Problem
										<br>
										<?php
										$cnt = 1;
										foreach ($view_recommand as $row) {
											echo "<a href=problem.php?id=$row[0]>$row[0]</a>&nbsp;";
											if ($cnt%5 == 0)
												echo "<br>";
											$cnt++;
										}
										?>
									</td>
								</tr>
							</table>
							<?php }?>

						</td>

						<td>
							<table id=problemstatus class="table-hover table-striped" align=center width=100%>
								<thead>
									<tr class=toprow>
										<th style="cursor:hand" onclick="sortTable('problemstatus', 0, 'int');" class="text-center" width=10%>
											<?php echo $MSG_Number?>
										</th>
										<th class="text-center" width=10%>
											<?php echo $MSG_RUNID?>
										</th>
										<th class="text-center" width=15%>
											<?php echo $MSG_USER?>
										</th>
										<th class="text-center" width=10%>
											<?php echo $MSG_MEMORY?>
										</th>
										<th class="text-center" width=10%>
											<?php echo $MSG_TIME?>
										</th>
										<th class="text-center" width=10%>
											<?php echo $MSG_LANG?>
										</th>
										<th class="text-center" width=10%>
											<?php echo $MSG_CODE_LENGTH?>
										</th>
										<th class="text-center" width=20%>
											<?php echo $MSG_SUBMIT_TIME?>
										</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$cnt = 0;
									foreach ($view_solution as $row) {
										if ( $cnt )
											echo "<tr class='oddrow'>";
										else
											echo "<tr class='evenrow'>";
										
										$i = 1;
										foreach ($row as $table_cell) {
											if ($i==1 || $i==8)
												echo "<td class='text-center'>";
											else if ($i==2 || $i==4 || $i==5 || $i==6  || $i==7)
												echo "<td class='text-right'>";
											else
												echo "<td>";

											echo $table_cell;
											echo "&nbsp";
											echo "</td>";
											$i++;
										}

										echo "</tr>";
										$cnt = 1-$cnt;
									}
									?>
								</tbody>
							</table>

							<br>

							<center>
							<?php
							echo "<a href='problemstatus.php?id=$id'>[TOP]</a>";
							//echo "&nbsp;&nbsp;<a href='status.php?problem_id=$id'>[STATUS]</a>";
							
							if ($page>$pagemin) {
								$page--;
								echo "&nbsp;&nbsp;<a href='problemstatus.php?id=$id&page=$page'>[PREV]</a>";
								$page++;
							}

							if ($page<$pagemax) {
								$page++;
								echo "&nbsp;&nbsp;<a href='problemstatus.php?id=$id&page=$page'>[NEXT]</a>";
								$page--;
							}
							?>
						  </center>

						</td>
					</tr>
				</table>
			</center>

				<script type="text/javascript" src="include/wz_jsgraphics.js"></script>
				<script type="text/javascript" src="include/pie.js"></script>
				<script language="javascript">
					var y = new Array();
					var x = new Array();
					var dt = document.getElementById("statics");
					var data = dt.rows;
					var n;
					for (var i=3; dt.rows[i].id!="pie"; i++) {
						x.push(dt.rows[i].cells[0].innerHTML);
						n = dt.rows[i].cells[1];
						n = n.innerText || n.textContent;
						//alert(n);
						n = parseInt(n);
						y.push(n);
					}
					var mypie = new Pie("PieDiv");
					mypie.drawPie(y, x);
					//mypie.clearPie();
				</script>
			
			</div>
		</div>

		<!-- /container -->

		<!-- Bootstrap core JavaScript
    ================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<?php include("template/$OJ_TEMPLATE/js.php");?>
		<script type="text/javascript" src="include/jquery.tablesorter.js"></script>
		<script type="text/javascript">
			$( document ).ready( function () {
				$( "#problemstatus" ).tablesorter();
			} );
		</script>
	</body>
</html>