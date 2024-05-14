<?php 
	$cache_time=10;
	$OJ_CACHE_SHARE=false;
	require_once('./include/cache_start.php');
    require_once('./include/db_info.inc.php');
	require_once('./include/setlang.php');
	$view_title= "Welcome To Online Judge";
	require_once("./include/check_post_key.php");
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

if (!$_SESSION[$OJ_NAME.'_'.'user_id']||$OJ_TEMPLATE!="bshark") {
    die();
}
$user_id=$_SESSION[$OJ_NAME.'_'.'user_id'];
$school=trim($_POST['school']);
$nick=trim($_POST['nick']);
$len=strlen($nick);
if ($len>100){
	$err_str=$err_str."昵称太长!";
	$err_cnt++;
}else if ($len==0) $nick=$user_id;
$len=strlen($_POST['school']);
if ($len>100){
	$err_str=$err_str."学校太长";
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
$nick=htmlentities ($nick,ENT_QUOTES,"UTF-8");
$school=(htmlentities ($school,ENT_QUOTES,"UTF-8"));
$sql="UPDATE `users` SET"
."`nick`=?,"
."`school`=? ";
$sql.="WHERE `user_id`=?";
//echo $sql;
//exit(0);
pdo_query($sql,$nick,$school,$user_id);
?>
<script>
    alert("修改成功！");
    history.back(-1);
</script>
<?php
?>
