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
  <div class="row">
    <div class="col-sm-offset-2 col-sm-10">
    <h3>정보 수정</h3>
    </div>
  </div>
  <form class="form-horizontal" action="modify.php" method="post">
    <div class="form-group">
      <label class="col-sm-2 control-label">아이디</label>
      <div class="col-sm-10">
        <p class="form-control-static">
          <?php echo $_SESSION['user_id']?>
          <?php require_once('./include/set_post_key.php');?>
        </p>
      </div>
    </div>
    <div class="form-group">
    <label for="inputNick" class="col-sm-2 control-label"><?php echo $MSG_NICK;?></label>
      <div class="col-sm-10">
        <input id="inputNick" name="nick" type="text" class="form-control" value="<?php echo htmlspecialchars($row->nick)?>">
      </div>
    </div>
    <div class="form-group">
      <label for="inputOldPassword" class="col-sm-2 control-label">현재 비밀번호</label>
      <div class="col-sm-10">
        <input id="inputOldPassword" name="opassword" class="form-control" type="password" placeholder="input current password">
      </div>
    </div>
    <div class="form-group">
      <label for="inputNewPassword" class="col-sm-2 control-label">새 비밀번호</label>
      <div class="col-sm-10">
        <input id="inputNewPassword" name="npassword" class="form-control" type="password" placeholder="input new password">
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <input id="inputRepeatNewPassword" name="rptpassword" class="form-control" type="password" placeholder="repeat new password">
      </div>
    </div>

    <div class="form-group">
      <label for="inputSchool" class="col-sm-2 control-label">학교</label>
      <div class="col-sm-10">
        <input id="inputSchool" name="school" class="form-control" type="text" value="<?php echo htmlspecialchars($row->school)?>">
      </div>
    </div>
    <div class="form-group">
      <label for="inputEmail" class="col-sm-2 control-label">메일주소</label>
      <div class="col-sm-10">
        <input id="inputEmail" name="email" class="form-control" type="text" value="<?php echo htmlspecialchars($row->email)?>">
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-1">
        <button type="submit" class="btn btn-primary"> 제 출 </button>
      </div>
      <div class="col-sm-1">
        <button type="reset" class="btn btn-link">초기화</button>
      </div>
    </div>
  </form>
  <a href=export_ac_code.php>Download All AC Source</a>
</div>
<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
</body>
</html>
