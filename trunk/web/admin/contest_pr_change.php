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
$private = intval($row[0]);
if ($private == 0)
  $next_private = 1;
else if ($private == 1)
  $next_private = 3;
else
  $next_private = 0;

pdo_query("UPDATE `contest` SET `private`=? WHERE `contest_id`=?",$next_private,$cid);
?>
<script language=javascript>
	history.go(-1);
</script>

