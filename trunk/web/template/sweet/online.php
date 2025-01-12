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
		<h3>current online user: <?php echo $on->get_num()?></h3>
		<table style="margin:auto;width:98%">
		<thead>
		<tr class=toprow><th style="width: 50px">ip</th><th>uri</th><th>refer</th><th style="width:100px">stay time</th><th>user agent</th></tr>
		</thead>
		<tbody>
		<?php 
		foreach($users as $u){
				 if(is_array($u)){
				 ?>
				<tr><td class="ip">
				<?php $l = $ip->getlocation($u['ip']);
                                echo $u['ip'].'<br />';
                                if(!empty($l)) echo $l;  ?>
				</td><td><?php echo $u->uri?></td><td><?php echo $u['refer']?></td>
				<td class="time"><?php echo sprintf("%dmin %dsec",($u['lastmove']-$u['firsttime'])/60,($u['lastmove']-$u['firsttime']) % 60)?></td><td><?php echo $u['ua']?></td></tr>
				<?php 
				}
		}
		
		if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])){
		
			echo "<tr><td width='100%' colspan='5'><form>IP<input type='text' name='search'><input type='submit' value='$MSG_SEARCH' ></form></td></tr>";
	  
			
			?>
			</tbody>
			</table>
			<table>
			<tbody>
         <tr  class=toprow align=center ><td>UserID<td>Password<td>IP<td>Time</tr>
				<?php 
				$cnt=0;
				foreach($view_online as $row){
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
		<?php
		}
		?>
		</table>
		</center>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>	    
  </body>
</html>
