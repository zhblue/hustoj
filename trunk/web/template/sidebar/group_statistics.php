<?php $show_title="$view_title - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<hr>
<div class='padding'>
<div ><h1><?php echo $MSG_GROUP_NAME.$MSG_STATISTICS ?></h1>
<form action=group_statistics.php method=get ><?php echo $MSG_GROUP_NAME ?>
	<select name=group_name onchange="$('form').submit()">
		<?php
                        if(empty($group_name)) echo "<option value='' />";
                        $groups=pdo_query("select distinct group_name from users where  defunct='N'  and expiry_date >= curdate() ");
                        $groups=array_column($groups,'group_name');
                        foreach($groups as $group){
                                echo "<option value='".htmlentities($group)."' ". ($group==$group_name?"selected":"") ."   >$group</option>";
                        }
		?>
	</select><a href="javascript:history.go(-1);" >Back</a> &nbsp;&nbsp;&nbsp;&nbsp; <a href="group_total.php?group_name=<?php echo htmlentities($group_name) ?>" >TotalView</a>
	<input type=hidden name=list value='<?php echo $pids ?>' >
	<span id="swapButton" type=button class='ui button red' > <?php echo $MSG_TABLE_TRANSPOSE ?> </span>
</form>
</div>
<?php
if(!empty($result)){
?>

<center>
  <table id="statistics"  width=100% border=1 style="text-align:center;" class="ui table striped">
      <thead>
    <tr>
    <?php
      echo "<th>$MSG_USER_ID</th>";
      echo "<th>$MSG_NICK</th>";
      echo "<th>$MSG_AC</th>";
  	foreach($pida as $pid){
      		echo "<th class='pid' value='$pid'><a href='problem.php?id=$pid' target=_blank >".$ptitle[$pid]."</a>";
      		echo "</th>";
	}
 // var_dump($result);
?> 
   </tr>
      </thead>
      <tbody>
<?php
    foreach($result as $row){
      echo "<tr>";
      echo "<td><a href='userinfo.php?user=".htmlentities($row['user_id'])."'>".$row['user_id']."</a></td>";
      echo "<td><a href='userinfo.php?user=".htmlentities($row['user_id'])."' target='_blank'>".$row['nick']."</a></td>";
        $ac=0;
  	foreach($pida as $pid){
      		if($row["P$pid"]==4) $ac++;
	}
      	echo "<td>$ac</td>";
  	foreach($pida as $pid){
      		echo "<td>";
      		if($row["P$pid"]==4) echo "<span class='label label-success label-sm' >AC</span>";
		else if($row["P$pid"]==15) echo "&nbsp;";
		else echo "<span class='label label-danger label-sm' >WA</span>";
      		echo "</td>";
	}
      echo "</tr>";
    }
  ?>
      </tbody>
</table>
<?php } ?>
</center>
<script>
        function setScale(scale) {
            $(".label.mini").css("transform",`scale(${scale})`);
        }

$(document).ready(function() {
    $('#swapButton').click(function() {
        var originalTable = $('#statistics');
        var newTable = $('<table id=statistics class="ui table striped"></table>');

        // Get all rows from the original table
        var rows = originalTable.find('tr');
        var row_count=rows.length;
        var col_count = $(rows[0]).find("th,td").length;
        console.log("row_count:"+row_count);
        console.log("col_count:"+col_count);
        // Create a new row for each column
        for(let i=0;i<col_count;i++){
                var newRow = $('<tr></tr>');
                for(let index=0;index<row_count;index++) {
                        // Create a new cell and add it to the new row
                    let cols=$(rows[index]).find('th,td');
                    let html=$(cols[i]).html();
                    var newCell = $('<td></td>').html(html);
                    newRow.append(newCell);
                }
                newTable.append(newRow);
        }
        originalTable.replaceWith(newTable);
    });


});
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
<script src="<?php echo $OJ_CDN_URL?>include/sortTable.js"></script>
      <script>
      	  $(document).ready(function(){
	  	console.log("sort");
    		sortTable('statistics', 2, 'int');	
    		sortTable('statistics', 2, 'int');	
		$('#swapButton').click();
		$('#swapButton').click();
    	  });
      </script>
