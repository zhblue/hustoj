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
  function pregReplaceImg2($content,$prefix)
        {
            $contentAlter = preg_replace_callback('/(<[img|IMG].*?src=[\'\"])([\s\S]*?)([\'\"])[\s\S]*?/i', function($match)use($prefix){
                if(strstr($match[2], 'http://') == false && strstr($match[1], 'https://') == false)
                    return $match[1].$prefix.$match[2].$match[3];
                else
                    return $match[1].$match[2].$match[3];
            } , $content);
            return $contentAlter;
        }


$url=$_POST ['url'];
  if (!$url) $url=$_GET['url'];
  if (strpos($url, "http") === false){
	echo "Please Input like http://uoj.ac/problem/1";
	exit(1);
  }
  if (false) {
	$url = stripslashes ( $url);
  }
  $loj_id=intval(substr($url,strrpos($url,"/")+1));
  //echo strrpos($url,"/");
  $baseurl=substr($url,0,strrpos($url,"/")+1);
  //echo $baseurl;
  $html = file_get_html($url);
 // foreach($html->find('img') as $element)
 //       $element->src=$baseurl.$element->src;
        
  $element=$html->find('h1',2);
  $title=trim($element->plaintext);

  $element=$html->find('article',0);
  $mlimit=$element->plaintext; 
  $mlimit=mb_substr($mlimit, mb_strpos($mlimit, "空间限制")+6);
  $mlimit=mb_substr($mlimit,0,mb_strpos($mlimit, 'MB')-8);
  $mlimit=intval($mlimit);
  if($mlimit==0)$mlimit=256;
  $tlimit=$element->plaintext;
  $tlimit=mb_substr($tlimit, mb_strpos($tlimit, "时间限制")+6);
  $tlimit=mb_substr($tlimit,0,mb_strpos($tlimit, 's')-8);
  $tlimit=intval($tlimit);
  if($tlimit==0)$tlimit=1;
  //$mlimit/=1000;
  //echo "mlimit:$mlimit<br>";
  //echo "tlimit:".$tlimit;
  
  $element=$html->find('article',0);
  $descriptionHTML=$element->outertext;
  $descriptionHTML=pregReplaceImg2($descriptionHTML,"https://darkbzoj.tk/");


  $element=$html->find('div[class=ui bottom attached segment font-content]',1);
  $inputHTML=$element->outertext;
  $element=$html->find('div[class=ui bottom attached segment font-content]',2);
  $outputHTML=$element->outertext;
  
  $element=$html->find('code[class=lang-plain]',0);
  $sample_input=$element->innertext;
  $element=$html->find('code[class=lang-plain]',1);
  $sample_output=$element->innertext;
  $element=$html->find('div[class=ui bottom attached segment font-content]',4);
  $hintHTML=$element->outertext;
  $element=$html->find('div[class=ui bottom attached segment]',1);
  $sourceHTML=$element->outertext;
?>
<form method=POST action="problem_add_uoj.php">
<input type=submit value=Submit name=submit>
<p align=center><font size=4 color=#333399>Add a Problem</font></p>
<input type="hidden" name=problem_id value="New Problem">
<input type="hidden" name="url" value="<?php echo htmlentities($url)?>">
<input type="hidden" name="loj_id" value="<?php echo $loj_id?>">
<p align=left>Problem Id:&nbsp;&nbsp;New Problem</p>
<p align=left>Title:<input type=text name=title size=71 value="<?php echo $title?>"></p>
<p align=left>Time Limit:<input type=text name=time_limit size=20 value="<?php echo $tlimit?>">S</p>
<p align=left>Memory Limit:<input type=text name=memory_limit size=20 value="<?php echo $mlimit?>">MByte</p>
<p align=left>Description:<br>
<textarea class="kindeditor" rows=13 name=description cols=80><?php echo $descriptionHTML;?></textarea>
</p>
<span style="display:none">
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
<textarea class="kindeditor" rows=13 name=hint cols=80><?php echo $hintHTML?></textarea>
</p>
<p>SpecialJudge: N<input type=radio name=spj value='0' checked>Y<input type=radio name=spj value='1'></p>
<p align=left>Source:<br><textarea name=source rows=1 cols=70><?php echo $url?></textarea></p>
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
</span>
<div align=center>
<?php require_once("../include/set_post_key.php");?>
</div></form>
<p>

</body></html>
