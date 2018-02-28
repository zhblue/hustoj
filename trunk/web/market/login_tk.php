<?php
require_once(dirname(__FILE__) . "/../include/db_info.inc.php");
require_once(dirname(__FILE__). "/../include/login-".$OJ_LOGIN_MOD.".php");
$user_id=$_POST['user_id'];
$password=$_POST['password'];
if (get_magic_quotes_gpc ()) {
	$user_id= stripslashes ( $user_id);
	$password= stripslashes ( $password);
}
$sql="SELECT `rightstr` FROM `privilege` WHERE `user_id`=?";
$login=check_login($user_id,$password);
if ($login)
{
	$_SESSION[$OJ_NAME.'_'.'user_id']=$login;
	$result=pdo_query($sql,$login);

	foreach ($result as $row)
		$_SESSION[$OJ_NAME.'_'.$row['rightstr']]=true;
	echo "isLogin";
}else{
	echo "noLogin";
}
?>
