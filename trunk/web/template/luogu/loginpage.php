<?php $show_title="$MSG_LOGIN - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="lg-auth">
  <div class="lg-auth-card">
    <h1><?php echo $MSG_LOGIN ?></h1>
    <div class="ui error message" id="error" hidden></div>
    <form class="ui large form" id="login" action="login.php" method="post" role="form" onSubmit="return jsMd5();">
      <div class="field">
        <div class="ui left icon input">
          <i class="user icon"></i>
          <input name="user_id" placeholder="<?php echo $MSG_USER_ID ?>" type="text" id="username" autocomplete="username">
        </div>
      </div>
      <div class="field">
        <div class="ui left icon input">
          <i class="lock icon"></i>
          <input name="password" placeholder="<?php echo $MSG_PASSWORD ?>" autocomplete="current-password" type="password" id="password">
        </div>
      </div>
      <?php if($OJ_VCODE){?>
        <div class="field">
          <div class="ui left icon input">
            <i class="lock icon"></i>
            <input name="vcode" placeholder="<?php echo $MSG_VCODE ?>" type="text" autocomplete="off">
            <img id="vcode-img" onclick="this.src='vcode.php?'+Math.random()" height="34px" alt="vcode">
          </div>
        </div>
      <?php }?>
      <button name="submit" type="submit" class="ui fluid large primary submit button"><?php echo $MSG_LOGIN ?></button>
    </form>

    <div class="lg-auth-links">
      <?php if ($OJ_REGISTER){ ?>
        <a href="registerpage.php"><?php echo $MSG_REGISTER ?></a>
      <?php } ?>
      <a href="lostpassword.php"><?php echo $MSG_LOST_PASSWORD ?></a>
    </div>
  </div>
</div>

<script src="<?php echo $OJ_CDN_URL?>include/md5-min.js"></script>
<script>
  function jsMd5(){
    if($("input[name=password]").val()=="") return false;
    $("input[name=password]").val(hex_md5($("input[name=password]").val()));
    return true;
  }
</script>
<?php if ($OJ_VCODE) { ?>
    <script>
        $(document).ready(function () {
            $("#vcode-img").attr("src", "vcode.php?" + Math.random());
        })
    </script>
<?php } ?>
<iframe id="sk" src="session.php" height=0px width=0px style="display:none"></iframe>
<?php include("template/$OJ_TEMPLATE/footer.php");?>
