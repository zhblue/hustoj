<html>
	<head>
		<title>OJ Administration</title>
		<meta http-equiv="Pragma" content="no-cache">
		<meta http-equiv="Cache-Control" content="no-cache">
		<meta http-equiv="Content-Language" content="zh-cn">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel=stylesheet href='admin.css' type='text/css'>
	</head>
<body>



<div class="container-fluid">
	<?php require_once("admin-bar.php"); ?>
	<div class="row-fluid top-space">
		<div class="span2" >
			<div class="menu-group"  >
				<?php require_once("menu.php") ?>
			</div>
		</div>
		<div class="span10">
			<div class="align-center" style="margin-left:auto;margin-right:auto;margin-top:20px;" >
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
				<div style="width:60%" class=" bk">
					<P>Import FPS data ,please make sure you file is smaller than [<?php echo $maxfile?>] </P>
					<P>or set upload_max_filesize and post_max_size in PHP.ini</P>
					<P>if you fail on import big files[10M+],try enlarge your [memory_limit]  setting in php.ini.</P>
				</div>
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
					   if(!writable("../upload")){
						   echo "../upload is not writable, <b>chmod 770</b> to it.<br>";
						   $show_form=false;
					   }
					}	
					if($show_form){
				?>
				<br><div  style="font-size:24px;margin-bottom: 10px;"><strong>Import Problem:</strong></div>
				<form class="button-align" action='problem_import_xml.php' method=post enctype="multipart/form-data">
					
					
					<input type=file name=fps>
					<?php require_once("../include/set_post_key.php");?>
				    <input type=submit value='Import' class="btn">
				</form>
				<?php 
				  
				   	}
				   
				?>
				<br>

				free problem set FPS-xml can be download at <a href=http://code.google.com/p/freeproblemset/downloads/list>FPS-Googlecode</a>

			</div>
		</div>
	</div>
</div>

</body>
</html>

