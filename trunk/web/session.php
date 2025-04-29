<?php
require_once(dirname(__FILE__)."/include/db_info.inc.php");
if(isset($_SESSION[$OJ_NAME."_user_id"])){
        $user_id=$_SESSION[$OJ_NAME."_user_id"];
        $defunct=pdo_query("select defunct from users where user_id=?",$user_id);
        if(!empty($defunct)&&$defunct[0][0]=="Y"){
                unset($_SESSION[$OJ_NAME.'_'.'user_id']);
                setcookie($OJ_NAME."_user","");
                setcookie($OJ_NAME."_check","");
                session_destroy();
?>
                <script>window.top.location.href="index.php";</script>
<?php
        }
}
// $OJ_LIP_URL="http://192.168.2.36/lip.php";
if (!empty($OJ_LIP_URL)){
        if(isset($_GET['lip'])){
                $lip=intval($_GET['lip']);
                setcookie("lip",$lip);
        }else if (!isset($_COOKIE['lip'])){
                echo "<script>window.location.href='$OJ_LIP_URL';</script> ";
        }
}
?>

   <script>window.setTimeout('window.location.reload();',300000);</script>
