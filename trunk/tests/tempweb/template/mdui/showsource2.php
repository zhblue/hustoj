<?php
if ($ok==true){

$brush=strtolower($language_name[$slanguage]);
if ($brush=='pascal') $brush='delphi';
if ($brush=='obj-c') $brush='c';
if ($brush=='c++') $brush='cpp';
if ($brush=='freebasic') $brush='vb';
if ($brush=='swift') $brush='csharp';
echo "<pre><code id=\"source-code\" class=\"language-".$brush.";\">";
ob_start();
echo "/**************************************************************\n";
echo "\tProblem: $sproblem_id\n\tUser: $suser_id\n";
echo "\tLanguage: ".$language_name[$slanguage]."\n\tResult: ".$judge_result[$sresult]."\n";
if ($sresult==4){
echo "\tTime:".$stime." ms\n";
echo "\tMemory:".$smemory." kb\n";
}
echo "****************************************************************/\n\n";
$auth=ob_get_contents();
ob_end_clean();
echo htmlentities(str_replace("\n\r","\n",$view_source),ENT_QUOTES,"utf-8")."\n".$auth."</code></pre>";
}else{
echo $MSG_WARNING_ACCESS_DENIED;
}
?>
