<?php
ini_set("display_errors", "Off");  //set this to "On" for debugging  ,especially when no reason blank shows up.
error_reporting(E_ALL);
require_once ("admin-header.php");
//require_once("../include/check_post_key.php");

if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_problem_editor'])  )) {
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
&nbsp;&nbsp;- Import Offline ... <br>
<b><?php echo $MSG_CONTEST."-".$MSG_IMPORT ?></b>
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
  $sql = "SELECT problem_id  FROM problem WHERE title=?";  
  $result = pdo_query($sql, $title);
  $ret=0;
  if (isset($result[0])&&isset($result[0][0]))  $ret=$result[0][0];		
  //echo "row->$rows_cnt";			
  return $ret;
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


if (!isset($_FILES ["offline"])||$_FILES ["offline"] ["error"] > 0) {
  echo "&nbsp;&nbsp;- Error: File size is too big, change in PHP.ini<br />";
?>
导入zip文件，遵循下面的目录结构:
<pre>
离线练习赛<?php date("Ymd")?>.zip
	+ data
	|   + problem1
	|   |      + 1.in
	|   |      + 1.out
	|   |      + 2.in
	|   |      + 2.out
	|   |
	|   + problem2
	|   |
	|   ......
	+ source   
	     + student1
	     |       + problem1.cpp
	     |       + problem2.cpp
	     |
	     |
	     + student2
	     |       + problem1.cpp
	     |       + problem2.cpp
	     |
	     ......
</pre>
	<form method="post" action="offline_import.php" enctype="multipart/form-data">
		<input type=file name="offline" >
		<input type=submit >

	</form>

<?php 
}
else {
  $tempdir = sys_get_temp_dir()."/import_offline".time();	
  mkdir($tempdir);
  $tempfile = $_FILES ["offline"] ["tmp_name"];
  $titles=array();
  $problems=array();
  $nums=array();
  $cid=0;
  $nextNum=0;
  if (get_extension( $_FILES ["offline"] ["name"])=="zip") {
	    $resource = zip_open($tempfile);
	    $save_path="";
	    $num = 0;
	    $pid=$title=$description=$input=$output=$sample_input=$sample_output=$hint=$source=$spj="";
	    $type="normal";
	    while ($dir_resource = zip_read($resource)) {
	      if (zip_entry_open($resource,$dir_resource)) {
		$file_name = $path.zip_entry_name($dir_resource);
		$file_path = substr($file_name,0,strrpos($file_name, "/"));
		$file_size = zip_entry_filesize($dir_resource);
		$file_content = zip_entry_read($dir_resource,$file_size);
		if(startsWith($file_name,"data")){
			if(dirname($file_name)=="data"){ 
				$title=basename($file_name);
				if($title!="data"){
					$pid = addproblem($title,1,128, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $OJ_DATA);
					mkdir($OJ_DATA."/$pid/");
					$problems[$title]=$pid;
					$titles[$num]=$title;
					$nums[$title]=$num++;
				}
			}else{
				
				if(endsWith($file_name,".in")||endsWith($file_name,".out")||endsWith($file_name,".name")){ 
					file_put_contents($OJ_DATA."/$pid/".basename($file_name),$file_content );			
				}else if(endsWith($file_name,".ans")){
					file_put_contents($OJ_DATA."/$pid/".basename($file_name,".ans").".out",$file_content );			
				}
			
			}	
		}else if (startsWith($file_name,"source")){
			$answer=basename($file_name);
			if($answer==="source"){
				$title= basename($_FILES ["offline"] ["name"],".zip");
				$sql = "INSERT INTO `contest`(`title`,`start_time`,`end_time`,`private`,`langmask`,`description`,`password`,`user_id`)
					  VALUES(?,?,?,?,?,?,?,?)";
				  $user_id=$_SESSION[$OJ_NAME.'_'.'user_id'];
				  $starttime=date("Y-m-d H:i");
				  $endtime=date("Y-m-d H:i",time()+3600);
				  $description = "Offline contest imported by ".$user_id;
				  $cid = pdo_query($sql,$title,$starttime,$endtime,0,0,$description,"",$user_id) ;
				  echo "导入离线竞赛$title:".$cid."<br>";
				  $plist="";
				    $i=0;				
				    $sql_1 = "INSERT INTO `contest_problem`(`contest_id`,`problem_id`,`num`) VALUES (?,?,?)";
				    for(; $i<count($problems); $i++){
					 if($plist) $plist.=",";
					 $plist.=intval($problems[$titles[$i]]);
					 pdo_query($sql_1,$cid,$problems[$titles[$i]],$i);
				    }
				    $nextNum=$i;
				    //echo $sql_1;
				    $sql = "UPDATE `problem` SET defunct='N' WHERE `problem_id` IN ($plist)";
				    pdo_query($sql) ;
			
			}else if(endsWith($answer,".cpp")){
				$student=dirname($file_name);
				if(dirname($student)!="source") $student=dirname($student);
				$student=basename($student);
				$problem=basename($answer,".cpp");
				if( endsWith($problem,".cpp")) $problem=basename($problem,".cpp");
				if(!isset($problems[$problem])){
					$pid=hasProblem($problem);
					if($pid>0){ 
						$problems[$problem]=$pid;
				    		$sql_1 = "INSERT INTO `contest_problem`(`contest_id`,`problem_id`,`num`) VALUES (?,?,?)";
					 	pdo_query($sql_1,$cid,$pid,$nextNum);
						$nextNum++;
					}
				}
				if(isset($nums[$problem])&&isset($problems[$problem])){
					$num=$nums[$problem];
					$pid=$problems[$problem];
					$len=strlen($file_content);
					$sql = "INSERT INTO solution(problem_id,user_id,contest_id,num,nick,in_date,language,ip,code_length,result)
							VALUES(?,?,?,?,?,NOW(),?,'127.0.0.1',?,14)";
					$insert_id = pdo_query($sql, $pid,$student,$cid,$num,$student, 1 , $len);
					//  echo "submiting$language.....$insert_id";
					$sql = "INSERT INTO `source_code`(`solution_id`,`source`) VALUES(?,?)";
					pdo_query($sql ,$insert_id, $file_content);
					$sql = "INSERT INTO `source_code_user`(`solution_id`,`source`) VALUES(?,?)";
					$ret=pdo_query($sql, $insert_id, $file_content);
					if($ret<0){
						echo "<h3> $student - $problem</h3> - 非法字符，提交失败<br>  ";
						echo " - 尝试转码 <br>  ";
						$file_content=mb_convert_encoding($file_content, "utf8", "gbk");
						$sql = "INSERT INTO `source_code`(`solution_id`,`source`) VALUES(?,?)";
						pdo_query($sql ,$insert_id, $file_content);
						$sql = "INSERT INTO `source_code_user`(`solution_id`,`source`) VALUES(?,?)";
						$ret=pdo_query($sql, $insert_id, $file_content);
						if($ret<0){
							pdo_query("delete from solution where solution_id=?",$insert_id);
							echo " - 转码失败,提交无效 <br>  ";
						}else{
							echo " - 转码成功 <br>  ";
						
						}
						
					}
					pdo_query("UPDATE solution SET result=1 WHERE solution_id=?", $insert_id);
					pdo_query("UPDATE problem SET submit=submit+1 WHERE problem_id=?", $pid);
				}
			}
		
		}

        if (isset($OJ_UDP) && $OJ_UDP) {
           trigger_judge();
        }

		zip_entry_close($dir_resource);
	      }
	    }
	    zip_close($resource);
	  
	    unlink ( $_FILES ["offline"] ["tmp_name"] );
	    system ("rmdir $tempdir");
 
  }else{
    	echo " zip file Only <hr> \n";
  }
}
 

?>
