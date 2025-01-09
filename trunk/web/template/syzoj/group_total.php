<?php $show_title="$view_title - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<hr>
<div class='padding'>
<div ><h1><?php echo $MSG_GROUP_NAME.$MSG_STATISTICS ?></h1>
<?php
        if(!isset($_GET['down'])){
?>
<form action="<?php echo basename(__FILE__)?>" method=get ><?php echo $MSG_GROUP_NAME ?>
        <select name=group_name onchange="$('form').submit()">
                <?php
                        if(empty($group_name)) echo "<option value='' />";
                        $groups=pdo_query("select distinct group_name from users");
                        $groups=array_column($groups,'group_name');
                        foreach($groups as $group){
                                echo "<option value='".htmlentities($group)."' ". ($group==$group_name?"selected":"") ."   >$group</option>";
                        }
                ?>
        </select>
		
        <span class='ui button primary' onclick="$('body').html($('#statistics').parent().html()).css('overflow','scroll');">FullScreen</span>
        <span id="swapButton" type=button class='ui button red' > 矩阵转置/行列转换 </span>
        <a class='ui button green'  href="?group_name=<?php echo htmlentities($group_name)?>&down"><?php echo $MSG_DOWNLOAD ?></a>
	&nbsp;  &nbsp;  &nbsp;  &nbsp; <a href="javascript:history.go(-1);" >Back</a>

	
</form>
        <center>
<?php }

?>

</div>
<?php
if(!empty($plista)){
?>

        <?php
        echo "<table id='statistics' class='ui striped table'  >";
        echo "<thead><tr><th>$MSG_USER_ID</th><th>$MSG_NICK</th>";
        echo "<th>$MSG_AC</th>";
        foreach($plista as $plist){
                $name=$plist["name"];
                $list=$plist['list'];
                echo "<th><a href='group_statistics.php?list=".htmlentities($list)."&group_name=".htmlentities($group_name)."'>$name</a></th>";
        }
        echo "</tr></thead><tbody>";
        foreach($users as $user){
                $sql="select DISTINCT problem_id sb FROM `solution` WHERE `user_id`=?  $not_in_noip ";
                $sb=mysql_query_cache($sql,$user['user_id']) ;
                $sb=array_column($sb,'sb');
                $sql="select DISTINCT problem_id ac FROM `solution` WHERE `user_id`=? AND `result`=4 $not_in_noip ";
                $ac=mysql_query_cache($sql,$user['user_id']) ;
                $ac=array_column($ac,'ac');
                $pass=0;
                echo "<tr>";
                echo "<td><a href='userinfo.php?user=".htmlentities($user['user_id'])."'>".$user['user_id']."</a></td>";
                echo "<td><a href='userinfo.php?user=".htmlentities($user['user_id'])."'>".$user['nick']."</a></td>";
                $line="";
                foreach($plista as $plist){
                        $line.="<td>";
                        $name=$plist["name"];
                        $list=explode(",",$plist['list']);
                        foreach($list as $pid){
                                $color='white';
                                if(in_array($pid,$sb)) $color='red';
                                if(in_array($pid,$ac)) {$color='green';$pass++;}
                                else if(isset($_GET['down'])) continue;
                                $line.= "<a class='ui $color label mini' href='problem.php?id=$pid'>".$bible[$pid]."</a>\n";
                                if(isset($_GET['down'])) echo "<br>\n";
                        }
                        $line.= "</td>";
                }
                echo "<td>".$pass."</td>";
                echo $line;
                echo "</tr>";
        }
        echo "</tbody></table>";
        ?>

<?php }
	if(!isset($_GET['down'])){	
?>
		

</center>
<button id="swapButton">矩阵转置/行列转换</button>	

<script>
$(document).ready(function() {
    $('#swapButton').click(function() {
        var originalTable = $('#statistics');
        var newTable = $('<table class="ui table striped"></table>');

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
		$('#main').removeClass("container");
                $('#statistics').parent().css("overflow-x","scroll");

    	  });
      </script>
<?php 	} ?>
