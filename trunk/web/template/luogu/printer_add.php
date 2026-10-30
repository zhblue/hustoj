<?php
  $show_title="$MSG_ERROR_INFO - $OJ_NAME";
  if(isset($OJ_MEMCACHE)) include(dirname(__FILE__)."/header.php");
  if($mark==100) {
  	$ui_class="positive";
  	$ui_icon="check";
  }else{
  	$ui_class="negative";
  	$ui_icon="remove";
  }
?>
	<div class="ui <?php echo $ui_class?> icon message">

 <center>
<form id=frmSolution action="printer.php" method="post">
<textarea style="width:80%" cols=180 rows=20 id="source" name="content">
</textarea><br>
<input type="submit" value="<?php echo $MSG_PRINTER?>">
		<?php require_once(dirname(__FILE__)."/../../include/set_post_key.php")?>
</form>
</center>
</div>

<?php include(dirname(__FILE__)."/footer.php");?>
