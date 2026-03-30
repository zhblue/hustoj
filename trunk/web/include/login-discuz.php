<?php 
	require_once("./include/my_func.inc.php");
    
	function check_login($user_id,$password){
		session_destroy();
		session_start();
		$discuz_db="discuz";

		$ret=false;
		pdo_query("set names utf8");
		// 安全修复：使用参数化查询防止 SQL 注入
		$sql="select password,salt,username from ".$discuz_db.".uc_members where username=?";
		$result=pdo_query($sql, $user_id);
		$row = $result[0];
		if($row && $row['password']==md5(md5($password).$row['salt'])){
				$_SESSION[$OJ_NAME.'_'.'user_id']=$row['username'];
				$ret=$_SESSION[$OJ_NAME.'_'.'user_id'];
		}
				
		return $ret; 
	}
?>
