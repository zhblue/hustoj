<?php require("admin-header.php");

if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
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
		if($result=pdo_query("select 1 from problem where problem_id=?",$to)){
			$row=count($result);
			
		}
		
		if($row==0&&rename("$OJ_DATA/$from","$OJ_DATA/$to")){
			$sql="UPDATE `problem` SET `problem_id`=? WHERE `problem_id`=?";
			if(pdo_query($sql,$to,$from)==0){
				 rename("$OJ_DATA/$to","$OJ_DATA/$from");
				 echo "fail...";
				 exit(1);
			}
			$sql="UPDATE `solution` SET `problem_id`=? WHERE `problem_id`=?";
			pdo_query($sql,$to,$from);
			$sql="UPDATE `contest_problem` SET `problem_id`=? WHERE `problem_id`=?";
			pdo_query($sql,$to,$from);
			$sql="UPDATE `topic` SET `pid`=? WHERE `pid`=?";
			pdo_query($sql,$to,$from);
			
			$sql="select max(problem_id) from problem";
			if($result=pdo_query($sql)){
				$f=$result[0];
				$nextid=$f[0]+1;
				$sql="ALTER TABLE problem AUTO_INCREMENT = $nextid";
				pdo_query($sql);
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
<b>Change ProblemID</b>
	<ol>
	<li>Problem
	<form action='problem_changeid.php' method=post>
		Move<input type=input name='from'>->
		<input type=input name='to'>
		<input type='hidden' name='do' value='do'>
		<input type=submit value=submit>
		<?php require_once("../include/set_post_key.php");?>
	</form>
	
<?php }
?>
