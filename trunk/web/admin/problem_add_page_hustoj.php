<html>
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Content-Language" content="zh-cn">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>New Problem</title>
</head>
<body leftmargin="30">
<center>
<?php require_once("../include/db_info.inc.php");?>

<?php require_once("admin-header.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}?>
<?php
include_once("kindeditor.php") ;
?>

<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse"  width="100%" height="50">
<tr>
<td width="100"></td>
<td>
<p align="center"><font color="#333399" size="4">Welcome To Administrator's Page of Judge Online of ACM ICPC,<?php echo $OJ_NAME?>.</font></td>
<td width="100"></td>
</tr>
</table>
</center>
<hr>
<h1>Add New problem</h1>
<?php require_once("../include/simple_html_dom.php");

  $url=$_POST ['url'];

  if (!$url) $url=$_GET['url'];
  if (strpos($url, "http") === false){
	echo "Please Input like http://hustoj.com/oj/problem.php?id=1000";
	exit(1);
  }   
    
  if (false) {
	$url = stripslashes ( $url);
  }
  $baseurl=substr($url,0,strrpos($url,"/")+1);
//  echo $baseurl;
  $html = file_get_html($url);
  foreach($html->find('img') as $element)
        $element->src=$baseurl.$element->src;
  $element=$html->find('h2',0);
  $title=$element->plaintext;
  $i=1;
  $sample_output=$sample_input=$descriptionHTML="";
  
  $html=$html->innertext;
 // echo $i."-".strlen($html);
  if(strpos($html,"<h2>Description</h2>")>0){
 	 $descriptionHTML=getPartByMark($html,"<h2>Description</h2>","<h2>Source</h2>");
  }else{
 	 $descriptionHTML=getPartByMark($html,"<h2>题目描述</h2>","<h2>来源</h2>");
  }
 // echo $i."-".strlen($descriptionHTML);

?>
<form method=POST action=problem_add.php>
<p align=center><font size=4 color=#333399>Add a Problem</font></p>
<input type=hidden name=problem_id value=New Problem>
<p align=left>Problem Id:&nbsp;&nbsp;New Problem</p>
<p align=left>Title:<input type=text name=title size=71 value="<?php echo $title?>"></p>
<p align=left>Time Limit:<input type=text name=time_limit size=20 value=1>S</p>
<p align=left>Memory Limit:<input type=text name=memory_limit size=20 value=128>MByte</p>
<p align=left>Description:<br>
<textarea class="kindeditor" rows=13 name=description cols=80><?php echo $descriptionHTML;?></textarea>
</p>
<p align=left>Input:<br>
<textarea class="kindeditor" rows=13 name=input cols=80><?php echo $inputHTML;?></textarea>
</p>
</p>
<p align=left>Output:<br><!--<textarea rows=13 name=output cols=80></textarea>-->
<textarea class="kindeditor" rows=13 name=output cols=80><?php echo $outputHTML;?></textarea>
</p>
<p align=left>Sample Input:<br><textarea rows=13 name=sample_input cols=80><?php echo $sample_input?></textarea></p>
<p align=left>Sample Output:<br><textarea rows=13 name=sample_output cols=80><?php echo $sample_output?></textarea></p>
<p align=left>Test Input:<br><textarea rows=13 name=test_input cols=80></textarea></p>
<p align=left>Test Output:<br><textarea rows=13 name=test_output cols=80></textarea></p>
<p align=left>Hint:<br>
<textarea class="kindeditor" rows=13 name=hint cols=80></textarea>
</p>
<p>SpecialJudge: N<input type=radio name=spj value='0' checked>Y<input type=radio name=spj value='1'></p>
<p align=left>Source:<br><textarea name=source rows=1 cols=70></textarea></p>
<p align=left>contest:
	<select  name=contest_id>
<?php $sql="SELECT `contest_id`,`title` FROM `contest` WHERE `start_time`>NOW() order by `contest_id`";
$result=pdo_query($sql);
echo "<option value=''>none</option>";
if (count($result)==0){
}else{
	foreach($result as $row)
			echo "<option value='{$row['contest_id']}'>{$row['contest_id']} {$row['title']}</option>";
}
?>
	</select>
</p>
<div align=center>
<?php require_once("../include/set_post_key.php");?>
<input type=submit value=Submit name=submit>
</div></form>
<p>

</body></html>


