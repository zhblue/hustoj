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

      <form action="modify.php" method="post" role="form" class="form-horizontal">
        <div class="form-group">
          <label class="col-sm-4 control-label"><?php echo $MSG_REG_INFO?></label>
        </div>
        <div class="form-group">
          <label class="col-sm-4 control-label"><?php echo $MSG_USER_ID?></label>
          <div class="col-sm-4"><label class="col-sm-2 control-label"><?php echo htmlentities($_SESSION[$OJ_NAME.'_'.'user_id'], ENT_QUOTES, 'UTF-8')?></label></div>
          <?php require_once('./include/set_post_key.php');?>
        </div>
        <div class="form-group">
          <label class="col-sm-4 control-label"><?php echo $MSG_NICK?></label>
          <div class="col-sm-4"><input name="nick" class="form-control" value="<?php echo htmlentities($row['nick'],ENT_QUOTES,"UTF-8")?>" type="text"></div>
        </div>
        <div class="form-group">
          <label class="col-sm-4 control-label"><?php echo $MSG_PASSWORD?></label>
          <div class="col-sm-4"><input name="opassword" class="form-control" placeholder="<?php echo $MSG_PASSWORD?>*" type="password"></div>
        </div>
        <div class="form-group">
          <label class="col-sm-4 control-label"><?php echo "New ".$MSG_PASSWORD?></label>
          <div class="col-sm-4"><input name="npassword" class="form-control" type="password"></div>
        </div>
        <div class="form-group">
          <label class="col-sm-4 control-label"><?php echo "New ".$MSG_REPEAT_PASSWORD?></label>
          <div class="col-sm-4"><input name="rptpassword" class="form-control" type="password"></div>
        </div>
        <div class="form-group">
          <label class="col-sm-4 control-label"><?php echo $MSG_SCHOOL?></label>
          <div class="col-sm-4"><input name="school" class="form-control" value="<?php echo htmlentities($row['school'],ENT_QUOTES,"UTF-8")?>" type="text"></div>
          <?php if(isset($_SESSION[$OJ_NAME."_printer"])) echo "$MSG_HELP_BALLOON_SCHOOL";?>
        </div>
        <div class="form-group">
          <label class="col-sm-4 control-label"><?php echo $MSG_EMAIL?></label>
          <div class="col-sm-4"><input name="email" class="form-control" value="<?php echo htmlentities($row['email'],ENT_QUOTES,"UTF-8")?>" type="text"></div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-4 col-sm-2">
            <button name="submit" type="submit" class="btn btn-default btn-block"><?php echo $MSG_SUBMIT; ?></button>
          </div>
          <div class="col-sm-2">
            <button name="submit" type="reset" class="btn btn-default btn-block"><?php echo $MSG_RESET; ?></button>
          </div>
        </div>
      </form>

      <a href=export_ac_code.php>Download All AC Source</a><br>
    </div>
  </div> <!-- /container -->

  <!-- Bootstrap core JavaScript
  ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <?php include("template/$OJ_TEMPLATE/js.php");?>      
</body>
</html>
