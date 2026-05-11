<?php
require_once("admin-header.php");
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
if(!(isset($_SESSION[$OJ_NAME.'_'.'password_setter'])|| isset($_SESSION[$OJ_NAME.'_'.'administrator'])  )) return;
function update_for_user($user_id){
	$sql="SELECT `user_id`,`password` FROM `users` WHERE `user_id`=?";
	$result=pdo_query($sql,$user_id);
	$row = $result[0];
	if ($row){
		$oldpw = $row['password'];
		if (!isOldPW($oldpw)) return False;
		$newpw = pwGen($row['password'],True);
		$sql="UPDATE `users` set `password`=? where `user_id`=? LIMIT 1";
		pdo_query($sql,$newpw,$user_id);
		return True;
	}
	return False;
}

$sql="select user_id from `users`";
$result=pdo_query($sql);
 foreach($result as $row){
	$uid = $row['user_id'];
	echo $uid.">".update_for_user($uid)."\n";
}
unlink("update_pw.php");
?>
