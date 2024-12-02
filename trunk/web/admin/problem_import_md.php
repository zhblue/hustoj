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

function create_upload_dir($base_dir)
{
    $date_dir = date("Ymd");
    $full_path = $base_dir . "/" . $date_dir;

    if (!file_exists($full_path)) {
        mkdir($full_path, 0755, true);
    }

    return $full_path;
}

function process_md_and_images($zip_path, $upload_dir)
{
    $zip = new ZipArchive();
    if ($zip->open($zip_path) === TRUE) {
        $upload_date_dir = create_upload_dir($upload_dir);
        $temp_dir = sys_get_temp_dir() . "/md_import_" . uniqid();

        if (!file_exists($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }

        // Extract all files to a temporary directory
        $zip->extractTo($temp_dir);
        $zip->close();

        $md_files = glob($temp_dir . "/*.md");
        $output_files = [];

        foreach ($md_files as $md_file) {
            $md_content = file_get_contents($md_file);

            // Find and process image paths in the Markdown file
            $md_content = preg_replace_callback('/!\[.*?\]\((.*?)\)/', function ($matches) use ($temp_dir, $upload_date_dir) {
                $original_path = $matches[1];
                $image_path = realpath($temp_dir . "/" . $original_path);

                if (file_exists($image_path)) {
                    // Create a unique name for the uploaded image
                    $image_ext = pathinfo($image_path, PATHINFO_EXTENSION);
                    $new_image_name = date("YmdHis") . "_" . uniqid() . "." . $image_ext;
                    $new_image_path = $upload_date_dir . "/" . $new_image_name;

                    if (copy($image_path, $new_image_path)) {
                        // Return the new path in Markdown format
                        return "![{$new_image_name}](/upload/image/" . basename($upload_date_dir) . "/" . $new_image_name . ")";
                    }
                }

                return $matches[0]; // Return original if unable to process
            }, $md_content);

            $output_files[] = [
                'filename' => basename($md_file),
                'content' => $md_content
            ];
        }

        // Cleanup the temp directory
        array_map('unlink', glob("$temp_dir/*.*"));
        rmdir($temp_dir);

        return $output_files;
    } else {
        return [];
    }
}

if ($_FILES ["fps"] ["error"] > 0) {
  echo "&nbsp;&nbsp;- Error: ".$_FILES ["fps"] ["error"]."File size is too big, change in PHP.ini<br />";
}
else {
  $tempdir = sys_get_temp_dir()."/import_markdown".time();	
  mkdir($tempdir, 0755, true); // 确保目录存在并可写
  $tempfile = $_FILES ["fps"] ["tmp_name"];
  if (get_extension( $_FILES ["fps"] ["name"])=="zip") {
    echo "&nbsp;&nbsp;- zip file, only Markdown .md file is supported<hr>\n";

    $upload_dir = "/home/judge/src/web/upload/image";
    $output_files = process_md_and_images($tempfile, $upload_dir);

    foreach ($output_files as $file) {
        $hydrop=explode("\n",$file['content']);	
        $title=str_replace("#","",$hydrop[0]);	
        $title=str_replace("\r","",$title);	
        $source="";	
        echo "<hr>".htmlentities($file['filename']." $title $source");
        $regex = '/<(?!div)/';
        $regex = '/(?<!div)>\s?/';
        $description="<span class=\"md\">".$file['content']."</span>";
        $description=preg_replace('/{{ select\(\d+\) }}/', "", $description); 
        $tail=0;
        $ptitle = $title;
        while (hasProblem($ptitle)) {
          $tail++;
          $ptitle = $title."_".$tail;
        }
        $title=$ptitle;

        // 初始化变量
        $input = "";
        $output = "";
        $sample_input = "";
        $sample_output = "";
        $hint = "";
        $spj = 0;

        $pid = addproblem($title,1,128, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $OJ_DATA);
        if (!file_exists($OJ_DATA."/$pid/")) {
            mkdir($OJ_DATA."/$pid/", 0755, true);
        }
        echo "PID:<a href='../problem.php?id=$pid' >".htmlentities($title,ENT_QUOTES,"UTF-8")."</a>";
    }

    unlink ( $_FILES ["fps"] ["tmp_name"] );
    rmdir ($tempdir);
  } else {
    echo "&nbsp;&nbsp;- Only zip files are supported<br />";
  }
}
?>
