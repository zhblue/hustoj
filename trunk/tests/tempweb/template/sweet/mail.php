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
	<center>
<?php
if($view_content)
echo "<center>
<table>
<tr>
<td class=blue>$to_user:".htmlentities(str_replace("\n\r","\n",$view_title),ENT_QUOTES,"UTF-8")." </td>
</tr>
<tr><td><pre>". htmlentities(str_replace("\n\r","\n",$view_content),ENT_QUOTES,"UTF-8")."</pre>
</td></tr>
</table></center>";
?>
<table><form method=post action=mail.php>
<tr>
<td> To:<input name=to_user size=10 value="<?php echo $to_user?>">
Title:<input name=title size=20 value="<?php echo $title?>">
<input type=submit value=<?php echo $MSG_SUBMIT?>></td>
</tr>
<tr><td>
<textarea name=content rows=10 cols=80 class="input input-xxlarge"></textarea>
</td></tr>
</form>
</table>
<table border=1>
<tr><td>Mail ID<td>From:Title<td>Date</tr>
<tbody>
<?php
$cnt=0;
foreach($view_mail as $row){
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
}
?>
</tbody>
</table>
</center> 
      </div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>	    
  </body>
</html>
