<?php require_once("admin-header.php");
require_once("../include/check_get_key.php");
$cid=intval($_GET['cid']);
	if(!(isset($_SESSION[$OJ_NAME.'_'."m$cid"]) || isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'contest_creator']))) exit();
$sql="select `private` FROM `contest` WHERE `contest_id`=?";
$result=pdo_query($sql,$cid);
$num=count($result);
if ($num<1){
	echo "No Such Problem!";
	
	exit(0);
}
$row=$result[0];
if (intval($row[0])==0) $sql="UPDATE `contest` SET `private`='1' WHERE `contest_id`=?";
else $sql="UPDATE `contest` SET `private`='0' WHERE `contest_id`=?";

pdo_query($sql,$cid);
?>
<script language=javascript>
	history.go(-1);
</script>

