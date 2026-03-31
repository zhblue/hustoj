<?php require_once ("admin-header.php");
require_once("../include/check_post_key.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor']))){
	exit(1);
}
?>
<?php require_once ("../include/db_info.inc.php");
?>
<?php require_once ("../include/problem.php");
?>
<?php // contest_id

$url=$_POST ['url'];

$title = $_POST ['title'];
$time_limit = $_POST ['time_limit'];
$memory_limit = $_POST ['memory_limit'];
$description = $_POST ['description'];
$input = $_POST ['input'];
$output = $_POST ['output'];
$sample_input = $_POST ['sample_input'];
$sample_output = $_POST ['sample_output'];
$test_input = $_POST ['test_input'];
$test_output = $_POST ['test_output'];
$hint = $_POST ['hint'];
$source = $_POST ['source'];
$spj = $_POST ['spj'];
if (false) {
	$title = stripslashes ( $title);
	$time_limit = stripslashes ( $time_limit);
	$memory_limit = stripslashes ( $memory_limit);
	$description = stripslashes ( $description);
	$input = stripslashes ( $input);
	$output = stripslashes ( $output);
	$sample_input = stripslashes ( $sample_input);
	$sample_output = stripslashes ( $sample_output);
	$test_input = stripslashes ( $test_input);
	$test_output = stripslashes ( $test_output);
	$hint = stripslashes ( $hint);
	$source = stripslashes ( $source);
	$spj = stripslashes ( $spj);
	$source = stripslashes ( $source );
}
//echo "->".$OJ_DATA."<-"; 
$pid=addproblem ( $title, $time_limit, $memory_limit, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $OJ_DATA );
$basedir = "$OJ_DATA/$pid";
mkdir ( $basedir );
chdir($basedir);
$loj_id=intval($_POST['loj_id']);
if(strstr($url,"uoj")){ 
	$fileCon = file_get_contents("https://uoj.ac/download.php?type=problem&id=$loj_id");
	file_put_contents($basedir."/data.zip",$fileCon);
    $tempfile=$basedir."/data.zip";
    echo "unzip files ... <br>";
    $resource = zip_open($tempfile);

    $i = 1;
    $tempfile = tempnam("/tmp", "fps");
    while ($dir_resource = zip_read($resource)) {
      if (zip_entry_open($resource,$dir_resource)) {
        $file_name = zip_entry_name($dir_resource);
	echo "<br>".$file_name;
	if(pathinfo($file_name)['extension']=="ans") {
		$file_name=pathinfo($file_name)['filename'].".out";
		echo "->".$file_name;
	}
        $file_path = substr($file_name,0,strrpos($file_name, "/"));
        if (!is_dir($file_name)) {
          $file_size = zip_entry_filesize($dir_resource);
          $file_content = zip_entry_read($dir_resource,$file_size);
	  if($file_name)
          	file_put_contents($basedir."/".$file_name,$file_content);
        }
       zip_entry_close($dir_resource);
      }
    }
    zip_close($resource);
    unlink ($basedir."/data.zip");
}else{
	$fileCon = file_get_contents("https://darkbzoj.tk/data/$loj_id.zip");
	file_put_contents($basedir."/data.zip",$fileCon);
}
//var_dump($fileCon);
if(strlen($sample_output)&&!strlen($sample_input)) $sample_input="0";
if(strlen($sample_input)) mkdata($pid,"sample.in",$sample_input,$OJ_DATA);
if(strlen($sample_output))mkdata($pid,"sample.out",$sample_output,$OJ_DATA);
if(strlen($test_output)&&!strlen($test_input)) $test_input="0";
if(strlen($test_input))mkdata($pid,"test.in",$test_input,$OJ_DATA);
if(strlen($test_output))mkdata($pid,"test.out",$test_output,$OJ_DATA);

$sql="insert into `privilege` (`user_id`,`rightstr`)  values(?,?)";
pdo_query($sql,$_SESSION[$OJ_NAME.'_'.'user_id'],"p$pid");
$_SESSION[$OJ_NAME.'_'."p$pid"]=true;
//print_r($_POST);
echo "<br>".$loj_id."<br>";
echo "<a href='javascript:phpfm($pid);'>Add more TestData now !</a>";
/*	*/
?>
<script src='../template/bs3/jquery.min.js' ></script>
<script>
function phpfm(pid){
        //alert(pid);
        $.post("phpfm.php",{'frame':3,'pid':pid,'pass':''},function(data,status){
                if(status=="success"){
                        document.location.href="phpfm.php?frame=3&pid="+pid;
                }
        });
}
</script>
Copy Next 
<form method=POST action=problem_add_page_uoj.php>
  <input name=url type=text size=100 value="<?php echo pathinfo($url)['dirname']."/".($loj_id+1)?>">
  <input type=submit>
</form>



