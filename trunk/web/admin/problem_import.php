<?php function writable($path){
	$ret=false;
	$fp=fopen($path."/testifwritable.tst","w");
	$ret=!($fp===false);
	fclose($fp);
	unlink($path."/testifwritable.tst");
	return $ret;
}
require_once("admin-header.php");
if (!(isset($_SESSION['administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
   $maxfile=min(ini_get("upload_max_filesize"),ini_get("post_max_size"));

?>
Import FPS data ,please make sure you file is smaller than [<?php echo $maxfile?>] <br/>
or set upload_max_filesize and post_max_size in PHP.ini<br/>
if you fail on import big files[10M+],try enlarge your [memory_limit]  setting in php.ini.<br>
<?php 
    $show_form=true;
   if(!isset($OJ_SAE)||!$OJ_SAE){
	   if(!writable($OJ_DATA)){
		   echo " You need to add  $OJ_DATA into your open_basedir setting of php.ini,<br>
					or you need to execute:<br>
					   <b>chmod 775 -R $OJ_DATA && chgrp -R www-data $OJ_DATA</b><br>
					you can't use import function at this time.<br>"; 
			$show_form=false;
	   }
	   if(!file_exists("../upload"))mkdir("../upload");
	   if(!writable("../upload")){
	   	 
		   echo "../upload is not writable, <b>chmod 777</b> to it.<br>";
		   $show_form=false;
	   }
	}	
	if($show_form){
?>
<br>
<form action='problem_import_xml.php' method=post enctype="multipart/form-data">
	<b>Import Problem:</b><br />
	
	<input type=file name=fps >
	<?php require_once("../include/set_post_key.php");?>
    <input type=submit value='Import'>
</form>
<?php 
  
   	}
   
?>
<br>

free problem set FPS-xml can be download at <a href=http://code.google.com/p/freeproblemset/downloads/list>FPS-Googlecode</a>
