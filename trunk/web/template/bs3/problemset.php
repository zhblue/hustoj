<?php 
if(!isset($_GET['ajax'])){

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
				<nav id="page" class="center">
					<small>
					<ul class="pagination">
						<li class="page-item"><a href="problemset.php?page=1">&lt;&lt;</a>
						</li>
						<?php
						if ( !isset( $page ) )$page = 1;
						$page = intval( $page );
						$section = 8;
						$start = $page > $section ? $page - $section : 1;
						$end = $page + $section > $view_total_page ? $view_total_page : $page + $section;
						for ( $i = $start; $i <= $end; $i++ ) {
							echo "<li class='" . ( $page == $i ? "active " : "" ) . "page-item'> <a href='problemset.php?page=" . $i .htmlentities($postfix,ENT_QUOTES,'UTF-8'). "'>" . $i . "</a></li>";
						}
						?>
						<li class="page-item"><a href="problemset.php?page=<?php echo $view_total_page?>">&gt;&gt;</a>
						</li>
					</ul>
					</small>
				</nav>

				<table>
					<tr align='center' class='evenrow'>
						<td width='5'></td>
						<td colspan='1'>
							<form class=form-inline action=problem.php>
								<input class="form-control search-query" type='text' name='id' placeholder="<?php echo $MSG_PROBLEM_ID?>">
								<button class="form-control" type='submit'><?php echo $MSG_SEARCH?></button>&nbsp;&nbsp;
							</form>
						</td>
						<td colspan='1'>
							<form class="form-search form-inline">
								<input type="text" name=search class="form-control search-query" placeholder="<?php echo $MSG_TITLE.', '.$MSG_SOURCE?>">
								<button type="submit" class="form-control"><?php echo $MSG_SEARCH?></button>
							</form>
						</td>
					</tr>
				</table>
<?php } ?>
				<table id='problemset' width='90%' class='table table-striped'>
					<thead>
						<tr class='toprow'>
							<td></td>
							<td class='hidden-xs center'>
								<?php echo $MSG_PROBLEM_ID?>
							</td>
							<td class='center'>
								<?php echo $MSG_TITLE?>
							</td>
							<td class='hidden-xs center'>
								<?php echo $MSG_SOURCE?>
							</td>
							<td style="cursor:hand" class='center'>
								<?php echo $MSG_SOVLED?>
							</td>
							<td style="cursor:hand" class='center'>
								<?php echo $MSG_SUBMIT?>
							</td>
						</tr>
					</thead>
					<tbody>
						<?php
						$cnt = 0;
						foreach ( $view_problemset as $row ) {
							if ( $cnt )
								echo "<tr class='oddrow'>";
							else
								echo "<tr class='evenrow'>";
							$i = 0;
							foreach ( $row as $table_cell ) {
								if ( $i == 1 || $i == 3 )echo "<td  class='hidden-xs'>";
								else echo "<td>";
								echo "\t" . $table_cell;
								echo "</td>";
								$i++;
							}
							echo "</tr>";
							$cnt = 1 - $cnt;
						}
						?>
					</tbody>
				</table>
<?php 
if(!isset($_GET['ajax'])){

?>
			</center>
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
			$( "#problemset" ).tablesorter();
			$( "#problemset" ).after( $( "#page" ).prop( "outerHTML" ) );
		} );
	</script>
</body>
</html>
<?php } ?>
