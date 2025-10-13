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
}
?>
<?php
include_once("kindeditor.php") ;
?>
<?php require_once("../include/simple_html_dom.php");

  $url=$_POST ['url'];
  $remote_id=basename($url);  
  if (!$url) $url=$_GET['url'];
  if (strpos($url, "http") === false){
	echo "Please Input like https://www.luogu.com.cn/problem/P1000";
	exit(1);
  } 
  if (false) {
	$url = stripslashes ( $url);
  }
  $baseurl=substr($url,0,strrpos($url,"/")+1);
//  echo $baseurl;
  //$html = file_get_html($url);
  $html = file_get_html($url, false,null,0, -1,true,true, DEFAULT_TARGET_CHARSET,false);
  foreach($html->find('img') as $element)
        $element->src=$baseurl.$element->src;
        
  $element=$html->find('article',0);
  $tlimit=1;
  $mlimit=128;
  $html=$element->innertext;
 // $html=getPartByMark($html,"<h1>","</h1>");
  $title=getPartByMark($html,"<h1>","</h1>");
  $descriptionHTML="<div class='md'>".getPartByMark($html,"<h2>题目背景</h2>","<h3>输入格式</h3>")."</div>";
  
  $inputHTML=getPartByMark($html,"<h3>输入格式</h3>","<h3>输出格式</h3>");
  $outputHTML=getPartByMark($html,"<h3>输出格式</h3>","<h2>输入输出样例</h2>");
  $hint=getPartByMark($html,"<h2>输入输出样例</h2>","<div>**广告**");

?>
<form method=POST action=problem_add.php>
<input type=hidden name=problem_id value="New Problem">
<p align=left>Problem Id:&nbsp;&nbsp;New Problem</p>
<p align=left>Title:<input type=text name=title size=71 value="<?php echo $title?>">
Time Limit:<input type=text class='input input-mini' name=time_limit size=20 value="<?php echo $tlimit?>">S
Memory Limit:<input type=text class='input input-mini'  name=memory_limit size=20 value="<?php echo $mlimit?>">MB
<br>LUOGU<input type=text name=remote_id value="<?php echo $remote_id?>" >
<input type=submit value=Submit name=submit>
<p align=left>Description:<br>
<textarea class="kindeditor" rows=5 name=description cols=30><?php echo $descriptionHTML;?></textarea>
<p align=left>Input:
<textarea  rows=5 name=input cols=30 ><?php echo $inputHTML;?></textarea>
Output:
<textarea  rows=5 name=output cols=30><?php echo $outputHTML;?></textarea>
</p>
<p align=left>Sample Input:<textarea rows=2 name=sample_input cols=80><?php echo $sample_input?></textarea>
Sample Output:<textarea rows=2 name=sample_output cols=80><?php echo $sample_output?></textarea></p>
<p align=left>Test Input:<textarea rows=2 name=test_input cols=80></textarea>
Test Output:<textarea rows=2 name=test_output cols=80></textarea>
<br>
Hint:<textarea rows=3 name=hint cols=2 ><?php echo $hint ?></textarea>
SpecialJudge: N<input type=radio name=spj value='0' checked>Y<input type=radio name=spj value='1'>
Source:<textarea name=source rows=1 cols=3><?php echo $url ?></textarea>
	<input type=hidden name=contest_id></input>
<input type=hidden name=remote_oj value="luogu" >	
<?php require_once("../include/set_post_key.php");?>
</div></form>
<p>

</body></html>

