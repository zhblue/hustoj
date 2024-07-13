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
<link href='<?php echo $OJ_CDN_URL?>highlight/styles/shCore.css' rel='stylesheet' type='text/css'/>
<link href='<?php echo $OJ_CDN_URL?>highlight/styles/shThemeDefault.css' rel='stylesheet' type='text/css'/>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shCore.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushCpp.js' type='text/javascript'></script>
<script language='javascript'>
function draw(){
	SyntaxHighlighter.config.bloggerMode = false;
	SyntaxHighlighter.config.clipboardSwf = '<?php echo $OJ_CDN_URL?>highlight/scripts/clipboard.swf';
	SyntaxHighlighter.highlight();
}
</script>

<input onclick="draw()" type="button" value="Line Number">
<input onclick="window.print();" type="button" value="<?php echo $MSG_PRINTER?>">
<input onclick="location.href='printer.php?id=<?php echo $id?>';" type="button" value="<?php echo $MSG_PRINT_DONE?>"><br>
<?php
echo "<h2>".htmlentities(str_replace("\n\r","\n",$view_user),ENT_QUOTES,"utf-8")."\n";
echo "-".htmlentities(str_replace("\n\r","\n",$view_school),ENT_QUOTES,"utf-8")."-".htmlentities(str_replace("\n\r","\n",$view_nick),ENT_QUOTES,"utf-8")."\n"."</h2>";
echo "<pre class='brush:c'>".htmlentities(str_replace("\n\r","\n",$view_content),ENT_QUOTES,"utf-8")."\n"."</pre>";
?>
<input onclick="draw()" type="button" value="Line Number">
<input onclick="window.print();" type="button" value="<?php echo $MSG_PRINTER?>">
<input onclick="location.href='printer.php?id=<?php echo $id?>';" type="button" value="<?php echo $MSG_PRINT_DONE?>">
<img src="image/wx.jpg" height="100px" width="100px">
</body>
