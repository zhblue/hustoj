<?php $show_title="$MSG_REG_INFO - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<div class="lg-auth" style="align-items:flex-start;">
  <div class="lg-register">
    <h1><?php echo $MSG_REG_INFO?></h1>
    <div class="ui error message" id="error" hidden>
      <p id="error_info"></p>
    </div>
    <form action="register.php" method="post" role="form" class="ui form">
      <div class="field">
        <label for="username"><?php echo $MSG_USER_ID?>*</label>
        <input name="user_id" placeholder="同名用户在 github 给 hustoj 加 ★ 可得 ★" type="text" autocomplete="username">
      </div>
      <div class="field">
        <label for="nick"><?php echo $MSG_NICK?>*</label>
        <input name="nick" placeholder="教学系统建议用真名" type="text">
      </div>
      <div class="two fields">
        <div class="field">
          <label><?php echo $MSG_PASSWORD?>*</label>
          <input name="password" type="password" autocomplete="new-password">
        </div>
        <div class="field">
          <label><?php echo $MSG_REPEAT_PASSWORD?>*</label>
          <input name="rptpassword" type="password" autocomplete="new-password">
        </div>
      </div>
      <div class="field">
        <label for="school"><?php echo $MSG_SCHOOL?></label>
        <input name="school" type="text" value="">
      </div>
      <div class="field">
        <label for="email"><?php echo $MSG_EMAIL?>*</label>
        <input name="email" placeholder="用 QQ 邮箱可得 QQ 头像" type="email">
      </div>
      <?php if($OJ_VCODE){?>
        <div class="field">
          <label for="vcode"><?php echo $MSG_VCODE?>*</label>
          <input name="vcode" placeholder="" type="text" autocomplete="off">
          <img alt="click to change" src="vcode.php" onclick="this.src='vcode.php?'+Math.random()" height="34px">
        </div>
      <?php }?>
      <button name="submit" type="submit" class="ui primary button"><?php echo $MSG_REGISTER; ?></button>
      <button name="submit" type="reset" class="ui button"><?php echo $MSG_RESET; ?></button>
    </form>
  </div>
</div>
<?php include("template/$OJ_TEMPLATE/footer.php");?>
