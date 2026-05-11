<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="refresh" content="10; url='printer.php'">
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
	 <form action="printer.php" method="post"  onsubmit="return confirm('Delete All Tasks?');">
                <input type="hidden" name="clean" >
                <input type="submit" class='btn btn-danger' value="Clean">
		<?php require_once(dirname(__FILE__)."/../../include/set_post_key.php")?>
        </form>

	<table class="table table-striped content-box-header">
<tr><td>id<td><?php echo $MSG_USER_ID?><td><?php echo $MSG_STATUS?><td></tr>
<?php
foreach($view_printer as $row){
	echo "<tr>\n";
	foreach($row as $table_cell){
		echo "<td>";
		echo $table_cell;
		echo "</td>";
	}
		$i++;
	echo "</tr>\n";
}
?>
</table>

        <p>
        </p>
      </div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>	    
  </body>
</html>
