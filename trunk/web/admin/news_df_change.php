<?php require_once("admin-header.php");
require_once("../include/check_get_key.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
	exit(1);
}
?>
<?php $id=intval($_GET['id']);
$sql="SELECT `defunct` FROM `news` WHERE `news_id`=?";
$result=pdo_query($sql,$id);
$row=$result[0];
$defunct=$row[0];
echo $defunct;

if ($defunct=='Y') $sql="update `news` set `defunct`='N' where `news_id`=?";
else $sql="update `news` set `defunct`='Y' where `news_id`=?";
pdo_query($sql,$id) ;
?>
<script language=javascript>
	history.go(-1);
</script>
