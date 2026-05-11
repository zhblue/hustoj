<?php require_once("admin-header.php");
require_once "../include/email.class.php";
require_once("../include/check_get_key.php");
$user_id=$_GET['cid'];
//echo htmlentities($user_id,ENT_QUOTE,'UTF-8');
if(!isset($_SESSION[$OJ_NAME.'_'.'administrator'])) exit();
if($_SESSION[$OJ_NAME."_user_id"]==$user_id){
        echo "Can't defunct yourself!";
        exit(0);
}
$sql="select `defunct`,email FROM `users` WHERE `user_id`=?";
$result=pdo_query($sql,$user_id);
$num=count($result);
if ($num<1){
        echo "No Such User!";
        require_once("../oj-footer.php");
        exit(0);
}
$row=$result[0];
if ($row[0]=='N'){
        $sql="UPDATE `users` SET `defunct`='Y' WHERE `user_id`=?";
        pdo_query($sql,$user_id);
}else{
        $sql="UPDATE `users` SET `defunct`='N' WHERE `user_id`=?";
        pdo_query($sql,$user_id);
        if(isset($OJ_SaaS_ENABLE) && $OJ_SaaS_ENABLE && $domain == $DOMAIN ){
                create_subdomain($user_id,'syzoj',3);
                $address=$row['email'];
                $mailtitle = "$OJ_NAME 账号重置激活通知";//邮件主题
                $mailcontent = "$user_id:\n您好！\n
                                              您在".$DOMAIN."系统开通了子OJ，您的子系统域名是".$user_id.".".$DOMAIN."。\n
                                              请及时访问该系统，并注册一个新账号".$user_id."，自动成为管理员。\n\n\n
                                                                                        $OJ_NAME 在线评测系统";//邮件内容
                email($address,$mailtitle,$mailcontent);

        }
?>
        <a href="user_list.php">Back</a>
<?php
}
?>

<script language=javascript>
        history.go(-1);
</script>
