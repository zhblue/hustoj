<?php
ini_set("display_errors", "On");  //set this to "On" for debugging  ,especially when no reason blank shows up.
require_once ("admin-header.php");
//require_once("../include/check_post_key.php");

if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_problem_importer'])  )) {
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}

if (isset($OJ_LANG)) {
  require_once("../lang/$OJ_LANG.php");
}

require_once ("../include/const.inc.php");
require_once ("../include/problem.php");
?>

<?php
?>

<hr>
&nbsp;&nbsp;- Import Problem ... <br>
&nbsp;&nbsp;- 如果导入失败，请参考 <a href="https://github.com/zhblue/hustoj/blob/master/wiki/FAQ.md#%E5%90%8E%E5%8F%B0%E5%AF%BC%E5%85%A5%E9%97%AE%E9%A2%98%E5%A4%B1%E8%B4%A5" target="_blank">FAQ</a>。
<br><br>

<?php
function startsWith( $haystack, $needle ) {
     $length = strlen( $needle );
     return substr( $haystack, 0, $length ) === $needle;
}
function endsWith( $haystack, $needle ) {
    $length = strlen( $needle );
    if( !$length ) {
        return true;
    }
    return substr( $haystack, -$length ) === $needle;
}
function strip($Node, $TagName) {
  $len=mb_strlen($TagName);
  $i=mb_strpos($Node,"<".$TagName.">");
  $j=mb_strpos($Node,"</".$TagName.">");

  return mb_substr($Node,$i+$len+2,$j-($i+$len+2));
}
function get_extension($file) {
  $info = pathinfo($file);
  return $info['extension'];
}

function getAttribute($Node, $TagName,$attribute) {
  return $Node->children()->$TagName->attributes()->$attribute;
}

function hasProblem($title) {
  //return false;	
  $md5 = md5($title);
  $sql = "SELECT 1 FROM problem WHERE md5(title)=?";  
  $result = pdo_query($sql, $md5);
  $rows_cnt = count($result);		
  //echo "row->$rows_cnt";			
  return ($rows_cnt>0);
}

function mkpta($pid,$prepends,$node) {
  $language_ext = $GLOBALS['language_ext'];
  $OJ_DATA = $GLOBALS['OJ_DATA'];

  foreach ($prepends as $prepend) {
    $language = $prepend->attributes()->language;
    $lang = getLang($language);
    $file_ext = $language_ext[$lang];
    $basedir = "$OJ_DATA/$pid";
    $file_name = "$basedir/$node.$file_ext";
    file_put_contents($file_name,$prepend);
  }
}


function import_dir($json) {
  global $OJ_DATA,$OJ_SAE,$OJ_REDIS,$OJ_REDISSERVER,$OJ_REDISPORT,$OJ_REDISQNAME,$domain,$DOMAIN;
  $qduoj_problem=json_decode($json);
  echo( $qduoj_problem->{'problem'}->{'title'})."<br>";

    $title = $qduoj_problem->{'problem'}->{'title'};

    $time_limit = floatval($qduoj_problem->{'problem'}->{'timeLimit'});
    $unit = "ms";
    //echo $unit;

    if ($unit=='ms')
      $time_limit /= 1000;

    $memory_limit =  floatval($qduoj_problem->{'problem'}->{'memoryLimit'});
    $unit = "M";

    if ($unit=='kb')
      $memory_limit /= 1024;

    $description = $qduoj_problem->{'problem'}->{'description'};
    $input = $qduoj_problem->{'problem'}->{'input'};
    $output = $qduoj_problem->{'problem'}->{'output'};
    $sample_input = strip($qduoj_problem->{'problem'}->{'examples'},"input");
    $sample_output = strip($qduoj_problem->{'problem'}->{'examples'},"output");
//    echo $sample_input."<br>";
//    echo $sample_output;
    $hint = $qduoj_problem->{'problem'}->{'hint'};
    $source = $qduoj_problem->{'problem'}->{'source'};				
    $spj=0;
    
    $pid = addproblem($title, $time_limit, $memory_limit, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $OJ_DATA);
    return $pid;
}


if ($_FILES ["fps"] ["error"] > 0) {
  echo "&nbsp;&nbsp;- Error: ".$_FILES ["fps"] ["error"]."File size is too big, change in PHP.ini<br />";
}
else {
  $tempdir = sys_get_temp_dir()."/import_markdown".time();	
  mkdir($tempdir);
  $tempfile = $_FILES ["fps"] ["tmp_name"];
  if (get_extension( $_FILES ["fps"] ["name"])=="zip") {
    echo "&nbsp;&nbsp;- zip file, only Markdown .md file is supported<hr>\n";
    $resource = zip_open($tempfile);
    $save_path="";
    $i = 1;
    $pid=$title=$description=$input=$output=$sample_input=$sample_output=$hint=$source=$spj="";
    $type="normal";
    while ($dir_resource = zip_read($resource)) {
      if (zip_entry_open($resource,$dir_resource)) {
        $file_name = $save_path.zip_entry_name($dir_resource);
        $file_path = substr($file_name,0,strrpos($file_name, "/"));
		if (!is_dir($file_name)) {
			$ymd ="/upload/".$domain."/". date("Ymd");
			$save_path = $ymd ;
			$file_size = zip_entry_filesize($dir_resource);
			$file_content = zip_entry_read($dir_resource,$file_size);
			if(endsWith(basename($file_name),".md")){
				$hydrop=explode("\n",$file_content);	
				$title=str_replace("#","",$hydrop[0]);	
				$title=str_replace("\r","",$title);	
				$source="";	
				echo "<hr>".htmlentities($file_name." $title $source");
				$regex = '/<(?!div)/';
			//	$file_content = preg_replace($regex, '＜',$file_content);
				$regex = '/(?<!div)>\s?/';
			//	$file_content = preg_replace($regex, '＞', $file_content);
				//$file_content = str_replace("&", '＆', $file_content);
	//			if(strpos($file_content,"##")===false) 
	//				$description=$file_content;
	  //                      else 
				if($type=="normal")$description="<span class=\"md\">".$file_content."</span>";
				else $description="<span class=\"md auto_select\">".$file_content."</span>";
				$description=preg_replace('/{{ select\(\d+\) }}/', "", $description); 
				if($save_path){
					$description=str_replace("file://",$save_path."/",$description);
				
			//		echo htmlentities($description);
				}

				//echo htmlentities("$description");
				$tail=0;
				$ptitle = $title;
				while (hasProblem($ptitle)) {
					 $tail++;
					 $ptitle = $title."_".$tail;
				}
				$title=$ptitle;
				$pid = addproblem($title,1,128, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $OJ_DATA);
				mkdir($OJ_DATA."/$pid/");
				
				echo "PID:<a href='../problem.php?id=$pid' >".htmlentities($title,ENT_QUOTES,"UTF-8")."</a>";
			}
		}
	}else{
       		zip_entry_close($dir_resource);
      	}
    }
    zip_close($resource);
    unlink ( $_FILES ["fps"] ["tmp_name"] );
    rmdir ($tempdir);
    }
  else {
  		echo ($tempfile);
  }
 
}
 

?>
