<?php
// Backend provides: $row = ['school', 'nick', 'email'] from users table
// All other variables must be checked with isset() + fallback
$my_nick = isset($row['nick']) ? $row['nick'] : '';
$my_school = isset($row['school']) ? $row['school'] : '';
$my_email = isset($row['email']) ? $row['email'] : '';
$my_user_id = isset($_SESSION[$OJ_NAME.'_user_id']) ? $_SESSION[$OJ_NAME.'_user_id'] : '';
?>
<?php $show_title=isset($MSG_REG_INFO) ? "$MSG_REG_INFO - $OJ_NAME" : "Modify Profile - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-pencil-square"></i> <?php echo isset($MSG_REG_INFO) ? $MSG_REG_INFO : 'Profile Settings'?></h4>
    </div>
    <div class="card-body">
        <form action="modify.php" method="post">
            <div class="mb-3">
                <label class="form-label"><?php echo isset($MSG_USER_ID) ? $MSG_USER_ID : 'User ID'?></label>
                <input class="form-control" type="text" value="<?php echo htmlentities($my_user_id, ENT_QUOTES, 'utf-8')?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label"><?php echo isset($MSG_NICK) ? $MSG_NICK : 'Nickname'?></label>
                <input class="form-control" type="text" name="nick" value="<?php echo htmlentities($my_nick, ENT_QUOTES, 'utf-8')?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><?php echo isset($MSG_SCHOOL) ? $MSG_SCHOOL : 'School'?></label>
                <input class="form-control" type="text" name="school" value="<?php echo htmlentities($my_school, ENT_QUOTES, 'utf-8')?>">
            </div>
            <div class="mb-3">
                <label class="form-label"><?php echo isset($MSG_EMAIL) ? $MSG_EMAIL : 'Email'?></label>
                <input class="form-control" type="email" name="email" value="<?php echo htmlentities($my_email, ENT_QUOTES, 'utf-8')?>">
            </div>
            <div class="mb-3">
                <label class="form-label"><?php echo isset($MSG_PASSWORD) ? $MSG_PASSWORD : 'Password'?></label>
                <input class="form-control" type="password" name="password" placeholder="Leave blank to keep current password">
                <small class="text-body-secondary">Leave blank to keep current password</small>
            </div>
            <div class="mb-3">
                <input type="hidden" name="spa" value="1">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-check-lg"></i> <?php echo isset($MSG_SAVE) ? $MSG_SAVE : 'Save'?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
