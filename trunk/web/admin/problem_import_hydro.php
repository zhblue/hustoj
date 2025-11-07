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
function replaceLT($string) {
    // 正则表达式匹配两个美元符号包裹的内容
    $pattern = '/(\$.*?)(<)(.*?\$)/';
    // 替换小于号为\lt
    $replacement = '$1 \\lt $3';

    // 使用preg_replace进行替换
    $ret= preg_replace($pattern, $replacement, $string);
    if (strlen($string)==strlen($ret))
    	return $ret;
    else
    	return preg_replace($pattern, $replacement, $ret);    //递归到不再发生变化
}

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
  global $OJ_DATA,$OJ_SAE,$OJ_REDIS,$OJ_REDISSERVER,$OJ_REDISPORT,$OJ_REDISQNAME,$domain,$DOMAIN,$_SESSION;
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

if ($_FILES["fps"]["error"] > 0) {
    echo "&nbsp;&nbsp;- Error: " . $_FILES["fps"]["error"] . " File size is too big, change in PHP.ini<br />";
} else {
    $tempdir = sys_get_temp_dir() . "/import_hydro" . time();
    mkdir($tempdir);
    $tempfile = $_FILES["fps"]["tmp_name"];

    if (get_extension($_FILES["fps"]["name"]) == "zip") {
        echo "&nbsp;&nbsp;- zip file, only HydroOJ exported file is supported<hr>\n";

        $zip = new ZipArchive();
        if ($zip->open($tempfile) !== TRUE) {
            die("Could not open ZIP archive.");
        }

        $save_path = "";
        $i = 1;
        $pid = $title = $description = $input = $output = $sample_input = $sample_output = $hint = $source = $spj = "";
        $type = "normal";
        $inserted = [];

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $file_name = $zip->getNameIndex($index);
            if ($file_name === false) continue;

            $file_path = dirname($file_name);
            $file_content = $zip->getFromIndex($index);
            if ($file_content === false) continue;

            if (basename($file_name) == "problem.yaml") {
                $hydrop = yaml_parse($file_content);
                $title = $hydrop['title'];
                $source = implode(" ", $hydrop['tag']);
                echo "<hr>" . htmlentities($file_name . " $title $source");

                if (!in_array($title, $inserted)) {
                    $pid = addproblem($title, 1, 128, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $OJ_DATA);
                    mkdir($OJ_DATA . "/$pid/");
                    array_push($inserted, $title);

                    $sql = "INSERT INTO `privilege` (`user_id`,`rightstr`) VALUES(?,?)";
                    pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id'], "p$pid");
                    $_SESSION[$OJ_NAME . '_' . "p$pid"] = true;
                }
            } elseif (basename($file_name) == "problem_zh.md" || basename($file_name) == "problem.md") {
                $file_content = replaceLT($file_content);

                if ($type == "normal") {
                    $description = "<span class=\"md\">" . $file_content . "</span>";
                } else {
                    $description = "<span class=\"md auto_select\">" . $file_content . "</span>";
                }

                $description = preg_replace('/{{ select\(\d+\) }}/', "", $description);

                if ($save_path) {
                    $description = str_replace("file://", $save_path . "/", $description);
                }

                $spj = 0;
                if ($title != "" && !in_array($title, $inserted) && !hasProblem($title)) {
                    $pid = addproblem($title, 1, 128, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $OJ_DATA);
                    echo htmlentities("$description");
                    mkdir($OJ_DATA . "/$pid/");
                    array_push($inserted, $title);

                    $sql = "INSERT INTO `privilege` (`user_id`,`rightstr`) VALUES(?,?)";
                    pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id'], "p$pid");
                    $_SESSION[$OJ_NAME . '_' . "p$pid"] = true;
                } else {
                    $sql = "UPDATE problem SET description=? WHERE problem_id=?";
                    pdo_query($sql, $description, $pid);
                    echo "skipped $title";
                }

                echo "PID:<a href='../problem.php?id=$pid'>" . htmlentities($title, ENT_QUOTES, "UTF-8") . "</a>";
            } elseif (basename($file_name) == "config.yaml") {
                $hydrop = yaml_parse($file_content);

                if ($hydrop['type'] == "objective") {
                    $type = "objective";
                    echo $type . ":dump answers";
                    $ansi = 1;
                    $out = "";
                    while (!empty($hydrop['answers'][$ansi])) {
                        $out .= $ansi . " [" . $hydrop['answers'][$ansi][1] . "] ";
                        $out .= $hydrop['answers'][$ansi][0] . "\n";
                        $ansi++;
                    }

                    if ($pid > 0) {
                        file_put_contents($OJ_DATA . "/$pid/data.out", $out);
                        file_put_contents($OJ_DATA . "/$pid/data.in", ($ansi - 1) . "\n");
                    }

                    $template = "";
                    for ($k = 1; $k < $ansi; $k++) {
                        $template .= $k . "\n";
                    }
                    file_put_contents($OJ_DATA . "/$pid/template.c", $template);

                    pdo_query("UPDATE problem SET spj=2, description=REPLACE(description,'<span class=\"md\">','<span class=\"md auto_select\">') WHERE problem_id=?", $pid);
                } else {
                    if (endsWith($hydrop['time'], "ms")) {
                        $hydrop['time'] = substr($hydrop['time'], 0, -2);
                        $hydrop['time'] = floatval($hydrop['time']) / 1000;
                    } elseif (endsWith($hydrop['time'], "s")) {
                        $hydrop['time'] = substr($hydrop['time'], 0, -1);
                        $hydrop['time'] = floatval($hydrop['time']);
                    }

                    $time = floatval($hydrop['time']);
                    $memory = floatval($hydrop['memory']);
                    $iofile = $hydrop['filename'];

                    if ($pid != "" && $iofile != "") {
                        file_put_contents($OJ_DATA . "/$pid/input.name", $iofile . ".in\n");
                        file_put_contents($OJ_DATA . "/$pid/output.name", $iofile . ".out\n");
                    }

                    if ($time > 0) {
                        pdo_query("UPDATE problem SET time_limit=?, memory_limit=? WHERE problem_id=?", $time, $memory, $pid);
                    }
                }
            } elseif ($pid != "" && strpos($file_path, "testdata") !== false && basename($file_name) != "testdata") {
                echo ".";
                $dataname = basename($file_name);

                if (endsWith($dataname, ".txt")) {
                    $dataname = preg_replace('/input([0-9]*).txt/i', '\\1.in', $dataname);
                    $dataname = preg_replace('/output([0-9]*).txt/i', '\\1.out', $dataname);
                } elseif (endsWith($dataname, "put")) {
                    $dataname = substr($dataname, 0, -3);
                } elseif (endsWith($dataname, ".ans")) {
                    $dataname = substr($dataname, 0, -3) . "out";
                } elseif (endsWith($dataname, ".in")) {
                    $dataname = substr($dataname, 0, -2) . "in";
                } elseif (endsWith($dataname, ".out")) {
                    $dataname = substr($dataname, 0, -3) . "out";
                }

                file_put_contents($OJ_DATA . "/$pid/" . $dataname, $file_content);
            } elseif (strpos($file_path, "additional_file") !== false && basename($file_name) != "additional_file") {
                $ext = strtolower(get_extension($file_name));

                if (!stristr(",jpeg,jpg,svg,png,gif,bmp,xlsx,xls,doc,docx", $ext)) {
                    continue;
                }

                $new_file_name = basename($file_name);
                $newpath = $save_path . "/" . $new_file_name;

                if ($OJ_SAE) {
                    $newpath = "saestor://web" . $newpath;
                } else {
                    $newpath = ".." . $newpath;
                }

                if (!file_exists(dirname($newpath))) {
                    mkdir(dirname($newpath), 0750, true);
                }

                file_put_contents($newpath, $file_content);
            }
        }

        $zip->close();
        unlink($_FILES["fps"]["tmp_name"]);
        rmdir($tempdir);
    } else {
        echo ($tempfile);
    }
}

