<?php
require_once("admin-header.php");
require_once("../include/check_post_key.php");

if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_problem_importer']))) {
    echo "<a href='../loginpage.php'>Please Login First!</a>";
    exit(1);
}

if (isset($OJ_LANG)) {
    require_once("../lang/$OJ_LANG.php");
}

require_once("../include/const.inc.php");
?>

<?php

function image_save_file($filepath, $base64_encoded_img)
{
    $dirpath = dirname($filepath);
    if (!file_exists($dirpath)) {
        mkdir($dirpath, 0755, true);
    }
    $fp = fopen($filepath, "wb");
    fwrite($fp, base64_decode($base64_encoded_img));
    fclose($fp);
}

require_once("../include/problem.php");
require_once("../include/db_info.inc.php");

function getLang($language)
{
    // PHP 8 protection: Ensure globals are arrays before counting
    $language_name = $GLOBALS['language_name'] ?? [];
    $language_ext = $GLOBALS['language_ext'] ?? [];

    if (!is_array($language_name)) return 0;

    for ($i = 0; $i < count($language_name); $i++) {
        // compatibility with other onlinejudge FPS implementation might using extension name as language 
        if (isset($language_ext[$i]) && $language == $language_ext[$i]) {
            return $i;
        }
        // HUSTOJ classic using language_name
        if ($language == $language_name[$i]) {
            return $i;
        }
    }
    return $i;
}

function submitSolution($pid, $solution, $language)
{
    global $OJ_NAME, $_SESSION;
    $language = getLang($language);
    // PHP 8: handle null solution
    $solution_str = $solution ?? '';
    $len = mb_strlen($solution_str, 'utf-8');
    
    $user_id = $_SESSION[$OJ_NAME . '_' . 'user_id'];
    $sql = "SELECT nick FROM users WHERE user_id=?";
    $nick = pdo_query($sql, $user_id);
    if ($nick) {
        $nick = $nick[0][0];
    } else {
        $nick = "Guest";
    }

    $sql = "INSERT INTO solution(problem_id,user_id,nick,in_date,language,ip,code_length,result) VALUES(?,?,?,NOW(),?,'127.0.0.1',?,14)";
    $insert_id = pdo_query($sql, $pid, $_SESSION[$OJ_NAME . '_' . 'user_id'], $nick, $language, $len);

    $sql = "INSERT INTO `source_code`(`solution_id`,`source`) VALUES(?,?)";
    pdo_query($sql, $insert_id, $solution_str);

    $sql = "INSERT INTO `source_code_user`(`solution_id`,`source`) VALUES(?,?)";
    pdo_query($sql, $insert_id, $solution_str);
    
    pdo_query("UPDATE solution SET result=1 WHERE solution_id=?", $insert_id);
    pdo_query("UPDATE problem SET submit=submit+1 WHERE problem_id=?", $pid);
}
?>

<hr>
&nbsp;&nbsp;- Import Problem ... <br>
&nbsp;&nbsp;- 如果导入失败，请参考 <a href="https://github.com/zhblue/hustoj/blob/master/wiki/FAQ.md#%E5%90%8E%E5%8F%B0%E5%AF%BC%E5%85%A5%E9%97%AE%E9%A2%98%E5%A4%B1%E8%B4%A5" target="_blank">FAQ</a>。
<br>
<a href="problem_list.php" > Return to Problem List </a>
<br>

<?php
function getValue($Node, $TagName)
{
    return $Node->$TagName;
}

function getAttribute($Node, $TagName, $attribute)
{
    return $Node->children()->$TagName->attributes()->$attribute;
}

function hasRemoteProblem($remote_oj, $remote_id)
{
    if ($remote_oj == "" || $remote_id == "") return false;
    // $title variable was undefined in original code here, likely meant remote_id check logic.
    // Preserving logic structure but $md5 line was effectively useless in original function context without $title passed in.
    
    $sql = "SELECT 1 FROM problem WHERE remote_oj=? and remote_id=?";
    $result = pdo_query($sql, $remote_oj, $remote_id);
    $rows_cnt = count($result);
    return ($rows_cnt > 0);
}

function hasProblem($title)
{
    $md5 = md5($title);
    $sql = "SELECT 1 FROM problem WHERE md5(title)=?";
    $result = pdo_query($sql, $md5);
    $rows_cnt = count($result);
    return ($rows_cnt > 0);
}

function mkpta($pid, $prepends, $node)
{
    $language_ext = $GLOBALS['language_ext'];
    $OJ_DATA = $GLOBALS['OJ_DATA'];

    foreach ($prepends as $prepend) {
        $language = (string)$prepend->attributes()->language;
        $lang = getLang($language);
        $file_ext = $language_ext[$lang];
        $basedir = "$OJ_DATA/$pid";
        $file_name = "$basedir/$node.$file_ext";
        file_put_contents($file_name, $prepend);
    }
}

function get_extension($file)
{
    $info = pathinfo($file);
    return $info['extension'] ?? '';
}

function import_fps($tempfile)
{
    global $OJ_DATA, $OJ_SAE, $OJ_REDIS, $OJ_REDISSERVER, $OJ_REDISPORT, $OJ_REDISQNAME, $domain, $DOMAIN, $OJ_NAME, $OJ_REMOTE_JUDGE;
    
    $xmlDoc = simplexml_load_file($tempfile, 'SimpleXMLElement', LIBXML_PARSEHUGE);
    if ($xmlDoc === false) {
        echo "Failed to parse XML file.<br>";
        return;
    }

    $searchNodes = $xmlDoc->xpath("/fps/item");
    $spid = 0;

    foreach ($searchNodes as $searchNode) {
        $title = (string)$searchNode->title;
        $time_limit = (float)$searchNode->time_limit;
        $unit = (string)getAttribute($searchNode, 'time_limit', 'unit');

        if ($unit == 'ms')
            $time_limit /= 1000;

        $memory_limit = intval(getValue($searchNode, 'memory_limit'));
        $unit = (string)getAttribute($searchNode, 'memory_limit', 'unit');

        if ($unit == 'kb')
            $memory_limit /= 1024;

        $description = (string)getValue($searchNode, 'description');
        $input = (string)getValue($searchNode, 'input');
        $output = (string)getValue($searchNode, 'output');
        $sample_input = (string)getValue($searchNode, 'sample_input');
        $sample_output = (string)getValue($searchNode, 'sample_output');
        $hint = (string)getValue($searchNode, 'hint');
        $source = (string)getValue($searchNode, 'source');
        $spjcode = (string)getValue($searchNode, 'spj');
        $remote_oj = (string)getValue($searchNode, 'remote_oj');
        $remote_id = (string)getValue($searchNode, 'remote_id');

        $spjlang = "";
        if ($spjcode) $spjlang = (string)getAttribute($searchNode, 'spj', 'language');
        
        $tpjcode = (string)getValue($searchNode, 'tpj');
        $tpjlang = "";
        if ($tpjcode) $tpjlang = (string)getAttribute($searchNode, 'tpj', 'language');
        
        $spj = trim($spjcode . $tpjcode) ? 1 : 0;
        if ($spjlang == "Text") $spj = 2;

        if (hasRemoteProblem($remote_oj, $remote_id)) {
            $sql = "update problem set title=?,time_limit=?,memory_limit=?,description=?,input=?,output=?,sample_input=?,sample_output=?,hint=?,source=?,spj=? where remote_oj=? and remote_id=?";
            pdo_query($sql, $title, $time_limit, $memory_limit, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $remote_oj, $remote_id);
        } else {
            $tail = 0;
            $ptitle = $title;
            while (hasProblem($ptitle)) {
                $tail++;
                $ptitle = $title . "_" . $tail;
            }
            $title = $ptitle;

            $pid = addproblem($title, $time_limit, $memory_limit, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $OJ_DATA);
            
            if ($OJ_REMOTE_JUDGE && $remote_oj != "") {
                $sql = "update problem set remote_oj=?,remote_id=? where problem_id=?";
                pdo_query($sql, $remote_oj, $remote_id, $pid);
            }
            if ($spid == 0)
                $spid = $pid;
            
            $sql = "INSERT INTO `privilege` (`user_id`,`rightstr`) VALUES(?,?)";
            pdo_query($sql, $_SESSION[$OJ_NAME . '_' . 'user_id'], "p$pid");
            $_SESSION[$OJ_NAME . '_' . "p$pid"] = true;

            $basedir = "$OJ_DATA/$pid";
            if(!file_exists($basedir)) mkdir($basedir);

            if (strlen($sample_input)) mkdata($pid, "sample.in", $sample_input, $OJ_DATA);
            if (strlen($sample_output)) mkdata($pid, "sample.out", $sample_output, $OJ_DATA);

            $testinputs = $searchNode->children()->test_input;
            $testno = 0;

            foreach ($testinputs as $testNode) {
                $name = (string)$testNode['name'];
                if ($name != "") {
                    mkdata($pid, $name . ".in", $testNode, $OJ_DATA);
                } else {
                    mkdata($pid, "test" . $testno . ".in", $testNode, $OJ_DATA);
                }
                $testno++;
            }

            unset($testinputs);
            $testinputs = $searchNode->children()->test_output;
            $testno = 0;

            foreach ($testinputs as $testNode) {
                $name = (string)$testNode['name'];
                if ($name != "") {
                    mkdata($pid, $name . ".out", $testNode, $OJ_DATA);
                } else {
                    mkdata($pid, "test" . $testno . ".out", $testNode, $OJ_DATA);
                }
                $testno++;
            }

            unset($testinputs);

            $images = ($searchNode->children()->img);
            $did = array();
            $testno = 0;

            foreach ($images as $img) {
                $src = (string)getValue($img, "src");

                if (!in_array($src, $did)) {
                    $base64 = getValue($img, "base64");
                    $ext = pathinfo($src);
                    $ext = strtolower($ext['extension']);

                    if (!stristr(",jpeg,jpg,svg,png,gif,bmp", $ext)) {
                        $ext = "bad";
                        exit(1);
                    }

                    $testno++;
                    $ymd = $domain . "/" . date("Ymd");
                    $save_path = $ymd . "/";
                    // New file name
                    $new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $ext;
                    $newpath = $save_path . "/$pid" . "_" . $testno . "_" . $new_file_name;
                    if ($OJ_SAE)
                        $newpath = "saestor://web/upload/" . $newpath;
                    else
                        $newpath = "../upload/" . $newpath;

                    image_save_file($newpath, $base64);
                    
                    // Update database paths
                    $sql = "UPDATE problem SET description=replace(description,?,?) WHERE problem_id=?";
                    pdo_query($sql, $src, $newpath, $pid);

                    $sql = "UPDATE problem SET input=replace(input,?,?) WHERE problem_id=?";
                    pdo_query($sql, $src, $newpath, $pid);

                    $sql = "UPDATE problem SET output=replace(output,?,?) WHERE problem_id=?";
                    pdo_query($sql, $src, $newpath, $pid);

                    $sql = "UPDATE problem SET hint=replace(hint,?,?) WHERE problem_id=?";
                    pdo_query($sql, $src, $newpath, $pid);
                    array_push($did, $src);
                }
            }

            if (!isset($OJ_SAE) || !$OJ_SAE) {
                if ($spj == 1) {
                    if ($spjcode) {
                        $basedir = "$OJ_DATA/$pid";
                        if ($spjlang == "C++") {
                            $fp = fopen("$basedir/spj.cc", "w");
                            fputs($fp, $spjcode);
                            fclose($fp);
                        } else {
                            $fp = fopen("$basedir/spj.c", "w");
                            fputs($fp, $spjcode);
                            fclose($fp);
                        }
                        if (!file_exists("$basedir/spj")) {
                            echo "you need to compile $basedir/spj.cc for spj[  g++ -o $basedir/spj $basedir/spj.cc   ]<br> and rejudge $pid";
                        }
                    }
                    
                    if ($tpjcode) {
                        $basedir = "$OJ_DATA/$pid";
                        if ($tpjlang == "C++") {
                            $fp = fopen("$basedir/tpj.cc", "w");
                            fputs($fp, $tpjcode);
                            fclose($fp);
                        } else {
                            $fp = fopen("$basedir/tpj.c", "w");
                            fputs($fp, $tpjcode); // Original code used $spjcode here, kept logically but suspect it should be $tpjcode? Leaving as per original flow if intended, but assuming tpjcode based on context.
                            fclose($fp);
                        }
                        if (!file_exists("$basedir/tpj")) {
                            echo "you need to compile $basedir/tpj.cc for tpj[  g++ -o $basedir/tpj $basedir/tpj.cc   ]<br> and rejudge $pid";
                        }
                    }
                }
            }

            $solutions = $searchNode->children()->solution;

            foreach ($solutions as $solution) {
                $language = (string)$solution->attributes()->language;
                submitSolution($pid, (string)$solution, $language);
            }
            unset($solutions);

            $prepends = $searchNode->children()->prepend;
            mkpta($pid, $prepends, "prepend");

            $prepends = $searchNode->children()->template;
            mkpta($pid, $prepends, "template");

            $prepends = $searchNode->children()->append;
            mkpta($pid, $prepends, "append");
        }
    }

    unlink($tempfile);

    if (isset($OJ_REDIS) && $OJ_REDIS) {
        $redis = new Redis();
        $redis->connect($OJ_REDISSERVER, $OJ_REDISPORT);
        $sql = "SELECT solution_id FROM solution WHERE result=0 AND problem_id>0";
        $result = pdo_query($sql);

        foreach ($result as $row) {
            echo $row['solution_id'] . "\n";
            $redis->lpush($OJ_REDISQNAME, $row['solution_id']);
        }
    }

    if ($spid > 0) {
        require_once("../include/set_get_key.php");
        //echo "<br><a class=blue href=contest_add.php?spid=$spid&getkey=".$_SESSION[$OJ_NAME.'_'.'getkey'].">Use these problems to create a contest.</a>";
    }
}


if ($_FILES["fps"]["error"] > 0) {
    echo "&nbsp;&nbsp;- Error: " . $_FILES["fps"]["error"] . "File size is too big, change in PHP.ini<br />";
} else {
    $tempfile = $_FILES["fps"]["tmp_name"];
    if (get_extension($_FILES["fps"]["name"]) == "zip") {
        echo "&nbsp;&nbsp;- zip file, only fps/xml files in root dir are supported";
        
        // --- REFACRORED: PHP 8 compatible ZipArchive ---
        $zip = new ZipArchive;
        if ($zip->open($tempfile) === TRUE) {
            $tempDir = sys_get_temp_dir();
            
            for($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                
                // Skip directories
                if(substr($filename, -1) == '/') continue;
                
                // We only want files in the root of the zip (following original logic implication)
                // or just allow flat structure processing.
                // Original logic read the file content and saved to a temp file.
                
                $fileContent = $zip->getFromIndex($i);
                if($fileContent !== false) {
                    $xmlTempFile = tempnam($tempDir, "fps");
                    file_put_contents($xmlTempFile, $fileContent);
                    import_fps($xmlTempFile);
                    // import_fps unlinks the file at the end, so we don't need to unlink here usually
                    // but duplicate unlink is harmless if import_fps failed halfway.
                    if(file_exists($xmlTempFile)) unlink($xmlTempFile);
                }
            }
            $zip->close();
        } else {
             echo "&nbsp;&nbsp;- Failed to open zip file.";
        }
        unlink($_FILES["fps"]["tmp_name"]);
        // -----------------------------------------------
        
    } else {
        import_fps($tempfile);
    }
}

if (isset($OJ_UDP) && $OJ_UDP) {
    $JUDGE_SERVERS = explode(",", $OJ_UDPSERVER);
    $JUDGE_TOTAL = count($JUDGE_SERVERS);

    $select = $insert_id % $JUDGE_TOTAL;
    $JUDGE_HOST = $JUDGE_SERVERS[$select];

    if (strstr($JUDGE_HOST, ":") !== false) {
        $JUDGE_SERVERS = explode(":", $JUDGE_HOST);
        $JUDGE_HOST = $JUDGE_SERVERS[0];
        $OJ_UDPPORT = $JUDGE_SERVERS[1];
    }
    if (isset($OJ_JUDGE_HUB_PATH))
        send_udp_message($JUDGE_HOST, $OJ_UDPPORT, $OJ_JUDGE_HUB_PATH);
    else
        send_udp_message($JUDGE_HOST, $OJ_UDPPORT, 0);
}
