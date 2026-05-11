<?php $show_title="$MSG_REGISTER - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-person-plus"></i> <?php echo $MSG_REGISTER?></h4>
            </div>
            <div class="card-body">
                <?php if(isset($_GET['error']) && $_GET['error']==1){ ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo $MSG_REG_ERROR?>
                </div>
                <?php } ?>
                <form action="register.php" method="post">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $MSG_USER_ID?></label>
                        <input class="form-control" type="text" name="user_id" placeholder="<?php echo $MSG_USER_ID?>" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $MSG_NICK?></label>
                        <input class="form-control" type="text" name="nick" placeholder="<?php echo $MSG_NICK?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $MSG_PASSWORD?></label>
                        <input class="form-control" type="password" name="password" placeholder="<?php echo $MSG_PASSWORD?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $MSG_REPEAT_PASSWORD?></label>
                        <input class="form-control" type="password" name="rptpassword" placeholder="<?php echo $MSG_REPEAT_PASSWORD?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $MSG_SCHOOL?></label>
                        <input class="form-control" type="text" name="school" placeholder="<?php echo $MSG_SCHOOL?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $MSG_EMAIL?></label>
                        <input class="form-control" type="email" name="email" placeholder="<?php echo $MSG_EMAIL?>">
                    </div>
                    <?php if(isset($OJ_CAPTCHA) && $OJ_CAPTCHA){ ?>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $MSG_CAPTCHA?></label>
                        <div class="input-group">
                            <input class="form-control" type="text" name="captcha" placeholder="<?php echo $MSG_CAPTCHA?>" required>
                            <img src="captcha.php" onclick="this.src='captcha.php?'+Math.random();" style="cursor:pointer;border:1px solid #ccc;" alt="captcha">
                        </div>
                    </div>
                    <?php } ?>
                    <div class="mb-3">
                        <input type="hidden" name="spa" value="1">
                        <button class="btn btn-success w-100" type="submit"><i class="bi bi-person-plus"></i> <?php echo $MSG_REGISTER?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
