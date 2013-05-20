<?php require("admin-header.php");
if (!(isset($_SESSION['administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}?>

<html>
	<head>
		<title>OJ Administration</title>
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Cache-Control" content="no-cache">
		<meta http-equiv="Content-Language" content="zh-cn">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel=stylesheet href='admin.css' type='text/css'>
	</head>
<body>


<div class="container-fluid">
	<?php require_once("admin-bar.php"); ?>
	<div class="row-fluid top-space">
		<div class="span2" >
			<div class="menu-group"  >
				<?php require_once("menu.php") ?>
			</div>
		</div>
		<div class="span10 bk">
			<div class="align-center">
<?php if(isset($_POST['prefix'])){
	require_once("../include/check_post_key.php");
	$prefix=$_POST['prefix'];
	require_once("../include/my_func.inc.php");
	if (!is_valid_user_name($prefix)){
		echo "Prefix is not valid.";
		exit(0);
	}
	$teamnumber=intval($_POST['teamnumber']);
        $pieces = explode("\n", trim($_POST['ulist']));
	
	if ($teamnumber>0){
		echo "<table border=1>";
		echo "<tr><td colspan=3>Copy these accounts to distribute</td></tr>";
		echo "<tr><td>team_name<td>login_id</td><td>password</td></tr>";
		for($i=1;$i<=$teamnumber;$i++){
			
        $user_id=$prefix.($i<10?('0'.$i):$i);
			$password=strtoupper(substr(MD5($user_id.rand(0,9999999)),0,10));
                        if(isset($pieces[$i-1]))
                        	$nick=$pieces[$i-1];
                        else
							$nick="your_own_nick";
			echo "<tr><td>$nick<td>$user_id</td><td>$password</td></tr>";
			
			$password=pwGen($password);
			$email="your_own_email@internet";
                       
			$school="your_own_school";
			$sql="INSERT INTO `users`("."`user_id`,`email`,`ip`,`accesstime`,`password`,`reg_time`,`nick`,`school`)"."VALUES('".$user_id."','".$email."','".$_SERVER['REMOTE_ADDR']."',NOW(),'".$password."',NOW(),'".$nick."','".$school."')on DUPLICATE KEY UPDATE `email`='".$email."',`ip`='".$_SERVER['REMOTE_ADDR']."',`accesstime`=NOW(),`password`='".$password."',`reg_time`=now(),nick='".$nick."',`school`='".$school."'";
			mysql_query($sql) or die(mysql_error());
		}
		echo  "</table>";
		
		
	}
	
}
?>
<b>TeamGenerator:</b>
	
	<form action='team_generate.php' method=post>
	    Prefix:<input type='test' name='prefix' value='team'>
		Generate<input type=input name='teamnumber' value=50>Teams.
		<input type=submit value=Generate><br>
                <p>Users:</p><textarea name="ulist" rows="20" cols="20"><?php if (isset($ulist)) { echo $ulist; } ?></textarea>
		<?php require_once("../include/set_post_key.php");?>
	</form>


			</div>
		</div>
	</div>
</div>

</body>
</html>
