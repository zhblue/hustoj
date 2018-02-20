<?php require("admin-header.php");
        if(isset($OJ_LANG)){
                require_once("../lang/$OJ_LANG.php");
        }
require_once("../include/set_get_key.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])
                ||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])
                ||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])
                )){
        echo "<a href='../loginpage.php'>Please Login First!</a>";
        exit(1);
}
if(isset($_GET['keyword']))
	$keyword=$_GET['keyword'];
else
	$keyword="";
$sql="SELECT max(`problem_id`) as upid FROM `problem`";
$page_cnt=100;
$result=pdo_query($sql);
$row=$result[0];
$cnt=intval($row['upid'])-1000;
$cnt=intval($cnt/$page_cnt)+(($cnt%$page_cnt)>0?1:0);
if (isset($_GET['page'])){
        $page=intval($_GET['page']);
}else $page=$cnt;
$pstart=1000+$page_cnt*intval($page-1);
$pend=$pstart+$page_cnt;
echo "<div class='container'>";
echo "<form action=problem_list.php>";
echo "<select class='input-mini' onchange=\"location.href='problem_list.php?page='+this.value;\">";
for ($i=1;$i<=$cnt;$i++){
        if ($i>1) echo '&nbsp;';
        if ($i==$page) echo "<option value='$i' selected>";
        else  echo "<option value='$i'>";
        echo $i+9;
        echo "**</option>";
}
echo "</select>";
$sql="";
if($keyword) {
	$keyword="%$keyword%";
	$sql="select `problem_id`,`title`,`accepted`,`in_date`,`defunct` FROM `problem` where title like ? or source like ?";
	$result=pdo_query($sql,$keyword,$keyword);
}else{
	$sql="select `problem_id`,`title`,`accepted`,`in_date`,`defunct`,`order_num` FROM `problem` where problem_id>=? and problem_id<=? order by `order_num` asc,`problem_id` desc";
	$result=pdo_query($sql,$pstart,$pend);
}
?>
<form action=problem_list.php>

<input name=keyword><input type=submit value="<?php echo $MSG_SEARCH?>" ></form>

<?php
echo "<center><table class='table table-striped' width=90% border=1>";
echo "<form method=post action=contest_add.php>";
echo "<tr><td colspan=9>";
echo "<input type=checkbox onchange='$(\"input[type=checkbox]\").prop(\"checked\", this.checked)'>";
echo "<input type=submit name='problem2contest' value='CheckToNewContest'>";
echo "<tr><td>PID<td>Title<td>AC<td>Date";
if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])){
        if(isset($_SESSION[$OJ_NAME.'_'.'administrator']))   echo "<td>Status<td>Delete";
        echo "<td>Edit<td>TestData<td>OrderNum</tr>";
}
foreach($result as $row){
        echo "<tr>";
        echo "<td>".$row['problem_id'];
        echo "<input type=checkbox name='pid[]' value='".$row['problem_id']."'>";
        echo "<td><a href='../problem.php?id=".$row['problem_id']."'>".$row['title']."</a>";
        echo "<td>".$row['accepted'];
        echo "<td>".$row['in_date'];
  if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])){
                if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])){
                        echo "<td><a href=problem_df_change.php?id=".$row['problem_id']."&getkey=".$_SESSION[$OJ_NAME.'_'.'getkey'].">"
                        .($row['defunct']=="N"?"<span titlc='click to reserve it' class=green>Available</span>":"<span class=red title='click to be available'>Reserved</span>")."</a><td>";
                        if($OJ_SAE||function_exists("system")){
                              ?>
                              <a href=# onclick='javascript:if(confirm("Delete?")) location.href="problem_del.php?id=<?php echo $row['problem_id']?>&getkey=<?php echo $_SESSION[$OJ_NAME.'_'.'getkey']?>";'>
                              Delete</a>
                              <?php
                        }
                }
                if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'."p".$row['problem_id']])){
                        echo "<td><a href=problem_edit.php?id=".$row['problem_id']."&getkey=".$_SESSION[$OJ_NAME.'_'.'getkey'].">Edit</a>";
			echo "<td><a href='javascript:phpfm(".$row['problem_id'].");'>TestData</a>";
			echo "<td>".$row['order_num'];
                }
        }
        echo "</tr>";
}
echo "<tr><td colspan=9><input type=submit name='problem2contest' value='CheckToNewContest'>";
echo "</tr></form>";
echo "</table></center>";
?>
<script src='../template/bs3/jquery.min.js' ></script>
<script>
function phpfm(pid){
        //alert(pid);
        $.post("phpfm.php",{'frame':3,'pid':pid,'pass':''},function(data,status){
                if(status=="success"){
                        document.location.href="phpfm.php?frame=3&pid="+pid;
                }
        });
}
</script>
</div>
