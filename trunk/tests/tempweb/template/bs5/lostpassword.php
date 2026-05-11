<?php $show_title=isset($MSG_LOST_PASSWORD) ? "$MSG_LOST_PASSWORD - $OJ_NAME" : "Lost Password - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-key"></i> <?php echo isset($MSG_LOST_PASSWORD) ? $MSG_LOST_PASSWORD : 'Lost Password'?></h4>
    </div>
    <div class="card-body">
        <?php if(isset($SMTP_USER) && $SMTP_USER == "mailer@qq.com"){ ?>
            <div class="alert alert-warning">
                管理员没有配置邮件发送账户，请联系系统管理员或老师来手工重置密码。
                <?php if(isset($_SESSION[$OJ_NAME."_administrator"])){ ?>
                <br>请配置 db_info.inc.php 中的 $SMTP_USER 等参数，激活邮件密码找回功能。
                <?php } ?>
            </div>
        <?php } ?>

        <?php if(isset($error_msg) && !empty($error_msg)){ ?>
            <div class="alert alert-danger"><?php echo htmlentities($error_msg)?></div>
        <?php } ?>

        <form action="lostpassword.php" method="post" class="row g-3 justify-content-center">
            <div class="col-md-4">
                <label class="form-label"><?php echo isset($MSG_USER_ID) ? $MSG_USER_ID : 'User ID'?></label>
                <input name="user_id" type="text" class="form-control" size="20">
            </div>
            <div class="col-md-4">
                <label class="form-label"><?php echo isset($MSG_EMAIL) ? $MSG_EMAIL : 'Email'?></label>
                <input name="email" type="text" class="form-control" size="20">
            </div>
            <div class="col-md-4">
                <label class="form-label"><?php echo isset($MSG_VCODE) ? $MSG_VCODE : 'VCode'?></label>
                <div class="input-group">
                    <input name="vcode" type="text" class="form-control" size="4" autocomplete="off">
                    <img src="vcode.php" class="img-thumbnail" onclick="this.src='vcode.php?'+Math.random()" style="cursor:pointer;height:38px;">
                </div>
            </div>
            <div class="col-12 text-center mt-3">
                <input name="submit" type="submit" class="btn btn-primary" value="Submit">
            </div>
        </form>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
