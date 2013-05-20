<?php require_once("admin-header.php");
	if(isset($OJ_LANG)){
		require_once("../lang/$OJ_LANG.php");
	}
?>

<?php
	require_once("../include/const.inc.php");
$description="";
 if (isset($_POST['syear']))
{
	
	require_once("../include/db_info.inc.php");
	require_once("../include/check_post_key.php");
	
	$starttime=intval($_POST['syear'])."-".intval($_POST['smonth'])."-".intval($_POST['sday'])." ".intval($_POST['shour']).":".intval($_POST['sminute']).":00";
	$endtime=intval($_POST['eyear'])."-".intval($_POST['emonth'])."-".intval($_POST['eday'])." ".intval($_POST['ehour']).":".intval($_POST['eminute']).":00";
	//	echo $starttime;
	//	echo $endtime;

        $title=$_POST['title'];
        $private=$_POST['private'];
        $description=$_POST['description'];
        if (get_magic_quotes_gpc ()){
                $title = stripslashes ($title);
                $private = stripslashes ($private);
                $description = stripslashes ($description);
        }

	$title=mysql_real_escape_string($title);
	$private=mysql_real_escape_string($private);
	$description=mysql_real_escape_string($description);
	
    $lang=$_POST['lang'];
    $langmask=0;
    foreach($lang as $t){
			$langmask+=1<<$t;
	} 
	$langmask=1023&(~$langmask);
	//echo $langmask;	
	
	$sql="INSERT INTO `contest`(`title`,`start_time`,`end_time`,`private`,`langmask`,`description`)
		VALUES('$title','$starttime','$endtime','$private',$langmask,'$description')";
//	echo $sql;
	mysql_query($sql) or die(mysql_error());
	$cid=mysql_insert_id();
	echo "Add Contest ".$cid;
	$sql="DELETE FROM `contest_problem` WHERE `contest_id`=$cid";
	$plist=trim($_POST['cproblem']);
	$pieces = explode(",",$plist );
	if (count($pieces)>0 && strlen($pieces[0])>0){
		$sql_1="INSERT INTO `contest_problem`(`contest_id`,`problem_id`,`num`) 
			VALUES ('$cid','$pieces[0]',0)";
		for ($i=1;$i<count($pieces);$i++){
			$sql_1=$sql_1.",('$cid','$pieces[$i]',$i)";
		}
		//echo $sql_1;
		mysql_query($sql_1) or die(mysql_error());
		$sql="update `problem` set defunct='N' where `problem_id` in ($plist)";
		mysql_query($sql) or die(mysql_error());
	}
	$sql="DELETE FROM `privilege` WHERE `rightstr`='c$cid'";
	mysql_query($sql);
	$sql="insert into `privilege` (`user_id`,`rightstr`)  values('".$_SESSION['user_id']."','m$cid')";
	mysql_query($sql);
	$_SESSION["m$cid"]=true;
	$pieces = explode("\n", trim($_POST['ulist']));
	if (count($pieces)>0 && strlen($pieces[0])>0){
		$sql_1="INSERT INTO `privilege`(`user_id`,`rightstr`) 
			VALUES ('".trim($pieces[0])."','c$cid')";
		for ($i=1;$i<count($pieces);$i++)
			$sql_1=$sql_1.",('".trim($pieces[$i])."','c$cid')";
		//echo $sql_1;
		mysql_query($sql_1) or die(mysql_error());
	}
	echo "<script>window.location.href=\"contest_list.php\";</script>";
}
else{
	
   if(isset($_GET['cid'])){
		   $cid=intval($_GET['cid']);
		   $sql="select * from contest WHERE `contest_id`='$cid'";
		   $result=mysql_query($sql) or die(mysql_error());
		   $row=mysql_fetch_object($result);
		   $title=$row->title;
		   mysql_free_result($result);
			$plist="";
			$sql="SELECT `problem_id` FROM `contest_problem` WHERE `contest_id`=$cid ORDER BY `num`";
			$result=mysql_query($sql) or die(mysql_error());
			for ($i=mysql_num_rows($result);$i>0;$i--){
				$row=mysql_fetch_row($result);
				$plist=$plist.$row[0];
				if ($i>1) $plist=$plist.',';
			}
			mysql_free_result($result);
   }
else if(isset($_POST['problem2contest'])){
	   $plist="";
	   //echo $_POST['pid'];
	   sort($_POST['pid']);
	   foreach($_POST['pid'] as $i){		    
			if ($plist) 
				$plist.=','.$i;
			else
				$plist=$i;
	   }
}else if(isset($_GET['spid'])){
	require_once("../include/check_get_key.php");
		   $spid=intval($_GET['spid']);
		 
			$plist="";
			$sql="SELECT `problem_id` FROM `problem` WHERE `problem_id`>=$spid ";
			$result=mysql_query($sql) or die(mysql_error());
			for ($i=mysql_num_rows($result);$i>0;$i--){
				$row=mysql_fetch_row($result);
				$plist=$plist.$row[0];
				if ($i>1) $plist=$plist.',';
			}
			mysql_free_result($result);
}  
  include_once("../fckeditor/fckeditor.php") ;
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
			<div class="marginTop">
				<form method="POST"  class="add-contest-p">
				<p><font style="font-size:22px;" color="#333399">Add a Contest</font></p>
				<p><strong>Title:</strong><input class=input-xxlarge  type="text" name=title size=71 value="<?php echo isset($title)?$title:""?>"></p>
				<p>Start Time:</p>
				<p>
					Year:<input  class="input-mini" type="text" name=syear value=<?php echo date('Y')?> size=4 >
					Month:<input class="input-mini"  type="text" name=smonth value=<?php echo date('m')?> size=2 >
					Day:<input class="input-mini" type="text" name=sday size=2 value=<?php echo date('d')?> >
					Hour:<input class="input-mini"    type="text" name=shour size=2 value=<?php echo date('H')?>>
					Minute:<input class="input-mini"    type="text" name=sminute value=00 size=2 >
				</p>
				<p>End Time:</p>
				<p>
					Year:<input class="input-mini"    type="text" name=eyear value=<?php echo date('Y')?> size=4 >
					Month:<input class="input-mini"    type="text" name=emonth value=<?php echo date('m')?> size=2 >
					Day:<input class="input-mini"  type="text" name=eday size=2 value=<?php echo date('d')+(date('H')+4>23?1:0)?>>
					Hour:<input class="input-mini"  type="text" name=ehour size=2 value=<?php echo (date('H')+4)%24?>>
					Minute:<input class="input-mini"  type="text" name=eminute value=00 size=2 >
				</p>
				<div class="row-fluid">
					<div style="text-align:center;margin-left:auto;margin-right:auto">
					<div align="right" class="span6" style="width:45%">
						<p style="text-align:right;">Public:</p>
						<select name=private>
							<option value=0>Public</option>
							<option value=1>Private</option>
						</select>
					<p style="text-align:right;">Language:</p><select name="lang[]" multiple="multiple"    style="height:220px">
					<?php
						$lang_count=count($language_ext);

						 $langmask=$OJ_LANGMASK;

						 for($i=0;$i<$lang_count;$i++){
						    echo "<option value=$i selected>
						        ".$language_name[$i]."
						    </option>";
						  }

					?>


				    </select>
					</div>
					<div class="span6" style="text-align:left;width:45%">
						<p style="text-align:left;padding-left:3%;">Users:</p><textarea name="ulist" rows="20" cols="20"></textarea>
					</div>
					</div>
				</div>
				<?php require_once("../include/set_post_key.php");?>
				<div align="center">Problems:<input class=input-xxlarge type="text" size=60 name=cproblem value="<?php echo isset($plist)?$plist:""?>"></div>
				<p align="center">Description:<br>

				<?php
					$fck_description = new FCKeditor('description') ;
					$fck_description->BasePath = '../fckeditor/' ;
					$fck_description->Height = 370 ;
					$fck_description->Width=600;
					$fck_description->Value = $description ;
					$fck_description->Create() ;
				?>
				

				<div class="align-center">*可以将学生学号从Excel整列复制过来，然后要求他们用学号做UserID注册,就能进入Private的比赛作为作业和测验。</div>
				<p class="btn-group" style="text-align:center;"><input class="btn" type="submit" value="Submit" name="submit"><input type="reset" class="btn" value="Reset" name="reset"></p>
				</form>
			<?php }

			?>


			</div>
		</div>
	</div>
</div>

</body>
</html>


	
