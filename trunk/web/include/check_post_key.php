<?php
if (!isset($_SESSION[$OJ_NAME.'_'.'postkey'])||!isset($_POST['postkey'])||$_SESSION[$OJ_NAME.'_'.'postkey']!=$_POST['postkey']){
	echo "post key fail...";
	exit(1);
}
unset($_SESSION[$OJ_NAME.'_'.'postkey']);
?>
