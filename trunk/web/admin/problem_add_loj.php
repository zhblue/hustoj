<?php require_once ("admin-header.php");
require_once("../include/check_post_key.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
?>
<?php require_once ("../include/db_info.inc.php");
?>
<?php require_once ("../include/problem.php");
?>
<?php // contest_id


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
 $description ='<link href="https://dn-menci.qbox.me/libreoj/libs/KaTeX/katex.min.css" rel="stylesheet">'. $description ;
//echo "->".$OJ_DATA."<-"; 
$pid=intval(addproblem ( $title, $time_limit, $memory_limit, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $OJ_DATA ));
$basedir = realpath("$OJ_DATA/$pid");
if (strpos($basedir, realpath($OJ_DATA)) !== 0) die("Invalid path");
mkdir ( $basedir );
if(strlen($sample_output)&&!strlen($sample_input)) $sample_input="0";
if(strlen($sample_input)) mkdata($pid,"sample.in",$sample_input,$OJ_DATA);
if(strlen($sample_output))mkdata($pid,"sample.out",$sample_output,$OJ_DATA);
if(strlen($test_output)&&!strlen($test_input)) $test_input="0";
if(strlen($test_input))mkdata($pid,"test.in",$test_input,$OJ_DATA);
if(strlen($test_output))mkdata($pid,"test.out",$test_output,$OJ_DATA);

$sql="insert into `privilege` (`user_id`,`rightstr`)  values(?,?)";
pdo_query($sql,$_SESSION[$OJ_NAME.'_'.'user_id'],"p$pid");
$_SESSION[$OJ_NAME.'_'."p$pid"]=true;
$loj_id=intval($_POST['loj_id']);
//print_r($_POST);
echo "<br>".$loj_id."<br>";
echo htmlentities("wget https://loj.ac/problem/".$loj_id."/testdata/download -O $OJ_DATA/$pid/data.zip");
echo system("wget https://loj.ac/problem/".$loj_id."/testdata/download -O $OJ_DATA/$pid/data.zip");
echo system("/home/judge/src/install/ans2out $OJ_DATA/$pid/");
echo "<br>";
echo htmlentities("unzip $OJ_DATA/$pid/data.zip -d $OJ_DATA/$pid");
echo system("unzip $OJ_DATA/$pid/data.zip -d $OJ_DATA/$pid");
echo system("/usr/bin/loj.ac $OJ_DATA/$pid");
echo "<br>";
	
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
Copy from https://loj.ac/problem/
<form method=POST action=problem_add_page_loj.php>
  <input name=url type=text size=100 value="https://loj.ac/problem/<?php echo $loj_id+1?>">
  <input type=submit>
</form>



