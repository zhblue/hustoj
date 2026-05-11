<?php	if(stripos($_SERVER['REQUEST_URI'],"template"))exit(); ?>
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
      <br><br>
      <table align=center width=80%>
        <tr align='center'>
          <td>
            <form class=form-inline action=ranklist.php>
              <input class="form-control" name='prefix' value="<?php echo htmlentities(isset($_GET['prefix'])?$_GET['prefix']:"",ENT_QUOTES,"utf-8") ?>" placeholder="<?php echo $MSG_USER?>">
              <button class="form-control" type='submit'><?php echo $MSG_SEARCH?></button>
            </form>
          </td>
        </tr>
      </table>

      <br>
      <table align=right>
        <tr>
          <td>       
            <a href=ranklist.php?scope=d>Day</a>
            <a href=ranklist.php?scope=w>Week</a>
            <a href=ranklist.php?scope=m>Month</a>
            <a href=ranklist.php?scope=y>Year</a>
            &nbsp;
          </td>
        </tr>        
      </table>
      <br>

      <table class="table table-striped content-box-header" align=center width=80%>
        <thead>
          <tr class='toprow'>
            <td class='text-center'><?php echo $MSG_Number?></td>
            <td class='text-center'><?php echo $MSG_USER?></td>
            <td class='text-center'><?php echo $MSG_NICK?></td>
            <td class='text-center'><?php echo $MSG_GROUP_NAME?></td>
            <td class='text-center'><?php echo $MSG_SOVLED?></td>
            <td class='text-center'><?php echo $MSG_SUBMIT?></td>
            <td class='text-center'><?php echo $MSG_RATIO?></td>
          </tr>
        </thead>
        <tbody>
          <?php
          $cnt=0;
          foreach($view_rank as $row){
            if ($cnt)
              echo "<tr class='oddrow'>";
            else
              echo "<tr class='evenrow'>";

            $i = 0;
            foreach($row as $table_cell){
              echo "<td class='text-center'>";
              echo $table_cell;
              echo "</td>";
              $i++;
            }
            echo "</tr>";
            $cnt=1-$cnt;
          }
          ?>
        </tbody>
      </table>

      <?php
      echo "<center>";
      $qs="";
      if(isset($_GET['prefix'])){
        $qs.="&prefix=".htmlentities($_GET['prefix'],ENT_QUOTES,"utf-8");
      }
      if(isset($scope)){
        $qs.="&scope=".htmlentities($scope,ENT_QUOTES,"utf-8");
      }

      for($i = 0; $i <$view_total ; $i += $page_size) {
        echo "<a href='./ranklist.php?start=" . strval ( $i ).$qs. "'>";
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
