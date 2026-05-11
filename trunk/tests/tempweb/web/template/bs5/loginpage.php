<?php $show_title="$MSG_LOGIN - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-box-arrow-in-right"></i> <?php echo $MSG_LOGIN?></h4>
            </div>
            <div class="card-body">
                <?php if(isset($_GET['error']) && $_GET['error']==1){ ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo $MSG_LOGIN_ERROR?>
                </div>
                <?php } ?>
                <form action="login.php" method="post">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $MSG_USER_ID?></label>
                        <input class="form-control" type="text" name="user_id" placeholder="<?php echo $MSG_USER_ID?>" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo $MSG_PASSWORD?></label>
                        <input class="form-control" type="password" name="password" placeholder="<?php echo $MSG_PASSWORD?>" required>
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
                        <button class="btn btn-primary w-100" type="submit"><i class="bi bi-box-arrow-in-right"></i> <?php echo $MSG_LOGIN?></button>
                    </div>
                </form>
                <div class="text-center">
                    <a href="lostpassword.php"><i class="bi bi-question-circle"></i> <?php echo $MSG_LOST_PASSWORD?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
