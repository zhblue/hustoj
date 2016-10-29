<?php
require_once("./include/db_info.inc.php");
$vcode=trim($_POST['vcode']);
$nextUrl=trim($_GET['url']);
if ($OJ_VCODE&&($vcode!= $_SESSION["vcode"]||$vcode==""||$vcode==null)) {
  echo "<script language='javascript'>\n";
  echo "alert('Verify Code Wrong!');\n";
  echo "history.go(-1);\n";
  echo "</script>";
  exit(0);
}

require_once("./include/login-".$OJ_LOGIN_MOD.".php");
$user_id=$_POST['user_id'];
$password=$_POST['password'];
if (get_magic_quotes_gpc ()) {
  $user_id= stripslashes ( $user_id);
  $password= stripslashes ( $password);
}

$sql="SELECT `rightstr` FROM `privilege` WHERE `user_id`='".mysql_real_escape_string($user_id)."'";
$result=mysql_query($sql);
$login=check_login($user_id,$password);

if ($login) {
  $_SESSION['user_id']=$login;

  echo mysql_error();
  while ($result&&$row=mysql_fetch_assoc($result)) {
    $_SESSION[$row['rightstr']]=true;
  }
  echo "<script language='javascript'>\n";
  if ($nextUrl) {
    echo "location.href='$nextUrl';\n";
  } else {
    echo "location.href='\\';\n";
  }
  echo "</script>";
} else {
  echo "<script language='javascript'>\n";
  echo "alert('UserName or Password Wrong!');\n";
  echo "history.go(-1);\n";
  echo "</script>";
}
?>
