<?php $show_title="$MSG_REG_INFO - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-pencil-square"></i> <?php echo $MSG_REG_INFO?></h4>
    </div>
    <div class="card-body">
        <form action="modify.php" method="post">
            <div class="mb-3">
                <label class="form-label"><?php echo $MSG_USER_ID?></label>
                <input class="form-control" type="text" value="<?php echo htmlentities($_SESSION[$OJ_NAME.'_user_id'],ENT_QUOTES,'utf-8')?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label"><?php echo $MSG_NICK?></label>
                <input class="form-control" type="text" name="nick" value="<?php echo htmlentities($nick,ENT_QUOTES,'utf-8')?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><?php echo $MSG_SCHOOL?></label>
                <input class="form-control" type="text" name="school" value="<?php echo htmlentities($school,ENT_QUOTES,'utf-8')?>">
            </div>
            <div class="mb-3">
                <label class="form-label"><?php echo $MSG_EMAIL?></label>
                <input class="form-control" type="email" name="email" value="<?php echo htmlentities($email,ENT_QUOTES,'utf-8')?>">
            </div>
            <div class="mb-3">
                <label class="form-label"><?php echo $MSG_PASSWORD?></label>
                <input class="form-control" type="password" name="password" placeholder="<?php echo $MSG_LEAVE_BLANK?>">
            </div>
            <div class="mb-3">
                <input type="hidden" name="spa" value="1">
                <button class="btn btn-primary" type="submit"><i class="bi bi-check-lg"></i> <?php echo $MSG_SAVE?></button>
            </div>
        </form>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
