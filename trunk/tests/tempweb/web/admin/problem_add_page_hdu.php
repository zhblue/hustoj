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
	exit(1);
}
?>
<?php
include_once("kindeditor.php") ;
?>
<?php require_once("../include/simple_html_dom.php");
  $url=$_POST ['url'];
  if (!$url) $url=$_GET['url'];
  if (strpos($url, "http") === false){
	echo "Please Input like http://acm.hdu.edu.cn/showproblem.php?pid=1000";
	exit(1);
  }
  if (false) {
	$url = stripslashes ( $url);
  }
  $remote_id=mb_substr($url,mb_strpos($url,"=")+1);  
  $baseurl=substr($url,0,strrpos($url,"/")+1);
  //echo $baseurl;
  $html = file_get_html($url, false,null,0, -1,true,true, DEFAULT_TARGET_CHARSET,false);//file_get_html($url,'GB2312');
  
  foreach($html->find('img') as $element)
        $element->src=$baseurl.$element->src;
        
  $element=$html->find('h1',0);
  $title=$element->plaintext;
  
  $element=$html->find('span',0);
  $tlimit=$element->plaintext; 
  $tlimit=substr($tlimit,12);
  $tlimit=substr($tlimit,strpos($tlimit, '/')+1,strpos($tlimit, ' MS') - strpos($tlimit, '/'));
  $mlimit=$element->plaintext;
  $mlimit=substr($mlimit, strpos($mlimit, "Memory"));
  $mlimit=substr($mlimit, strpos($mlimit, '/')+1,strpos($mlimit, ' K') - strpos($mlimit, '/'));
  //echo $mlimit;
  $tlimit/=1000;
  $mlimit/=1000;
  
  $element=$html->find('div[class=panel_content]',0);
  $descriptionHTML=$element->outertext;
  $element=$html->find('div[class=panel_content]',1);
  $inputHTML=$element->outertext;
  $element=$html->find('div[class=panel_content]',2);
  $outputHTML=$element->outertext;
  
  $element=$html->find('pre',0);
  $element=$element->find('div',0);
  $sample_input=$element->innertext;
  $element=$html->find('pre',1);
  $element=$element->find('div',0);
  $sample_output=$element->innertext;
?>
<form method=POST action=problem_add.php>
<input type=hidden name=problem_id value=New Problem>
<p align=left>Problem Id:&nbsp;&nbsp;New Problem from acm.hdu.edu.cn</p>
HDU<input type=text name=remote_id value="<?php echo $remote_id?>" >
<p align=left>Title:<input type=text name=title size=71 value="<?php echo $title?>">
Time Limit:<input type=text name=time_limit class='input input-mini' size=2 value="<?php echo $tlimit?>">S
Memory Limit:<input type=text name=memory_limit class='input input-mini' size=2 value="<?php echo $mlimit?>">MByte
<input type=submit value=Submit name=submit></p>
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
<p align=left>Sample Input:<textarea rows=3 name=sample_input cols=80><?php echo $sample_input?></textarea>
Sample Output:<textarea rows=3 name=sample_output cols=80><?php echo $sample_output?></textarea></p>
<p align=left>Test Input:<textarea rows=3 name=test_input cols=80></textarea>
Test Output:<textarea rows=3 name=test_output cols=80></textarea></p>
<p align=left>Hint:<br>
<textarea rows=3 name=hint cols=30></textarea>
SpecialJudge: N<input type=radio name=spj value='0' checked>Y<input type=radio name=spj value='1'></p>
<p align=left>Source:<textarea name=source rows=1 cols=70 ><?php echo htmlentities($url)?></textarea></p>
<div align=center>
<?php require_once("../include/set_post_key.php");?>
<input type=hidden name=remote_oj value="hdu" >	
</div></form>
<p>

</body></html>
