<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title><?php echo htmlentities(str_replace("\n\r","\n",$view_user),ENT_QUOTES,"utf-8")?></title>  
</head>
<body>
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
<script src="include/jquery-latest.js"></script>
<script>
  $("td:contains(<?php echo $view_user?>)").css("background-color","<?php echo $ball_color[$view_pid]?>");

</script>
</body>
