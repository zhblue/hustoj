<?php
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
function strip($Node, $TagName) {
  $len=mb_strlen($TagName);
  $i=mb_strpos($Node,"<".$TagName.">");
  $j=mb_strpos($Node,"</".$TagName.">");

  return mb_substr($Node,$i+$len+2,$j-($i+$len+2));
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

function get_extension($file) {
  $info = pathinfo($file);
  return $info['extension'];
}

function import_json($json) {
  global $OJ_DATA,$OJ_SAE,$OJ_REDIS,$OJ_REDISSERVER,$OJ_REDISPORT,$OJ_REDISQNAME,$domain,$DOMAIN;
  $qduoj_problem=json_decode($json)->{'problem'};
 echo( "<br> ".$qduoj_problem->{'problemId'});
  echo( $qduoj_problem->{'title'})."<br>";

    $title = $qduoj_problem->{'title'};

    $time_limit = floatval($qduoj_problem->{'timeLimit'});
    $unit = "ms";
    //echo $unit;
   
    $time_limit /= 1000;

    $memory_limit =  floatval($qduoj_problem->{'memoryLimit'});
    $unit = "M";

    if ($unit=='kb')
      $memory_limit /= 1024;

    $description = "[md]\n".$qduoj_problem->{'description'}."\n[/md]";
    $input = "[md]\n".$qduoj_problem->{'input'}."\n[/md]";
    $output = "[md]\n".$qduoj_problem->{'output'}."\n[/md]";
    $examples=$qduoj_problem->{'examples'};
    $sample_input = strip($examples,"input");
    $sample_output = strip($examples,"output");;
    echo "sin:".$sample_input."<br>";
    echo "sout:".$sample_output."<br>";
    $hint = "[md]\n".$qduoj_problem->{'hint'}."\n[/md]";
    $source ="";
    echo json_decode($json)->{'tags'}->{'value'};				
    $spj=0;
    
    echo "($title, $time_limit, $memory_limit, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj  )";
    $pid = addproblem($title, $time_limit, $memory_limit, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $OJ_DATA);
    return $pid;
}


if ($_FILES ["fps"] ["error"] > 0) {
  echo "&nbsp;&nbsp;- Error: ".$_FILES ["fps"] ["error"]."File size is too big, change in PHP.ini<br />";
}
else {
  $tempdir = sys_get_temp_dir()."/import_hoj".time();	
  echo  $tempdir ;
  mkdir($tempdir);
  $tempfile = $_FILES ["fps"] ["tmp_name"];
  if (get_extension( $_FILES ["fps"] ["name"])=="zip") {
    echo "&nbsp;&nbsp;- zip file, only HOJ exported file is supported";
    $resource = zip_open($tempfile);

    $i = 1;
    while ($dir_resource = zip_read($resource)) {
      if (zip_entry_open($resource,$dir_resource)) {
        $file_name = $path.zip_entry_name($dir_resource);
        $file_path = substr($file_name,0,strrpos($file_name, "/"));
        if (!is_dir($file_name)) {
          $file_size = zip_entry_filesize($dir_resource);
          $file_content = zip_entry_read($dir_resource,$file_size);
	  //echo "$file_name"."<br>";
	  if(get_extension($file_name)=="json")
	  {
//		  import_json($file_content);
	  }else{
	  	//echo "$file_name"."<br>";
		mkdir($tempdir."/".dirname($file_name));
		file_put_contents($tempdir."/".$file_name,$file_content);
	  }
	}else{
	  echo $file_name;
	}
       zip_entry_close($dir_resource);
      }
    }
    zip_close($resource);
    $resource = zip_open($tempfile);
    $cmds=array();
    $i = 1;
    while ($dir_resource = zip_read($resource)) {
      if (zip_entry_open($resource,$dir_resource)) {
        $file_name = $path.zip_entry_name($dir_resource);
        $file_path = substr($file_name,0,strrpos($file_name, "/"));
        if (!is_dir($file_name)) {
          $file_size = zip_entry_filesize($dir_resource);
          $file_content = zip_entry_read($dir_resource,$file_size);
	  if(get_extension($file_name)=="json")
	  {
		  $pid=import_json($file_content);
		 // $dir=$tempdir."/".basename($file_name,".json");
		  mkdir("$OJ_DATA/$pid");
		  $data_dir=basename($file_name,".json");
		  array_push ($cmds,"mv $tempdir/$data_dir/* $OJ_DATA/$pid/");
		  array_push ($cmds,"rmdir $tempdir/$data_dir/");
		  $i++;
	  }else{
	  	//echo "$file_name"."<br>";
		mkdir($tempdir."/".dirname($file_name),0755,true);
		file_put_contents($tempdir."/".$file_name,$file_content);
	  }
	}else{
	  echo $file_name;
	}
       zip_entry_close($dir_resource);
      }
    }
    zip_close($resource);
    unlink ( $_FILES ["fps"] ["tmp_name"] );
    foreach($cmds as $cmd){
//	echo $cmd."<br>";
    	system($cmd);
    
    }
    system ("rmdir $tempdir");
  }
  else {
  echo ($tempfile);
  }
 
}
 

?>
