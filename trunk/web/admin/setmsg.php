<?php require_once(dirname(__FILE__)."/admin-header.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
if(isset($_POST['do'])){
	require_once(dirname(__FILE__)."/../include/check_post_key.php");
	$fp=fopen($OJ_SAE?"saestor://web/msg.txt":dirname(__FILE__)."/../config/msg.txt","w");
	fputs($fp, stripslashes($_POST['msg']));
	fclose($fp);
	echo "Update At ".date('Y-m-d h:i:s');
}

$msg=file_get_contents($OJ_SAE?"saestor://web/msg.txt":dirname(__FILE__)."/../config/msg.txt");
include("kindeditor.php");

?>
<div class="container">
	<form action='setmsg.php' method='post'>
		<textarea name='msg' rows=25 class="kindeditor" ><?php echo $msg?></textarea><br>
		<input type='hidden' name='do' value='do'>
		<input type='submit' value='change'>
		<?php require_once(dirname(__FILE__)."/../include/set_post_key.php");?>
	</form>
</div>
<?php require_once(dirname(__FILE__)."/../oj-footer.php");
?>
