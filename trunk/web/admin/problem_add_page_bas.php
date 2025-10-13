<html>
<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Content-Language" content="zh-cn">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>New Problem</title>
</head>
<body leftmargin="30">
<?php require_once("../include/db_info.inc.php");?>

<?php require_once("admin-header.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}?>
<?php
include_once("kindeditor.php") ;
?>
<?php require_once("../include/simple_html_dom.php");

  $url=$_POST ['url'];

  if (!$url) $url=$_GET['url'];
  if (strpos($url, "http") === false){
	echo "Please Input like http://ybt.ssoier.cn:8088/problem_show.php?pid=1000 ";
	exit(1);
  }   
  $remote_id=mb_substr($url,mb_strpos($url,"=")+1);  
  if (false) {
	$url = stripslashes ( $url);
  }
  $baseurl=substr($url,0,strrpos($url,"/")+1);
//  echo $baseurl;
  $html = file_get_html($url, false,null,0, -1,true,true, DEFAULT_TARGET_CHARSET,false);
  foreach($html->find('img') as $element)
        $element->src=$baseurl.$element->src;
  $element=$html->find('h3',0);
  $title=$element->plaintext;
  $title=mb_substr($title,mb_strlen($remote_id."："));
  $i=1;
  $sample_output=$sample_input=$descriptionHTML="";
  
  $html=$html->innertext;
  $html=mb_ereg_replace("\<script\>pshow\(\"","",$html);
  $html=mb_ereg_replace("\"\);\<\/script\>","",$html);
 // echo $i."-".strlen($html);
  if(strpos($html,"<center><table width=1000px>")>0){
 	 $descriptionHTML=getPartByMark($html,"<center><table width=1000px><td class=\"pcontent\"><center>","<p align=center> <a href=submit.php?pid=$remote_id class=\"bottom_link\">");
  }else{
 	 $descriptionHTML=getPartByMark($html,"【题目描述】","<p align=center> <a href=submit.php?pid=".$remote_id);
  }
 // echo $i."-".strlen($descriptionHTML);
  $time_limit=getPartByMark( $descriptionHTML,"时间限制: ","ms");
  $time_limit=intval($time_limit)/1000.0;
  $memory_limit=getPartByMark( $descriptionHTML,"内存限制: ","KB");
  $memory_limit=intval($memory_limit)/1024.0;
  $inputHTML=getPartByMark($descriptionHTML,"<h3>【输入】</h3>","<h3>【输出】</h3>");
  $inputHTML=mb_ereg_replace("\\\\n","\n<br>",$inputHTML);
  $outputHTML=getPartByMark($descriptionHTML,"<h3>【输出】</h3>","<h3>【输入样例】</h3>");
  $outputHTML=mb_ereg_replace("\\\\n","\n<br>",$outputHTML);
  $sample_input=getPartByMark($descriptionHTML,"【输入样例】</h3>\n<font size=3><pre>","</pre></font>");
  $sample_output=getPartByMark($descriptionHTML,"【输出样例】</h3>\n<font size=3><pre>","</pre></font>");
  $descriptionHTML=getPartByMark($descriptionHTML,"【题目描述】</h3>","<h3>【输入】</h3>");
  $descriptionHTML=mb_ereg_replace("\\\\n","\n<br>",$descriptionHTML);
?>
<form method=POST action=problem_add.php >
<input type=hidden name=problem_id value=New Problem>
<p align=left>Title:<input type=text name=title size=71 value="<?php echo $title?>"></p>
<p align=left>Time Limit:<input type=text name=time_limit size=20 value="<?php echo $time_limit?>">S
Memory Limit:<input type=text name=memory_limit size=20 value="<?php echo $memory_limit?>">MByte</p>
<p align=left>Description:<br>
<textarea class="kindeditor" rows=13 name=description cols=80><?php echo $descriptionHTML;?></textarea>
</p>
<p align=left>Input:
<textarea  rows=5 name=input cols=80><?php echo $inputHTML;?></textarea>
Output:<!--<textarea rows=13 name=output cols=80></textarea>-->
<textarea  rows=5 name=output cols=80><?php echo $outputHTML;?></textarea>
<br>
Sample Input:<textarea rows=1 name=sample_input cols=80><?php echo $sample_input?></textarea>
Sample Output:<textarea rows=1 name=sample_output cols=80><?php echo $sample_output?></textarea>
<br>
Test Input:<textarea rows=1 name=test_input cols=80></textarea>
Test Output:<textarea rows=1 name=test_output cols=80></textarea>
<br>
Hint:
<textarea rows=1 name=hint cols=80></textarea>
<p>SpecialJudge: N<input type=radio name=spj value='0' checked>Y<input type=radio name=spj value='1'></p>
<p align=left>Source:<textarea class='input input-xxlarge' name=source rows=1 cols=170><?php echo htmlentities($url,ENT_QUOTES,'UTF-8')?></textarea></p>
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
<input type=hidden name=remote_oj value="bas" >
	<input type=text name=remote_id value="<?php echo $remote_id?>" >
<input type=submit value=Submit name=submit>
</div></form>
<p>
<?php if(intval($remote_id)<3483){ ?>
<script>  window.setTimeout("$('input[type=submit]').click();",2000);</script>
<?php } ?>
</body></html>


