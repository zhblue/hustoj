<?php require("admin-header.php");

if (isset($OJ_LANG)) {
	require_once("../lang/$OJ_LANG.php");
}
?>

<title>User Set IP</title>
<hr>
<center><h3><?php echo $MSG_SET_LOGIN_IP?></h3></center>

<div class='container'>

<?php
require_once("../include/set_get_key.php");
$sql = "";
if(isset($_POST['do'])){
	require_once("../include/check_post_key.php");
	
	$user_id = $_POST['user_id'];
  $ip = $_POST['ip'];

  if(false){
		$user_id = stripslashes($user_id);
		$ip = stripslashes($ip);
	}

	$sql = "insert into loginlog (user_id,password,ip,time) value(?,?,?,now())";
	$result = pdo_query($sql,$user_id,"set ip by ".$_SESSION[$OJ_NAME."_user_id"],$ip);
	echo "<center><h4 class='text-danger'>User ".htmlentities($_POST['user_id'], ENT_QUOTES, 'UTF-8')."'s Login IP Changed!</h4></center>";
}
?>

<form action=user_set_ip.php method=post class="form-horizontal">
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
		<label class="col-sm-offset-3 col-sm-3 control-label"><?php echo "New IP"?></label>
		<?php if(isset($_POST['ip'])) { ?>
		<div class="col-sm-3"><input name="ip" class="form-control" value="<?php echo htmlentities($_POST['ip'], ENT_QUOTES, 'UTF-8')?>" type="text"  autocomplete="off" required ></div>
		<?php } else { ?>
		<div class="col-sm-3"><input name="ip" class="form-control" placeholder="<?php echo "?.?.?.?*"?>" type="text"  autocomplete="off" required ></div>
		<?php } ?>
	</div>

	<div class="form-group">
		<?php require_once("../include/set_post_key.php");?>
		<div class="col-sm-offset-4 col-sm-2">
			<button name="do" type="hidden" value="do" class="btn btn-default btn-block" ><?php echo $MSG_SAVE; ?></button>
		</div>
		<div class="col-sm-2">
			<button name="submit" type="reset" class="btn btn-default btn-block"><?php echo $MSG_RESET?></button>
		</div>
	</div>
</form>

</div>

<?php
require("../oj-footer.php");
?>
