<?php
        require_once('./include/db_info.inc.php');
        require_once('./include/setlang.php');
	require_once("./include/const.inc.php");
	require_once("./include/my_func.inc.php");
	$code=$_GET['code'];
	if($OJ_EMAIL_CONFIRM && $code!=""){
	  	$sql="update `users` set defunct='N',activecode=''  WHERE `activecode`=? and `activecode`!='' and defunct='Y' ";
	        $result=pdo_query($sql,$code);
	}
	//var_dump( $result);
	header("location:loginpage.php");
?>
