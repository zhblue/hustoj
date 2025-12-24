<?php
	require_once('./include/db_info.inc.php');
	require_once('./include/setlang.php');
	require_once("./include/const.inc.php");
	require_once("./include/my_func.inc.php");
	/**
	 * 处理用户邮箱确认激活功能
	 * 通过GET参数中的激活码来激活被禁用的用户账户
	 *
	 * 该功能首先检查是否启用了邮箱确认功能($OJ_EMAIL_CONFIRM)，
	 * 然后验证激活码是否为空，如果不为空则执行用户激活操作，
	 * 最后重定向到登录页面
	 */
		$code=$_GET['code'];
		if($OJ_EMAIL_CONFIRM && $code!=""){
		  	$sql="update `users` set defunct='N',activecode=''  WHERE `activecode`=? and `activecode`!='' and defunct='Y' ";
		        $result=pdo_query($sql,$code);
		}
		//var_dump( $result);
		header("location:loginpage.php");


