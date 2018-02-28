<?php 
	require_once(dirname(__FILE__) . "/../include/my_func.inc.php");
    
	function check_login($user_id,$password){
		
		$pass2 = 'No Saved';
		session_destroy();
		session_start();
		$sql="SELECT `user_id`,`password` FROM `users` WHERE `user_id`=? and defunct='N' ";
		$result=pdo_query($sql,$user_id);
		if(count($result)==1){
			$row = $result[0];
			if( pwCheck($password,$row['password'])){
				$user_id=$row['user_id'];
				$ip = ($_SERVER['REMOTE_ADDR']);
				if( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ){
				    $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR'];
				    $tmp_ip=explode(',',$REMOTE_ADDR);
				    $ip =(htmlentities($tmp_ip[0],ENT_QUOTES,"UTF-8"));
				}
				$sql="INSERT INTO `loginlog` VALUES(?,'login ok',?,NOW())";
				pdo_query($sql,$user_id,$ip);
				return $user_id;
			}
		}
		return false; 
	}
?>
