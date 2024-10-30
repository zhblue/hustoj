<?php require_once("admin-header.php");

if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'password_setter']) )){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}

if(isset($OJ_LANG)){
  require_once("../lang/$OJ_LANG.php");
}
?>

<title>Set Password</title>
<hr>
<center><h3><?php echo $MSG_USER."-".$MSG_SETPASSWORD?></h3></center>

<div class='container'>

<?php
if(isset($_POST['do'])){	
	require_once("../include/check_post_key.php");
	require_once("../include/my_func.inc.php");
	
	$user_id = $_POST['user_id'];
        $passwd = $_POST['passwd'];
	$passwd = pwGen($passwd);
	$sql = "update `users` set `password`=? where `user_id`=?  and user_id not in( select user_id from privilege where rightstr='administrator')";
	
	if(pdo_query($sql,$passwd,$user_id) == 1)
		echo "<center><h4 class='text-danger'>User ".htmlentities($_POST['user_id'], ENT_QUOTES, 'UTF-8')."'s Password Changed!</h4></center>";
  else
  	        echo "<center><h4 class='text-danger'>There is No such User ".htmlentities($_POST['user_id'], ENT_QUOTES, 'UTF-8')."! or User ".htmlentities($_POST['user_id'], ENT_QUOTES, 'UTF-8')." is administrator!</h4></center>";

    ?>
	 <script>window.setTimeout("history.go(-2);",2000);</script>
     <?php
}
?>

<form action=changepass.php method=post class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-offset-3 col-sm-3 control-label"><?php echo $MSG_USER_ID?></label>
		<?php if(isset($_GET['uid'])) { ?>
		<div class="col-sm-3"><input name="user_id" class="form-control" value="<?php echo htmlentities($_GET['uid'], ENT_QUOTES, 'UTF-8');?>" type="text" required ></div>
  	<?php } else if(isset($_POST['user_id'])) { ?>
		<div class="col-sm-3"><input name="user_id" class="form-control" value="<?php echo htmlentities($_POST['user_id'], ENT_QUOTES, 'UTF-8');?>" type="text" required ></div>
		<?php } else { ?>
		<div class="col-sm-3"><input name="user_id" class="form-control" placeholder="<?php echo $MSG_USER_ID."*"?>" type="text" required ></div>
		<?php } ?>
	</div>

	<div class="form-group">
		<label class="col-sm-offset-3 col-sm-3 control-label"><?php echo $MSG_PASSWORD?></label>
		<div class="col-sm-3"><input name="passwd" class="form-control" placeholder="<?php echo $MSG_PASSWORD."*"?>" type="password"  autocomplete="off" required ></div>
	</div>

	<div class="form-group">
		<?php require_once("../include/set_post_key.php");?>
		<div class="col-sm-offset-4 col-sm-2">
			<button name="do" type="hidden" value="do" class="btn btn-default btn-block" ><?php echo $MSG_SAVE?></button>
		</div>
		<div class="col-sm-2">
			<button name="submit" type="button" onclick='$("input[name=passwd]").attr("type","text");' class="btn btn-default btn-block">Show</button>
		</div>
	</div>
</form>

</div>



