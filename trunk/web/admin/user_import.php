<?php
ini_set("display_errors", "On");  //set this to "On" for debugging  ,especially when no reason blank shows up.
require_once ("admin-header.php");
if (!(isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_problem_importer']))) {
    echo "<a href='../loginpage.php'>Please Login First!</a>";
    exit(1);
}
if (isset($OJ_LANG)) {
    require_once ("../lang/$OJ_LANG.php");
}
require_once ("../include/const.inc.php");
require_once ("../include/my_func.inc.php");
?>

<?php
function get_extension($file) {
    $info = pathinfo($file);
    return $info['extension'];
}
function import_user($filename) {
    global $OJ_EXPIRY_DAYS,$MSG_EXPIRY_DATE;
    $check=false;
    $expire=false;
    if (($h = fopen("{$filename}", "r")) !== FALSE) {
        // 文件中的每一行数据都被转换为我们调用的单个数组$data
        // 数组的每个元素以逗号分隔
	$bom = fread($h, 3);  // 文件有BOM，跳过这三个字节
        if ($bom !== "\xEF\xBB\xBF") {
            fseek($h, 0);  // 文件没有BOM，退回文件头部
        }

        while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
         // 每个单独的数组都被存入到嵌套的数组中
	    if(!$check){
                if ($data[0] == "学号") {
                        $check=true;
                        echo "导入名单：<hr>\n";
                        $gb2312=false;
			if(isset($data[6])) $expire=($data[6]==$MSG_EXPIRY_DATE );
                        continue;
                }else if (iconv("gb2312","utf-8",$data[0])=="学号") {
                        $check=true;
                        $gb2312=true;
			$expire=(iconv("gb2312","utf-8",$data[6])==$MSG_EXPIRY_DATE );
                        continue;
                }
	    }
            if($check){
                    $user_id = mb_trim($data[0]);
                    $nick = $data[1];
                    $password = pwGen(trim($data[2]));
                    $school = "";
                    $email = "";
                    $group_name="";
		    $expiry_date=add_days($OJ_EXPIRY_DAYS);
                    if (isset($data[3])) $school = $data[3];
                    if (isset($data[4])) $email = $data[4];
                    if (isset($data[5])) $group_name = $data[5];
                    if (isset($data[6])) $expiry_date = $data[6];
                    if($gb2312) {
                            $nick=iconv("gb2312","utf-8",$nick);
                            $school=iconv("gb2312","utf-8",$school);
                            $group_name=iconv("gb2312","utf-8",$group_name);
                            $expiry_date=iconv("gb2312","utf-8",$expiry_date);
                    }
		    if (!is_date($expiry_date)&&is_numeric($expiry_date)) $expiry_date=add_days($expiry_date);
                    if (mb_strlen($nick, 'utf-8') > 20) {
                        $new_len = mb_strlen($nick, 'utf-8');
                        if ($new_len > $max_length) {
                            $max_length = $new_len;
                            $longer = "ALTER TABLE `users` MODIFY COLUMN `nick` varchar($max_length) NULL DEFAULT '' ";
                            pdo_query($longer);
                        }
                    }

                    $ip = "127.0.0.1";
                    $sql = "INSERT INTO `users`(" . "`user_id`,`email`,`ip`,`accesstime`,`password`,`reg_time`,`nick`,`school`,`group_name`,`expiry_date`)" . "VALUES(?,?,?,NOW(),?,NOW(),?,?,?,?)on DUPLICATE KEY UPDATE `email`=?,`ip`=?,`accesstime`=NOW(),`password`=?,`reg_time`=now(),nick=?,`school`=?,`group_name`=?,expiry_date=?";
                    $ret=pdo_query($sql, $user_id, $email, $ip, $password, $nick, $school,$group_name,$expiry_date, $email, $ip, $password, $nick, $school,$group_name,$expiry_date);
                    echo "$user_id : $expiry_date : $ret <br>\n";

            }else{
                echo "<h1>请用下载的模板填写，保存为UTF-8编码。</h1>";
                break;
            }
        }
        // 关闭文件
        fclose($h);
    }
}

if (isset($_FILES["fps"])) {
    if ($_FILES["fps"]["error"] > 0) {
        echo "&nbsp;&nbsp;- Error: " . $_FILES["fps"]["error"] . "File size is too big, change in PHP.ini<br />";
    } else {
        $tempfile = $_FILES["fps"]["tmp_name"];
        if (get_extension($_FILES["fps"]["name"]) == "zip") {
            echo "&nbsp;&nbsp;- zip file, only fps/xml files in root dir are supported";
            $resource = zip_open($tempfile);
            $i = 1;
            $tempfile = tempnam("/tmp", "fps");
            while ($dir_resource = zip_read($resource)) {
                if (zip_entry_open($resource, $dir_resource)) {
                    $file_name = $path . zip_entry_name($dir_resource);
                    $file_path = substr($file_name, 0, strrpos($file_name, "/"));
                    if (!is_dir($file_name)) {
                        $file_size = zip_entry_filesize($dir_resource);
                        $file_content = zip_entry_read($dir_resource, $file_size);
                        file_put_contents($tempfile, $file_content);
                        import_user($tempfile);
                    }
                    zip_entry_close($dir_resource);
                }
            }
            zip_close($resource);
            unlink($_FILES["fps"]["tmp_name"]);
        } else {
            import_user($tempfile);
            unlink($_FILES["fps"]["tmp_name"]);
        }
    }
} else {
?>

<br>
<br>
<h1>导入用户csv文件</h1>
    <form class='form-inline' action='user_import.php' method=post enctype="multipart/form-data">
      <div class='form-group'>
        <input class='form-control' type=file name='fps' >
      </div>
      <br><br>
      <br><br><br>
      <center>
      <div class='form-group'>
        <button class='btn btn-default btn-sm' type=submit>Upload to HUSTOJ</button>
      </div>
      </center>
      <?php require_once ("../include/set_post_key.php"); ?>
    </form>
<h2><a href="users.csv">下载模板</a></h2>
<h3>请用下载的模板填写，保存为UTF-8编码。</h3>
<?php
} ?>
