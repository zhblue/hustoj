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

    <?php include("template/$OJ_TEMPLATE/nav.php");?>	    
      <!-- Main component for a primary marketing message or call to action -->
      <div class="container">
	 <form action="printer.php" method="post"  onsubmit="return confirm('Delete All Tasks?');">
                <input type="hidden" name="clean" >
                <input type="submit" class='btn btn-danger' value="Clean">
		<?php require_once(dirname(__FILE__)."/../../include/set_post_key.php")?>
        </form>

	<table class="table table-striped content-box-header">
<tr><td>id<td><?php echo $MSG_USER_ID?><td><?php echo $MSG_STATUS?><td></tr>
<?php
foreach($view_printer as $row){
	echo "<tr>\n";
	foreach($row as $table_cell){
		echo "<td>";
		echo $table_cell;
		echo "</td>";
	}
		$i++;
	echo "</tr>\n";
}
?>
</table>

        <p>
        </p>
      </div>

</div>

<?php include(dirname(__FILE__)."/footer.php");?>
