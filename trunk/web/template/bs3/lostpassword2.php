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
 <form action=lostpassword2.php method=post>
<center>
<table algin=center>
<tr><td><?php echo $MSG_USER_ID?>:<td><input name="user_id" type="text" size=20></td></tr>
<tr><td><?php echo $MSG_LOSTPASSWORD_MAILBOX ?>:<td><input name="lost_key" type="text" size=20>*
        <?php echo $MSG_LOSTPASSWORD_WILLBENEW ?></td></tr>
<?php // if($OJ_VCODE){
  ?>
<tr><td><?php echo $MSG_VCODE?>:</td>
<td><input name="vcode" size=4 type=text autocomplete=off ><img alt="click to change" src=vcode.php onclick="this.src='vcode.php#'+Math.random()">*</td>
</tr>
<?php // }
  ?>
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
