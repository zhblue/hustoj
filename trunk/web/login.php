<?php
require_once( "./include/db_info.inc.php" );
require_once( './include/setlang.php' );
$use_cookie=false;
$login=false;
if($OJ_LONG_LOGIN&&isset($_COOKIE[$OJ_NAME."_user"])&&isset($_COOKIE[$OJ_NAME."_check"])){
	$C_check=$_COOKIE[$OJ_NAME."_check"]; 
	$C_user=$_COOKIE[$OJ_NAME."_user"];
	$use_cookie=true;
	$C_num=strlen($C_check)-1;
	$C_num=($C_num*$C_num)%7;
	if($C_check[strlen($C_check)-1]!=$C_num){
		setcookie($OJ_NAME."_check","",0);
		setcookie($OJ_NAME."_user","",0);
		echo "<script>\n alert('Cookie失效或错误!(-1)'); \n history.go(-1); \n </script>";
		exit(0);
	} 
	$C_info=pdo_query("SELECT `password`,`accesstime` FROM `users` WHERE `user_id`=? and defunct='N'",$C_user)[0];
	$C_len=strlen($C_info[1]);
	for($i=0;$i<strlen($C_info[0]);$i++){
		$tp=ord($C_info[0][$i]);
		$C_res.=chr(39+($tp*$tp+ord($C_info[1][$i % $C_len])*$tp)%88);
	}
	if(substr($C_check,0,-1)==sha1($C_res))
		$login=$C_user;
	else{   
		setcookie($OJ_NAME."_check","",0);
		setcookie($OJ_NAME."_user","",0);
		echo "<script>\n alert('Cookie失效或错误!(-2)'); \n history.go(-1); \n </script>";
		exit(0);
	}
}
$vcode="";
if(!$use_cookie){
  if(isset($_POST[ 'vcode' ]))$vcode=trim($_POST['vcode']);
  if($OJ_VCODE&&( $vcode != $_SESSION[ $OJ_NAME . '_' . "vcode" ] || $vcode == "" || $vcode == null ) ) {
	$_SESSION[ $OJ_NAME . '_' . "vfail" ]=true;
	echo "<script language='javascript'>\n";
	echo "alert('Verify Code Wrong!');\n";
	echo "history.go(-1);\n";
	echo "</script>";
	exit( 0 );
  }
  $view_errors = "";
  require_once( "./include/login-" . $OJ_LOGIN_MOD . ".php" );
  $user_id = $_POST[ 'user_id' ];
  $password = $_POST[ 'password' ];
  if ( false ) {
	$user_id = stripslashes( $user_id );
	$password = stripslashes( $password );
  }
  $login = check_login( $user_id, $password );
}
if($login){
	//提取组名
  	$group_name="";
        $group_row=pdo_query("select group_name from users where user_id=?",$login);
        if(is_array($group_row)&&count($group_row)==1){
                $group_name=$group_row[0][0];
        }
        if($group_name==""){
                $sql = "SELECT * FROM `privilege` WHERE `user_id`=?";
                $_SESSION[ $OJ_NAME . '_' . 'user_id' ] = $login;
                $result = pdo_query( $sql, $login );
        }else{
                $sql = "SELECT * FROM `privilege` WHERE `user_id`=? or (user_id=? and rightstr like 'c%' )";
                $_SESSION[ $OJ_NAME . '_' . 'user_id' ] = $login;
                $result = pdo_query( $sql, $login ,$group_name);
        }
       // 对用户权限进行session转存
	foreach ( $result as $row ){
		if(isset($row[ 'valuestr' ]))
                        $_SESSION[ $OJ_NAME . '_' . $row[ 'rightstr' ] ] = $row[ 'valuestr' ];
                else
                        $_SESSION[ $OJ_NAME . '_' . $row[ 'rightstr' ] ] = true;
	}
        if(isset($_SESSION[ $OJ_NAME . '_vip' ])){  // VIP mark can access all [VIP] marked contest vip权限用户可以参加所有标记了[VIP]字样的比赛
		$sql="select contest_id from contest where title like '%[VIP]%'";
		$result=pdo_query($sql);
		foreach ($result as $row){
			$_SESSION[ $OJ_NAME . '_c' .$row['contest_id'] ] = true;
		}
	}
		
	$sql="update users set accesstime=now() where user_id=?";
        $result = pdo_query( $sql, $login );

	if($OJ_LONG_LOGIN){
		$C_info=pdo_query("SELECT `password` , `accesstime` FROM`users` WHERE`user_id`=? and defunct='N'",$login)[0];
		$C_len=strlen($C_info[1]);
		$C_res="";
		for($i=0;$i<strlen($C_info[0]);$i++){
			$tp=ord($C_info[0][$i]);
			$C_res.=chr(39+($tp*$tp+ord($C_info[1][$i % $C_len])*$tp)%88);
		}
		$C_res=sha1($C_res);
		$C_time=time()+86400*$OJ_KEEP_TIME;
		setcookie($OJ_NAME."_user",$login,$C_time);
		setcookie($OJ_NAME."_check",$C_res.(strlen($C_res)*strlen($C_res))%7,$C_time);
	}
	echo "<script language='javascript'>\n";
	if ($OJ_NEED_LOGIN)
		echo "window.location.href='index.php';\n";
	else
		echo "setTimeout('history.go(-2)',500);\n";
	echo "</script>";
} else {
	if ( $view_errors ) {
		require( "template/" . $OJ_TEMPLATE . "/error.php" );
	} else {
		echo "<script language='javascript'>\n";
		echo "alert('UserName or Password Wrong!');\n";
		echo "history.go(-1);\n";
		echo "</script>";
	}
}
?>
