<?php $show_title=$id." - $MSG_SOURCE_CODE - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<div class="padding">
<link href='<?php echo $OJ_CDN_URL?>highlight/styles/shCore.css' rel='stylesheet' type='text/css'/>
<link href='<?php echo $OJ_CDN_URL?>highlight/styles/shThemeDefault.css' rel='stylesheet' type='text/css'/>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shCore.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushCpp.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushCss.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushJava.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushDelphi.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushRuby.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushBash.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushPython.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushPhp.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushPerl.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushCSharp.js' type='text/javascript'></script>
<script src='<?php echo $OJ_CDN_URL?>highlight/scripts/shBrushVb.js' type='text/javascript'></script>
<script language='javascript'>
SyntaxHighlighter.config.bloggerMode = false;
SyntaxHighlighter.config.clipboardSwf = 'highlight/scripts/clipboard.swf';
SyntaxHighlighter.all();
</script>
<?php
if ($ok==true){
$brush=strtolower($language_name[$slanguage]);
if ($brush=='pascal') $brush='delphi';
if ($brush=='clang') $brush='c';
if ($brush=='clang++') $brush='c++';
if ($brush=='obj-c') $brush='c';
if ($brush=='python3') $brush='python';
if ($brush=='swift') $brush='csharp';
if ($brush=='r') $brush='delphi';
echo "<pre class=\"brush:".$brush.";\">";
ob_start();
echo "/**************************************************************\n";
echo "\tProblem: $sproblem_id\n\tUser: $suser_id [$nick]\n";
echo "\tLanguage: ".$language_name[$slanguage]."\n\tResult: ".$judge_result[$sresult]."\n";
if ($sresult==4){
echo "\tTime:".$stime." ms\n";
echo "\tMemory:".$smemory." kb\n";
}
echo "****************************************************************/\n\n";
$auth=ob_get_contents();
ob_end_clean();
echo htmlentities(str_replace("\n\r","\n",$view_source),ENT_QUOTES,"utf-8")."\n".$auth."</pre>";
}else{
echo "I am sorry, You could not view this code!";
}
?>
  
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>

