<?php $show_title="$MSG_REG_INFO - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<div class="padding">
  <h1><?php echo $MSG_REG_INFO?></h1>
  <div class="ui error message" id="error" data-am-alert hidden>
    <p id="error_info"></p>
  </div>
          <form action="register.php" method="post" role="form" class="ui form">
                <div class="field">
                    <label for="username"><?php echo $MSG_USER_ID?>*</label>
                    <input name="user_id" class="form-control" placeholder="" type="text">
                </div>
                <div class="field">
                    <label for="username"><?php echo $MSG_NICK?>*</label>
                    <input name="nick" placeholder="" type="text">
                </div>
                <div class="two fields">
                    <div class="field">
                    <label class="ui header"><?php echo $MSG_PASSWORD?>*</label>
                      <input name="password" placeholder="" type="password">
                    </div>
                    <div class="field">
                      <label class="ui header"><?php echo $MSG_REPEAT_PASSWORD?>*</label>
                      <input name="rptpassword" placeholder="" type="password">
                    </div>
                </div>
                <div class="field">
                    <label for="username"><?php echo $MSG_SCHOOL?></label>
                    <input name="school" placeholder="" type="text" value="">
                </div>
                <div class="field">
                    <label for="email"><?php echo $MSG_EMAIL?>*</label>
                    <input name="email" placeholder="" type="text">
                </div>
                <?php if($OJ_VCODE){?>
                  <div class="field">
                    <label for="email"><?php echo $MSG_VCODE?>*</label>
                    <input name="vcode" class="form-control" placeholder="" type="text" autocomplete=off >
                    <img alt="click to change" src="vcode.php" onclick="this.src='vcode.php?'+Math.random()" height="30px">
                  </div>
                <?php }?>
                <button name="submit" type="submit" class="ui button"><?php echo $MSG_REGISTER; ?></button>
                <button name="submit" type="reset" class="ui button"><?php echo $MSG_RESET; ?></button>
            </form>
</div>
<?php include("template/$OJ_TEMPLATE/footer.php");?>
