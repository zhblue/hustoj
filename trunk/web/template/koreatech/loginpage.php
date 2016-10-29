<!DOCTYPE html>
<html lang="ko">
<head>
  <title><?php echo $view_title?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
</head>
<body>
  <?php require_once("oj-header.php");?>
  <div class="container">
    <form class="form-horizontal" action="login.php?url=<?php echo urlencode($referrer);?>" method="post">
      <div class="form-group">
        <label for="inputUserId" class="col-sm-2 control-label"><?php echo $MSG_USER_ID?></label>
        <div class="col-sm-10">
          <input type="text" class="form-control" id="inputUserId" name="user_id" placeholder="id">
        </div>
      </div>
      <div class="form-group">
        <label for="inputPassword" class="col-sm-2 control-label"><?php echo $MSG_PASSWORD?></label>
        <div class="col-sm-10">
          <input type="password" class="form-control" id="inputPassword" name="password" placeholder="password">
        </div>
      </div>
<?php if($OJ_VCODE){?>
      <div class="form-group">
        <label for="inputVerificationCode" class="col-sm-2 control-label"><?php echo $MSG_VCODE?></label>
        <div class="col-sm-5">
          <input type="text" class="form-control" id="inputVerificationCode" name="vcode" placeholder="verification code">
        </div>
        <div class="col-sm-5">
          <img alt="click to change" src="vcode.php" onclick="this.src='vcode.php?'+Math.random()">
        </div>
      </div>
<?php }?>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <button type="submit" class="btn btn-default">Login</button>
          or <a href="lostpassword.php">Lost Password</a>
        </div>
      </div>
    </form>
  </div>
<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
</body>
</html>

