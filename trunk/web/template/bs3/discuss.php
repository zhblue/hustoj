<!-- /web/template/bs3/discuss.php -->

<?php $show_title="$MSG_BBS - $OJ_NAME"; ?>

<?php 
$view_discuss=ob_get_contents();
ob_end_clean();
require_once(dirname(__FILE__)."/../../lang/$OJ_LANG.php");
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
      <?php include("include/bbcode.php");?>
      <div class="padding">
        <h1><?php echo $news_title ?></h1>
        <div class="ui existing segment">
          <?php echo $view_discuss?>
        </div>
      </div>
    </div>
  </div>

  <!-- /container -->


  <!-- Bootstrap core JavaScript
  ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <?php include("template/$OJ_TEMPLATE/js.php");?>

</body>
</html>
