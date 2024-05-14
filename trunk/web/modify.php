<?php 
	//$cache_time=10;
	$OJ_CACHE_SHARE=false;
	//require_once('./include/cache_start.php');
        require_once('./include/db_info.inc.php');
	require_once('./include/setlang.php');
	$view_title= "Welcome To Online Judge";
	//require_once("./include/check_post_key.php");
	require_once("./include/my_func.inc.php");
if(
		(isset($OJ_EXAM_CONTEST_ID)&&$OJ_EXAM_CONTEST_ID>0)||
		(isset($OJ_ON_SITE_CONTEST_ID)&&$OJ_ON_SITE_CONTEST_ID>0)
   ){
		$view_errors= $MSG_MODIFY_NOT_ALLOWED_FOR_EXAM;
		require("template/".$OJ_TEMPLATE."/error.php");
		exit ();
}
$err_str="";
$err_cnt=0;
$len;
$user_id=$_SESSION[$OJ_NAME.'_'.'user_id'];
$email=trim($_POST['email']);
$school=trim($_POST['school']);
$nick=trim($_POST['nick']);
$len=strlen($nick);
if ($len>100){
	$err_str=$err_str."Nick Name Too Long!";
	$err_cnt++;
}else if ($len==0) $nick=$user_id;
$password=$_POST['opassword'];
$sql="SELECT `user_id`,`password` FROM `users` WHERE `user_id`=?";
$result=pdo_query($sql,$user_id);
 $row=$result[0];
if ($row && pwCheck($password,$row['password'])) $rows_cnt = 1;
else $rows_cnt = 0;

if ($rows_cnt==0){
	$err_str=$err_str."Old Password Wrong";
	$err_cnt++;
}
$len=strlen($_POST['npassword']);
if ($len<6 && $len>0){
	$err_cnt++;
	$err_str=$err_str."Password should be Longer than 6!\\n";
}else if (strcmp($_POST['npassword'],$_POST['rptpassword'])!=0){
	$err_str=$err_str."Two Passwords Not Same!";
	$err_cnt++;
}
$len=strlen($_POST['school']);
if ($len>100){
	$err_str=$err_str."School Name Too Long!";
	$err_cnt++;
}
$len=strlen($_POST['email']);
if ($len>100){
	$err_str=$err_str."Email Too Long!";
	$err_cnt++;
}

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
if ($err_cnt>0){
	print "<script language='javascript'>\n";
	echo "alert('";
	echo $err_str;
	print "');\n history.go(-1);\n</script>";
	exit(0);
	
}
if (strlen($_POST['npassword'])==0) $password=pwGen($_POST['opassword']);
else $password=pwGen($_POST['npassword']);
$nick=htmlentities ($nick,ENT_QUOTES,"UTF-8");
$school=(htmlentities ($school,ENT_QUOTES,"UTF-8"));
$email=(htmlentities ($email,ENT_QUOTES,"UTF-8"));
$sql="UPDATE `users` SET"
."`password`=?,"
."`nick`=?,"    //删除此行和81行含的,$nick禁用昵称修改
."`school`=?,"
."`email`=?"
."WHERE `user_id`=?"
;
//echo $sql;
//exit(0);
pdo_query($sql,$password,$nick,$school,$email,$user_id);
if($nick!=""){
	$sql="update solution set nick=? where user_id=?";
	pdo_query($sql,$nick,$user_id);
}
header("Location: ./");
?>
