<?php require_once("admin-header.php");
require_once("../include/check_get_key.php");
$cid=intval($_GET['cid']);
if(!(isset($_SESSION[$OJ_NAME.'_'."m$cid"]) || isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'contest_creator']))) exit();
$sql="select `defunct` FROM `contest` WHERE `contest_id`=?";
$result=pdo_query($sql,$cid);
$num=count($result);
if ($num<1){
	
	echo "No Such Contest!";
	require_once("../oj-footer.php");
	exit(0);
}
$row=$result[0];
if ($row[0]=='N'){
	$sql="UPDATE `contest` SET `defunct`='Y' WHERE `contest_id`=?";
}else{ 
	$sql="UPDATE `contest` SET `defunct`='N' WHERE `contest_id`=?";
}
pdo_query($sql,$cid);



?>
<script language=javascript>
	history.go(-1);
</script>

