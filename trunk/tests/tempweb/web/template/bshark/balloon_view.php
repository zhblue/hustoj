<?php 
  $show_title="$MSG_ERROR_INFO - $OJ_NAME";
  include("template/$OJ_TEMPLATE/header.php");
?>
<div class="ui positive icon message">
  <i class="check icon"></i>
  <div class="content">
<h1>Balloon Ticket</h1>
<?php
echo "<h2>".htmlentities(str_replace("\n\r","\n",$view_user),ENT_QUOTES,"utf-8")."\n";
echo "-".htmlentities(str_replace("\n\r","\n",$view_school),ENT_QUOTES,"utf-8")."-".htmlentities(str_replace("\n\r","\n",$view_nick),ENT_QUOTES,"utf-8")."\n"."</h2>";
echo "Problem ".$PID[$view_pid]."<br>";
if(isset($_GET['fb']) && intval($_GET['fb'])==1){
echo "Balloon Color: <font color='".$ball_color[$view_pid]."'>".$ball_name[$view_pid]." First Blood! </font><br>";
}else{
echo "Balloon Color: <font color='".$ball_color[$view_pid]."'>".$ball_name[$view_pid]."</font><br>";
}
?>
<input onclick="window.print();" type="button" value="<?php echo $MSG_PRINTER?>">
<input onclick="location.href='balloon.php?id=<?php echo $id?>&cid=<?php echo $cid?>';" type="button" value="<?php echo $MSG_PRINT_DONE?>">
<img src="image/wx.jpg" height="100px" width="100px">
<?php echo $view_map?>
<script src="<?php echo $OJ_CDN_URL?>template/bs3/jquery.min.js"></script>
<script>
  $("td:contains(<?php echo $view_user?>)").css("background-color","<?php echo $ball_color[$view_pid]?>");

</script>
  </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
