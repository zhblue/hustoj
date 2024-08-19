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
require_once ("../include/curl.php");
require_once ("../include/problem.php");
?>

<?php
?>

<hr>
&nbsp;&nbsp;- Import Problem ... <br>
&nbsp;&nbsp;- 如果导入失败，请参考 <a href="https://github.com/zhblue/hustoj/blob/master/wiki/FAQ.md#%E5%90%8E%E5%8F%B0%E5%AF%BC%E5%85%A5%E9%97%AE%E9%A2%98%E5%A4%B1%E8%B4%A5" target="_blank">FAQ</a>。
<br><br>

<?php
function get_extension($file) {
  $info = pathinfo($file);
  return $info['extension'];
}


if ($_FILES ["fps"] ["error"] > 0) {
  echo "&nbsp;&nbsp;- Error: ".$_FILES ["fps"] ["error"]."File size is too big, change in PHP.ini<br />";
}
else {
  $tempdir = sys_get_temp_dir()."/import_syzoj".time();	
  mkdir($tempdir);
  $tempfile = $_FILES ["fps"] ["tmp_name"];
  if (get_extension( $_FILES ["fps"] ["name"])=="zip") {
    echo "&nbsp;&nbsp;- zip file, only SYZOJ exported file is supported<hr>\n";
    $resource = zip_open($tempfile);

    $i = 1;
    $pid=$title=$description=$input=$output=$sample_input=$sample_output=$hint=$source=$spj="";
    while ($dir_resource = zip_read($resource)) {
      if (zip_entry_open($resource,$dir_resource)) {
        $file_name = $path.zip_entry_name($dir_resource);
        $file_path = substr($file_name,0,strrpos($file_name, "/"));
        if (!is_dir($file_name)) {
		$file_size = zip_entry_filesize($dir_resource);
		$file_content = zip_entry_read($dir_resource,$file_size);
		if(basename($file_name)=="statement.md"){
			$description="<div class='md'>\n".$file_content."\n</div>";	
			$title=explode("\n",$file_content)[0];	
			$title=mb_substr($title,2);
			$time=getPartByMark($file_content,"时间限制：","ms");			
			$time=intval($time)/1000;
			$memory=getPartByMark($file_content,"空间限制：","MiB");			
			echo "<hr>".htmlentities(" $title $source");
    			$pid = addproblem($title,$time,$memory, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $OJ_DATA);
			echo "PID:$pid";
			mkdir($OJ_DATA."/$pid");
			echo ("mv $tempdir/data/* $OJ_DATA/$pid/");
			system ("mv $tempdir/data/* $OJ_DATA/$pid/");
		}else if(strpos($file_path,"data") !== false && basename($file_name) != "data" ){
	  		echo ".";
			mkdir($tempdir."/data/");
			$name=basename($file_name);
			if(get_extension($name)=="ans"){
				$name=basename($file_name,".ans").".out";
			}
		        echo "[$pid]";
                        if($pid>0)
                                file_put_contents("$OJ_DATA/$pid/".$name,$file_content);
                        else
                                file_put_contents($tempdir."/data/".$name,$file_content);
		}else{
			echo $file_name.":$pid<br>";
		}
	}else{
	  echo $file_name;
	}
	
       zip_entry_close($dir_resource);
      }
    }
    zip_close($resource);
  
    unlink ( $_FILES ["fps"] ["tmp_name"] );
    system ("rmdir $tempdir");
  }
  else {
  echo ($tempfile);
  }
 
}
 

?>
