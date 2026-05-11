<?php
if ($_SESSION[$OJ_NAME.'_'.'getkey']!=$_GET['getkey']){
?>
<script language=javascript>
        history.go(-1);
</script>
<?php 
	exit(1);
}
else{
   unset($_SESSION[$OJ_NAME.'_'.'getkey']);
}
?>
