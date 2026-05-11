<?php
/**
 * Created by IntelliJ IDEA.
 * User: anshi
 * Date: 2018/10/23
 * Time: 3:12 PM
 */

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("content-type:application/javascript");

if(isset($_SERVER['HTTP_REFERER'])) $dir=basename(dirname($_SERVER['HTTP_REFERER']));
else $dir="";

if($dir=="discuss3") $path_fix="../";
else $path_fix="";

require_once("../../include/db_info.inc.php");

if(isset($OJ_LANG)){
    require_once("../../lang/$OJ_LANG.php");
}else{
    require_once("../../lang/en.php");
}

function checkmail(){
    global $OJ_NAME;

    $sql="SELECT count(1) FROM `mail` WHERE new_mail=1 AND `to_user`=?";
    $result=pdo_query($sql,$_SESSION[$OJ_NAME.'_'.'user_id']);

    if(!$result) return false;

    $row=$result[0];
    $retmsg="<span id=red>(".$row[0].")</span>";

    return $retmsg;
}

$profile='';

if(isset($_SESSION[$OJ_NAME.'_'.'user_id'])){
    $sid=$_SESSION[$OJ_NAME.'_'.'user_id'];

//    <dd><a href="">移动模块</a></dd>

    $profile.= "<dd><a href=".$path_fix."modifypage.php>$MSG_REG_INFO</a></dd><dd><a href='".$path_fix."userinfo.php?user=$sid'><span id=red>$MSG_USERINFO</span></a></dd>";

    if((isset($OJ_EXAM_CONTEST_ID)&&$OJ_EXAM_CONTEST_ID>0)||
        (isset($OJ_ON_SITE_CONTEST_ID)&&$OJ_ON_SITE_CONTEST_ID>0)||
        (isset($OJ_MAIL)&&!$OJ_MAIL)){
    }
    else{
        $mail=checkmail();
        if($mail) $profile.= "<dd><a class='glyphicon glyphicon-envelope' href=".$path_fix."mail.php>$mail</a></dd>";
    }

    $profile.="<dd><a href='".$path_fix."status.php?user_id=$sid'><span id=red>$MSG_MY_SUBMISSIONS</span></a></dd>";
    $profile.="<dd><a href='".$path_fix."contest.php?my'><span id=red>$MSG_MY_CONTESTS</span></a></dd>";

    $profile.= "<dd><a href=".$path_fix."logout.php>$MSG_LOGOUT</a></dd>";

}else{
    if($OJ_WEIBO_AUTH){
        $profile.= "<dd><a href=".$path_fix."login_weibo.php>$MSG_LOGIN(WEIBO)</a></dd>";
    }
    if($OJ_RR_AUTH){
        $profile.= "<dd><a href=".$path_fix."login_renren.php>$MSG_LOGIN(RENREN)</a></dd>";
    }
    if ($OJ_QQ_AUTH){
        $profile.= "<dd><a href=".$path_fix."login_qq.php>$MSG_LOGIN(QQ)</a></dd>";
    }

    $profile.= "<dd><a href=".$path_fix."loginpage.php>$MSG_LOGIN</a></dd>";

//    if($OJ_LOGIN_MOD=="hustoj"){
//        if(isset($OJ_REGISTER)&&!$OJ_REGISTER){
//        }else{
//            $profile.= "<dd><a href=".$path_fix."registerpage.php>$MSG_REGISTER</a></dd>&nbsp;";
//        }
//    }
}

//$_SESSION[$OJ_NAME.'_'.'administrator'] = true;
//$_SESSION[$OJ_NAME.'_'.'balloon'] = true;

if(isset($_SESSION[$OJ_NAME.'_'.'balloon'])){
    $profile.= "<dd><a href='".$path_fix."balloon.php'>$MSG_BALLOON</a></dd>";
}

if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])){
    $profile.= "<dd><a href=".$path_fix."admin/>$MSG_ADMIN</a></dd>";
}

//$profile.="</ul></li>";
?>
document.write("<?php echo ( $profile);?>");
document.getElementById("profile").innerHTML="<?php echo  isset($sid)?$sid:$MSG_LOGIN  ?>";
