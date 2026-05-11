<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Login</title>  
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

<form id="login" action="login.php" method="post" role="form" class="form-horizontal" onSubmit="return jsMd5();"  >
	<div class="form-group">
	<label class="col-sm-4 control-label"><?php echo $MSG_USER_ID?></label><div class="col-sm-4"><input name="user_id" class="form-control" placeholder="<?php echo $MSG_USER_ID?>" type="text" required ></div>						</div>
	<div class="form-group">
	<label class="col-sm-4 control-label"><?php echo $MSG_PASSWORD?></label><div class="col-sm-4"><input name="password" class="form-control" placeholder="<?php echo $MSG_PASSWORD?>" type="password"  autocomplete="off" required ></div>						</div>
<?php if($OJ_VCODE){?>

	<div class="form-group">
	<label class="col-sm-4 control-label"><?php echo $MSG_VCODE?></label><div class="col-sm-3"><input name="vcode" class="form-control" type="text" autocomplete=off ></div><div class="col-sm-4"><img id="vcode-img" alt="click to change" onclick="this.src='vcode.php?'+Math.random()" height="30px">*</div>						</div>
<?php }?>
	<div class="form-group">
	<div class="col-sm-offset-4 col-sm-2">
	<button name="submit" type="submit" class="btn btn-default btn-block" ><?php echo $MSG_LOGIN; ?></button>
	</div>
	<div class="col-sm-2">
	<a class="btn btn-default btn-block" href="lostpassword.php"><?php echo $MSG_LOST_PASSWORD; ?></a>
	</div>
	</div>
</form>					
      </div>
	<script src="<?php echo $OJ_CDN_URL?>include/md5-min.js"></script>
	<script>
		function jsMd5(){
			if($("input[name=password]").val()=="") return false;
			$("input[name=password]").val(hex_md5($("input[name=password]").val()));
			return true;
		}
	</script>
    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>	
    
<?php if ($OJ_VCODE) { ?>
    <script>
        $(document).ready(function () {
            $("#vcode-img").attr("src", "vcode.php?" + Math.random());
        })
    </script>
<?php } ?>
  </body>
</html>
