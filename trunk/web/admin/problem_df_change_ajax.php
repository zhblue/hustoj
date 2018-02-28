<?php 
require_once("../include/db_info.inc.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
	echo "Please Login First!";
	exit(1);
}
?>
<?php 
$ids = explode(',',$_POST['ids']);
foreach ($ids as $id)
{
	if($_POST['act'] =='Y')		$sql="update `problem` set `defunct`='Y' where `problem_id` = ? ";
	else		$sql="update `problem` set `defunct`='N' where `problem_id` = ? ";
	pdo_query($sql,intval($id)) ;
}
echo("success");
?>
