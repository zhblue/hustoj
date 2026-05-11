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
<!--    --><?php //include("template/$OJ_TEMPLATE/css.php");?><!--	    -->


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
        <div style="height: 20px;">

        </div>
      <div class="layui-container">
        <p>

            <?php
            if (!$result){
                $my_category= "<h3>No Category Now!</h3>";
            }else{
                $my_category.= "<div class=\"layui-btn-container\">";

                foreach ($category as $cat){
                    if(trim($cat)=="") continue;
                    $my_category.= "<button class=\"layui-btn\"><a class='btn btn-primary' href='problemset.php?search=".htmlentities($cat,ENT_QUOTES,'UTF-8')."'>".$cat."</a></button>";
                }

                $my_category.= "</div>";
            }

            echo $my_category
            ?>

        </p>
      </div>

    </div> <!-- /container -->




    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>	    
  </body>
</html>
