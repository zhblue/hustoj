<?php
        require_once('./include/db_info.inc.php');
        require_once('./include/setlang.php');
        $view_title= "Welcome To Online Judge";

	require_once("./include/const.inc.php");
	require_once("./include/my_func.inc.php");
	$lost_user_id=$_POST['user_id'];
	$lost_email=$_POST['email'];
    if(isset($_POST['vcode']))$vcode=trim($_POST['vcode']);
    if($lost_user_id&&($vcode!= $_SESSION[$OJ_NAME.'_'."vcode"]||$vcode==""||$vcode==null) ){
                echo "<script language='javascript'>\n";
                echo "alert('Verify Code Wrong!');\n";
                echo "history.go(-1);\n";
                echo "</script>";
                exit(0);
    }
  if(false){
        $lost_user_id=stripslashes($lost_user_id);
        $lost_email=stripslashes($lost_email);
  }
  $sql="SELECT `email` FROM `users` WHERE `user_id`=? and defunct='N' ";
                $result=pdo_query($sql,$lost_user_id);
                $row = $result[0];
 if($row && $row['email']==$lost_email&&strpos($lost_email,'@')){
   $_SESSION[$OJ_NAME.'_'.'lost_user_id']=$lost_user_id;
   $_SESSION[$OJ_NAME.'_'.'lost_key']=getToken(16);

  
	require_once ("include/email.class.php");     // 新版本的邮件发送信息请填写到这个文件中
/* 弃用留存，不做修改
	//******************** 配置信息 ********************************
	$smtpserver = "smtp.qiye.aliyun.com";//SMTP服务器
	$smtpserverport =25;//SMTP服务器端口
	$smtpusermail = "mailer@hustoj.com";//SMTP服务器的用户邮箱
	$smtpemailto = $row['email'];//发送给谁
	$smtpuser = "mailer@hustoj.com";//SMTP服务器的用户帐号
	$smtppass = "hustojmailer";//SMTP服务器的用户密码
邮箱信息备忘*/
	 
        $mailtitle = "OJ系统密码重置激活";//邮件主题
        $mailcontent = "$lost_user_id:\n您好！\n您通过[".basename($ip)."]在${OJ_NAME}系统选择了找回密码服务,为了验证您的身份,请将下面字串输入口令重置页面以确认身份:".$_SESSION[$OJ_NAME.'_'.'lost_key']."\n 注意：验证通过后，上面这个字符串将成为您的新密码!\n\n\nHUSTOJ在线评测系统";//邮件内容

/* 弃用存留，不做修改
        $mailtype = "TXT";//邮件格式（HTML/TXT）,TXT为文本邮件
        //************************ 配置信息 ****************************
        $smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
        $smtp->debug =false;//是否显示发送的调试信息
        $state = $smtp->sendmail($smtpemailto, $smtpusermail, $mailtitle, $mailcontent, $mailtype);
邮箱信息备忘*/

        email($row['email'],$mailtitle,$mailcontent);    

        require("template/".$OJ_TEMPLATE."/lostpassword2.php");

 }else{
    if( $row['email']!=$lost_email)$error_msg="用户名与Email不匹配";
/////////////////////////Template
   require("template/".$OJ_TEMPLATE."/lostpassword.php");

}
/////////////////////////Common foot
?>
