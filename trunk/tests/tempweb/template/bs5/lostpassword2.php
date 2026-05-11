<?php $show_title=isset($MSG_LOSTPASSWORD_MAILBOX) ? "$MSG_LOSTPASSWORD_MAILBOX - $OJ_NAME" : "Lost Password Mailbox - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-envelope"></i> <?php echo isset($MSG_LOSTPASSWORD_MAILBOX) ? $MSG_LOSTPASSWORD_MAILBOX : 'Lost Password Mailbox'?></h4>
    </div>
    <div class="card-body">
        <form action="lostpassword2.php" method="post" class="row g-3 justify-content-center">
            <div class="col-md-4">
                <label class="form-label"><?php echo isset($MSG_USER_ID) ? $MSG_USER_ID : 'User ID'?></label>
                <input name="user_id" type="text" class="form-control" size="20">
            </div>
            <div class="col-md-4">
                <label class="form-label"><?php echo isset($MSG_LOSTPASSWORD_MAILBOX) ? $MSG_LOSTPASSWORD_MAILBOX : 'Key'?></label>
                <input name="lost_key" type="text" class="form-control" size="20">
                <small class="text-body-secondary"><?php echo isset($MSG_LOSTPASSWORD_WILLBENEW) ? $MSG_LOSTPASSWORD_WILLBENEW : 'New password will be sent'?></small>
            </div>
            <div class="col-12 text-center mt-3">
                <input name="submit" type="submit" class="btn btn-primary" value="Submit">
            </div>
        </form>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
