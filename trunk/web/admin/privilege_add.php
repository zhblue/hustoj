<?php require_once("admin-header.php");
require_once("../include/email.class.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))) {
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
?>

<title>Privilege Add</title>
<hr>
<center><h3><?php echo $MSG_USER."-".$MSG_PRIVILEGE."-".$MSG_ADD?></h3></center>

<div class="container">

<?php
if (isset($_POST['do'])) {
	require_once("../include/check_post_key.php");

	$user_id = $_POST['user_id'];
	$rightstr = $_POST['rightstr'];
	$valuestr = "true";
	if(isset($_POST['valuestr']))
		$valuestr = $_POST['valuestr'];
	
	if (isset($_POST['contest']))
		$rightstr = "c$rightstr";

	if (isset($_POST['psv']))
		$rightstr = "s$rightstr";

	$sql = "insert into `privilege`(user_id,rightstr,valuestr,defunct) values(?,?,?,'N')";
	$link= 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']);
        $msg = $_SESSION[$OJ_NAME.'_user_id']." $MSG_ADD $rightstr [$valuestr] $MSG_PRIVILEGE -> $user_id @  ".date('Y-m-d h:i:s a', time());
        $msg .="\n\nmessage from site: $link";
        $rows = pdo_query($sql,$user_id,$rightstr,$valuestr);
        if ($OJ_ADMIN=="root@localhost"){
                $sql="select email from users where user_id=? ";
                $OJ_ADMIN=pdo_query($sql,$_SESSION[$OJ_NAME.'_user_id'])[0][0];
		//email($OJ_ADMIN,"Privilege Add Warning!",$msg);
        }else{
       	 	email($OJ_ADMIN,"Privilege Add Warning!",$msg);
	}
        echo "<center><h4 class='text-danger'>User ".htmlentities($_POST['user_id'], ENT_QUOTES, 'UTF-8')."'s Privilege Added!</h4></center>";
}
?>

<div>
<form method="post" class="form-horizontal">
	<?php require_once("../include/set_post_key.php");?>
	<center><label class="text-info"><?php echo $MSG_HELP_ADD_PRIVILEGE?></label></center>
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
		<label class="col-sm-offset-3 col-sm-3 control-label"><?php echo $MSG_PRIVILEGE_TYPE?></label>
		<select class="col-sm-3" name="rightstr" onchange="show_value_input(this.value)" >
		<?php
			$rightarray = array("administrator","problem_editor","source_browser","contest_creator","user_adder","http_judge","password_setter","printer","balloon","vip",'problem_start','problem_end');
			while ($val=current($rightarray)) {
                                $key=key($rightarray);
                                if (isset($rightstr) && ($rightstr == $val)) {
                                        echo '<option value="'.$val.'" selected>'.$val.'</option>';
                                } else {
                                        echo '<option value="'.$val.'">'.$val.'</option>';
                                }
                                next($rightarray);
                        }
		?>
		</select>
		<div class="col-sm-offset-9"><input id='value_input' type="text" class="form-control" name="valuestr" value="true"></div>
	</div>
	<script>
		function show_value_input(new_value){
			if(new_value=='problem_start'||new_value=='problem_end') {
				$("#value_input").val("1000");
				$("#value_input").show();
			}else{
				$("#value_input").val("true");
				$("#value_input").hide();
			}
		}
		$(document).ready(function(){
			 $("#value_input").hide();
		});
	</script>
	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-2">
			<input type='hidden' name='do' value='do'>
			<button type="submit" name="do" value="do" class="btn btn-default btn-block" ><?php echo $MSG_SAVE?></button>
		</div>
		<div class="col-sm-2">
			<button type="reset" class="btn btn-default btn-block"><?php echo $MSG_RESET?></button>
		</div>
	</div>
</form>
</div>

<br>

<div>
<form method="post" class="form-horizontal">
	<?php require_once("../include/set_post_key.php");?>
	<center><label class="text-info"><?php echo $MSG_HELP_ADD_CONTEST_USER?></label></center>
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
		<label class="col-sm-offset-3 col-sm-3 control-label"><?php echo $MSG_CONTEST_ID?></label>
		<div class="col-sm-3"><input name="rightstr" class="form-control" placeholder="<?php echo $MSG_CONTEST_ID."*"?>" type="text"></div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-2">
			<input type='hidden' name='do' value='do'>
			<button type="submit" name="contest" value="do" class="btn btn-default btn-block" ><?php echo $MSG_SAVE?></button>
			<input type=hidden name="postkey" value="<?php echo $_SESSION[$OJ_NAME.'_'.'postkey']?>">
		</div>
		<div class="col-sm-2">
			<button type="reset" class="btn btn-default btn-block"><?php echo $MSG_RESET?></button>
		</div>
	</div>
</form>
</div>

<br>

<div>
<form method="post" class="form-horizontal">
	<?php require_once("../include/set_post_key.php");?>
	<center><label class="text-info"><?php echo $MSG_HELP_ADD_SOLUTION_VIEW?></label></center>
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
		<label class="col-sm-offset-3 col-sm-3 control-label"><?php echo $MSG_PROBLEM_ID?></label>
		<div class="col-sm-3"><input name="rightstr" class="form-control" placeholder="<?php echo $MSG_PROBLEM_ID."*"?>" type="text"></div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-4 col-sm-2">
			<input type='hidden' name='do' value='do'>
			<button type="submit" name="psv" value="do" class="btn btn-default btn-block" ><?php echo $MSG_SAVE?></button>
			<input type=hidden name="postkey" value="<?php echo $_SESSION[$OJ_NAME.'_'.'postkey']?>">
			</div>
		<div class="col-sm-2">
			<button type="reset" class="btn btn-default btn-block"><?php echo $MSG_RESET?></button>
		</div>
	</div>
</form>
</div>

</div>
