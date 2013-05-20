<?php require("admin-header.php");

if (!(isset($_SESSION['administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
function writable($path){
	$ret=false;
	$fp=fopen($path."/testifwritable.tst","w");
	$ret=!($fp===false);
	fclose($fp);
	unlink($path."/testifwritable.tst");
	return $ret;
}
?>
<?php if(isset($_POST['do'])){
	require_once("../include/check_post_key.php");
	if (isset($_POST['from'])){
		$from=intval($_POST['from']);
		$to=intval($_POST['to']);
		$row=0;
		if($result=mysql_query("select 1 from problem where problem_id=$to")){
			$row=mysql_num_rows($result);
			mysql_free_result($result);
		}
		
		if($row==0&&rename("$OJ_DATA/$from","$OJ_DATA/$to")){
			$sql="UPDATE `problem` SET `problem_id`=$to WHERE `problem_id`=".$from;
			if(!mysql_query($sql)){
				 rename("$OJ_DATA/$to","$OJ_DATA/$from");
				 exit(1);
			}
			$sql="UPDATE `solution` SET `problem_id`=$to WHERE `problem_id`=".$from;
			if(!mysql_query($sql)){
				 rename("$OJ_DATA/$to","$OJ_DATA/$from");
				 exit(1);
			}
			$sql="UPDATE `contest_problem` SET `problem_id`=$to WHERE `problem_id`=".$from;
			if(!mysql_query($sql)){
				 rename("$OJ_DATA/$to","$OJ_DATA/$from");
				 exit(1);
			}
			$sql="UPDATE `topic` SET `pid`=$to WHERE `pid`=".$from;
			if(!mysql_query($sql)){
				 rename("$OJ_DATA/$to","$OJ_DATA/$from");
				 exit(1);
			}
			$sql="select max(problem_id) from problem";
			if($result=mysql_query($sql)){
				$f=mysql_fetch_array($result);
				$nextid=$f[0]+1;
				mysql_free_result($result);
				$sql="ALTER TABLE problem AUTO_INCREMENT = $nextid";
				mysql_query($sql);
			}
			
			echo "done!";
		}else{
			
				echo "fail...";
		}

	}
}

 $show_form=true;
   if(!isset($OJ_SAE)||!$OJ_SAE){
	   if(!writable($OJ_DATA)){
		   echo " You need to add  $OJ_DATA into your open_basedir setting of php.ini,<br>
					or you need to execute:<br>
					   <b>chmod 775 -R $OJ_DATA && chgrp -R www-data $OJ_DATA</b><br>
					you can't use import function at this time.<br>"; 
			$show_form=false;
	   }
	}	
	if($show_form){
?>

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
			<div class="col align-center changeid">
				<div class="news-header">Change ProblemID<hr/></div>

					MOVE THE PROBLEM ID FROM A TO B
					<form action='problem_changeid.php' method=post>
						Move <input type=input name='from'><b> -></b>
						<input type=input name='to'>
						<input type='hidden' name='do' value='do'>
						<input type=submit value=submit>
						<?php require_once("../include/set_post_key.php");?>
					</form>
					
				<?php }
				?>
			</div>
		</div>
	</div>
</div>

</body>
</html>