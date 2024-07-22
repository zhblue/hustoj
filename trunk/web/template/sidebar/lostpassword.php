<?php $show_title="$MSG_LOST_PASSWORD - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
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
      <script src="template/<?php echo $OJ_TEMPLATE?>/js/html5shiv.js"></script>
      <script src="template/<?php echo $OJ_TEMPLATE?>/js/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">    
      <!-- Main component for a primary marketing message or call to action -->
      <div class="padding">
 <form action=lostpassword.php method=post>
<center>
<table width=400 algin=center>
<tr><td width=200><?php echo $MSG_USER_ID?>:<td width=200><input name="user_id" type="text" size=20></tr>
<tr><td><?php echo $MSG_EMAIL?>:<td><input name="email" type="text" size=20></tr>
<tr><td><?php echo $MSG_VCODE?>:</td>
<td><input name="vcode" size=4 type=text><img alt="click to change" src=vcode.php onclick="this.src='vcode.php?'+Math.random()">*</td>
</tr>
<tr><td><td><input name="submit" type="submit" size=10 value="Submit">
</tr>
</table>
<center>
</form>
      </div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>	    
  </body>
</html>
