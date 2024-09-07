<?php 
require_once("./include/db_info.inc.php");
if(isset($OJ_REGISTER)&&!$OJ_REGISTER) exit(0);
require_once("./include/my_func.inc.php");
require_once( './include/setlang.php' );
require_once("./include/email.class.php");     // 新版本的邮件发送信息请填写到这个文件中
if(isset($OJ_CSRF)&&$OJ_CSRF)require_once("./include/csrf_check.php");
$err_str="";
$err_cnt=0;
$len;
$user_id=trim($_POST['user_id']);
$len=strlen($user_id);
$email=trim($_POST['email']);
$school=trim($_POST['school']);
if(isset($OJ_VCODE)&&$OJ_VCODE)$vcode=trim($_POST['vcode']);
if($OJ_VCODE&&($vcode!= $_SESSION[$OJ_NAME.'_'."vcode"]||$vcode==""||$vcode==null) ){
	$_SESSION[$OJ_NAME.'_'."vcode"]=null;
	$err_str=$err_str."Verification Code Wrong!\\n";
	$err_cnt++;
	$_SESSION[ $OJ_NAME . '_' . "vfail" ]=true;
}
if($OJ_LOGIN_MOD!="hustoj"){
	$err_str=$err_str."System do not allow register.\\n";
	$err_cnt++;
}

if($len>48){ 
	$err_str=$err_str."User ID Too Long!\\n";
	$err_cnt++;
}else if ($len<3){
	$err_str=$err_str." $MSG_WARNING_USER_ID_SHORT\\n";
	$err_cnt++;
}
if (!is_valid_user_name($user_id)){
	$err_str=$err_str."User ID can only contain NUMBERs & LETTERs!\\n";
	$err_cnt++;
}
$nick=trim($_POST['nick']);
$len=strlen($nick);
if ($len>20){
	$err_str=$err_str."Nick Name Too Long!\\n";
	$err_cnt++;
}else if ($len==0) $nick=$user_id;

if(has_bad_words($user_id)){
        $err_str=$err_str.$MSG_USER_ID." Too Bad!\\n";
        $err_cnt++;
}
if(has_bad_words($school)){
        $err_str=$err_str.$MSG_SCHOOL." Too Bad!\\n";
        $err_cnt++;
}
if(has_bad_words($nick)){
        $err_str=$err_str.$MSG_NICK." Too Bad!\\n";
        $err_cnt++;
}

if (strcmp($_POST['password'],$_POST['rptpassword'])!=0){
	$err_str=$err_str."$MSG_WARNING_REPEAT_PASSWORD_DIFF!\\n";
	$err_cnt++;
}
if (strlen($_POST['password'])<6){
	$err_cnt++;
	$err_str=$err_str."$MSG_WARNING_PASSWORD_SHORT \\n";
}
$len=strlen($_POST['school']);
if ($len>20){
	$err_str=$err_str."School Name Too Long!\\n";
	$err_cnt++;
}
$len=strlen($_POST['email']);
if ($len>100){
	$err_str=$err_str."Email Too Long!\\n";
	$err_cnt++;
}
if ($err_cnt>0){
	print "<script language='javascript'>\n";
	print "alert('";
	print $err_str;
	print "');\n history.go(-1);\n</script>";
	exit(0);
	
}
$password=pwGen($_POST['password']);
$sql="SELECT `user_id` FROM `users` WHERE `users`.`user_id` = ?";
$result=pdo_query($sql,$user_id);
$rows_cnt=count($result);
if ($rows_cnt == 1){
	print "<script language='javascript'>\n";
	print "alert('User Existed!\\n');\n";
	print "history.go(-1);\n</script>";
	exit(0);
}
if ($domain==$DOMAIN && $OJ_NAME==$user_id){
        print "<script language='javascript'>\n";
        print "alert('User Existed!\\n');\n";
        print "history.go(-1);\n</script>";
        exit(0);
}
$nick=(htmlentities ($nick,ENT_QUOTES,"UTF-8"));
$school=(htmlentities ($school,ENT_QUOTES,"UTF-8"));
$email=(htmlentities ($email,ENT_QUOTES,"UTF-8"));
$ip = ($_SERVER['REMOTE_ADDR']);
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])&&!empty(trim($_SERVER['HTTP_X_FORWARDED_FOR']))) {
    $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR'];
    $tmp_ip = explode(',', $REMOTE_ADDR);
    $ip = (htmlentities($tmp_ip[0], ENT_QUOTES, "UTF-8"));
} else if (isset($_SERVER['HTTP_X_REAL_IP'])&&!empty(trim($_SERVER['HTTP_X_REAL_IP']))) {
    $REMOTE_ADDR = $_SERVER['HTTP_X_REAL_IP'];
    $tmp_ip = explode(',', $REMOTE_ADDR);
    $ip = (htmlentities($tmp_ip[0], ENT_QUOTES, "UTF-8"));
}

// 检查IP是否已经注册过
if (isset($OJ_REG_SPEED) && $OJ_REG_SPEED > 0 ) {

    // 查询最近1小时内该IP地址已经注册的用户数量
    $sql = "SELECT COUNT(*) FROM `users` WHERE (`ip` = ? or email = ? ) AND `reg_time` > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
    $result = pdo_query($sql, $ip,$email);
    $count = intval($result[0][0]);
    
    if ($count > $OJ_REG_SPEED ) {
        // 如果数量大于$OJ_REG_SPEED ，则表示该IP地址在最近1小时内已经注册过$OJ_REG_SPEED个账户
	$warning="$ip 正在快速注册大量新账号，请确认是否存在攻击行为。若能确认是攻击行为，可以用sudo iptables -A INPUT -s  $ip  -j DROP 命令 封禁IP。";
        email($OJ_ADMIN,"系统警告,疑似攻击!",$warning);
        print "<script language='javascript'>\n";
        print "alert('您的IP地址或Email已经注册过".$OJ_REG_SPEED."个账户，请稍后再试。\\n');\n";
        print "history.go(-1);\n</script>";
        exit(0);
    }
}
if(isset($OJ_EMAIL_CONFIRM) && $OJ_EMAIL_CONFIRM) 
	$_SESSION[$OJ_NAME.'_'.'activecode']=getToken(16);
else
	$_SESSION[$OJ_NAME.'_'.'activecode']="";

if(isset($OJ_REG_NEED_CONFIRM)&&$OJ_REG_NEED_CONFIRM) $defunct="Y";
else $defunct="N";
$sql="INSERT INTO `users`("
."`user_id`,`email`,`ip`,`accesstime`,`password`,`reg_time`,`nick`,`school`,`defunct`,activecode)"
."VALUES(?,?,?,NOW(),?,NOW(),?,?,?,?)";
$rows=pdo_query($sql,$user_id,$email,$ip,$password,$nick,$school,$defunct,$_SESSION[$OJ_NAME.'_'.'activecode']);// or die("Insert Error!\n");
//发送激活邮件
if (isset($OJ_EMAIL_CONFIRM) && $OJ_EMAIL_CONFIRM ) {
	$link= 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI'])."active.php?code=".$_SESSION[$OJ_NAME.'_'.'activecode'];
	email($email,"$MSG_ACTIVE_YOUR_ACCOUNT",
		"$MSG_CLICK_COPY $MSG_ACTIVE_YOUR_ACCOUNT $user_id :\n ".$link );

	$view_errors= "<div class='ui main container' ><font size=5 > $MSG_CHECK $email $MSG_EMAIL , $MSG_CLICK_COPY $MSG_ACTIVE_YOUR_ACCOUNT";
        $view_errors.="</font></div><hr><hr>";

        require("template/".$OJ_TEMPLATE."/error.php");
	exit(0);
}
$sql="INSERT INTO `loginlog`(user_id,password,ip,time) VALUES(?,?,?,NOW())";
pdo_query($sql,$user_id,"no save",$ip);

if(!isset($OJ_REG_NEED_CONFIRM)||!$OJ_REG_NEED_CONFIRM){
	$sql="SELECT `user_id` FROM `users` WHERE `users`.`user_id` = ?";
        $result=pdo_query($sql,$user_id);
        $rows_cnt=count($result);
        if ($rows_cnt == 1){
		$_SESSION[$OJ_NAME.'_'.'user_id']=$user_id;
		$sql="SELECT `rightstr` FROM `privilege` WHERE `user_id`=?";
		//echo $sql."<br />";
		$result=pdo_query($sql,$_SESSION[$OJ_NAME.'_'.'user_id']);
		foreach ($result as $row){
			$_SESSION[$OJ_NAME.'_'.$row['rightstr']]=true;
			//echo $_SESSION[$OJ_NAME.'_'.$row['rightstr']]."<br />";
		}
		$_SESSION[$OJ_NAME.'_'.'ac']=Array();
		$_SESSION[$OJ_NAME.'_'.'sub']=Array();
	        if($OJ_SaaS_ENABLE && $domain==$DOMAIN)  header("location:modifypage.php#MyOJ");
	}
}
?>
<script>history.go(-2);</script>
