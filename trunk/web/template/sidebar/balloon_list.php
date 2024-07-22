<?php 
  $show_title="$MSG_ERROR_INFO - $OJ_NAME";
  include("template/$OJ_TEMPLATE/header.php");
?>
<div class="ui positive icon message">
  <div class="content">
    <div class="header" style="margin-bottom: 10px; " ondblclick='$(this).load("refresh-privilege.php")'>
	<form  style="float:left;display:inline" action="balloon.php" method="get">
                Contest ID:<input type="text" name="cid" value="<?php echo $cid?>" >
                <input type="submit" class="btn btn-primary" value="Check">
	</form>
	<div style="float:right;display:inline">
	 <form  style="float:left;" action="balloon.php?cid=<?php echo $cid?>" method="post" onsubmit="return confirm('Delete All Tasks?');">
                <input type="hidden" name="cid" value="<?php echo $cid?>" >
                <input type="hidden" name="clean" >
                <input type="submit" class='btn btn-danger' value="Clean">
		<?php require_once(dirname(__FILE__)."/../../include/set_post_key.php")?>
        </form>
	</div>
	<table class="table table-striped content-box-header">
<tr><td>id<td><?php echo $MSG_USER_ID?><td><?php echo $MSG_COLOR?><td><?php echo $MSG_STATUS?><td></tr>
<?php
foreach($view_balloon as $row){
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
      <!-- <p><%= err.details %></p> -->
    <p>
      <!-- <a href="<%= err.nextUrls[text] %>" style="margin-right: 5px; "><%= text %></a> -->
      
      <a href="javascript:history.go(-1)"><?php echo $MSG_BACK;?></a>
    </p>
  </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
