<?php require_once("admin-header.php");
	if(isset($OJ_LANG)){
		require_once("../lang/$OJ_LANG.php");
	}
	if (!(isset($_SESSION['administrator'])||isset($_SESSION['problem_editor']))){
		echo "<a href='../loginpage.php'>Please Login First!</a>";
		exit(1);
	}
	require_once("../include/db_info.inc.php");
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
	<?php
	include_once("../fckeditor/fckeditor.php") ;
	?>

<div class="container-fluid">
	<?php require_once("admin-bar.php"); ?>
	<div class="row-fluid top-space">
		<div class="span2" >
			<div class="menu-group"  >
				<?php require_once("menu.php") ?>
			</div>
		</div>
		<div class="span10">
			<div class="">
				<div class="align-center">
					<div class="problem-add " style="margin-left:auto;margin-right:auto;">
					<div class="add-header" >
					<h1 >Add New problem</h1>
					</div>
					<form method=POST action=problem_add.php>
					<input type=hidden name=problem_id value="New Problem">
					<p align="center">Problem Id:&nbsp;&nbsp;New Problem</p>
					<p align="center">Title:<input class="input input-xxlarge" type=text name=title size=71></p>
					<p align="center">Time Limit:<input type=text name=time_limit size=20 value=1>S</p>
					<p align="center">Memory Limit:<input type=text name=memory_limit size=20 value=128>MByte</p>
					<p align="center">Description:<br><!--<textarea rows=13 name=description cols=80></textarea>-->

					<?php
					$description = new FCKeditor('description') ;
					$description->BasePath = '../fckeditor/' ;
					$description->Height = 250 ;
					$description->Width=800;

					$description->Value = '<p></p>' ;
					$description->Create() ;
					?>
					</p>

					<p align="center">Input:<br><!--<textarea rows=13 name=input cols=80></textarea>-->

					<?php
					$input = new FCKeditor('input') ;
					$input->BasePath = '../fckeditor/' ;
					$input->Height = 250 ;
					$input->Width=800;

					$input->Value = '<p></p>' ;
					$input->Create() ;
					?>
					</p>

					</p>
					<p align="center">Output:<br><!--<textarea rows=13 name=output cols=80></textarea>-->


					<?php
					$output = new FCKeditor('output') ;
					$output->BasePath = '../fckeditor/' ;
					$output->Height = 250 ;
					$output->Width=800;

					$output->Value = '<p></p>' ;
					$output->Create() ;
					?>

					</p>
					<p align="center">Sample Input:<br><textarea  class="input input-xxlarge"  rows=13 name=sample_input cols=80></textarea></p>
					<p align="center">Sample Output:<br><textarea  class="input input-xxlarge"  rows=13 name=sample_output cols=80></textarea></p>
					<p align="center">Test Input:<br><textarea  class="input input-xxlarge" rows=13 name=test_input cols=80></textarea></p>
					<p align="center">Test Output:<br><textarea  class="input input-xxlarge"  rows=13 name=test_output cols=80></textarea></p>
					<p align="center">Hint:<br>
					<?php
					$output = new FCKeditor('hint') ;
					$output->BasePath = '../fckeditor/' ;
					$output->Height = 250 ;
					$output->Width=800;

					$output->Value = '<p></p>' ;
					$output->Create() ;
					?>
					</p>
					<p>SpecialJudge: N<input type=radio name=spj value='0' checked>Y<input type=radio name=spj value='1'></p>
					<p align="center">Source:<br><textarea name=source rows=1 cols=70></textarea></p>
					<p align="center">contest:
						<select  name=contest_id>
					<?php $sql="SELECT `contest_id`,`title` FROM `contest` WHERE `start_time`>NOW() order by `contest_id`";
					$result=mysql_query($sql);
					echo "<option value=''>none</option>";
					if (mysql_num_rows($result)==0){
					}else{
						for (;$row=mysql_fetch_object($result);)
							echo "<option value='$row->contest_id'>$row->contest_id $row->title</option>";
					}
					?>
						</select>
					</p>
					<div align=center>
					<?php require_once("../include/set_post_key.php");?>
					<input type="submit" value="Submit" name="submit" class="btn">
					</div></form>
					<p>

					</div>
					</div>
			</div>
		</div>
	</div>
</div>

</body>
</html>



