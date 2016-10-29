<!DOCTYPE html>
<html lang="ko">
<head>
  <title><?php echo $view_title?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
</head>
<body>
<div class="container">
  <?php require_once("oj-header.php");?>
  <div class="col-sm-offset-2 col-sm-10">
    <h3><?php echo $MSG_REG_INFO?></h3>
  </div>
  <form class="form-horizontal" action="register.php" method="post">
    <div class="form-group">
      <label class="col-sm-2 control-label"><?php echo $MSG_USER_ID?>*</label>
      <div class="col-sm-10">
        <input name="user_id" type="text" class="form-control" placeholder="input id">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label"><?php echo $MSG_NICK?></label>
      <div class="col-sm-10">
        <input name="nick" type="text" class="form-control" placeholder="input any words">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label"><?php echo $MSG_PASSWORD?>*</label>
      <div class="col-sm-10">
        <input name="password" type="password" class="form-control" placeholder="input password">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label"><?php echo $MSG_REPEAT_PASSWORD?>*</label>
      <div class="col-sm-10">
        <input name="rptpassword" type="password" class="form-control" placeholder="repeat password">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label"><?php echo $MSG_SCHOOL?></label>
      <div class="col-sm-10">
        <input name="school" type="text" class="form-control" placeholder="school">
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-2 control-label"><?php echo $MSG_EMAIL?></label>
      <div class="col-sm-10">
        <input name="email" type="text" class="form-control" placeholder="email">
      </div>
    </div>
<?php if($OJ_VCODE){?>
    <div class="form-group">
      <label class="col-sm-2 control-label"><?php echo $MSG_VCODE?>*</label>
      <div class="col-sm-8">
        <input name="vcode" type="text" class="form-control" placeholder="vcode">
      </div>
      <div class="col-sm-2">
        <img alt="click to change" src="vcode.php" onclick="this.src='vcode.php?'+Math.random()">
      </div>
    </div>
<?php }?>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <button type="submit" class="btn btn-primary">가입</button>
        <button type="reset" class="btn btn-default">초기화</button>
      </div>
    </div>
  </form>
<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
</body>
</html>
