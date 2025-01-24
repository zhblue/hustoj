<?php
require_once("admin-header.php");

if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
        echo "<a href='../loginpage.php'>Please Login First!</a>";
        exit(1);
}
?>

<div class="padding">
<br>
<br>
<br>
<br>

<?php
if(isset($_POST['do'])){

require_once(dirname(__FILE__)."/../include/backup.php");
$target=$OJ_DATA."/0/".$DB_NAME."_".(date('Y-m-d H:i:s')).".sql";
$db_file=basename($target);
$dirpath=dirname($target);
if (!file_exists($dirpath)) {
        mkdir($dirpath);
}
$config = array(
        'host' => $DB_HOST,
        'port' => 3306,
        'user' => $DB_USER,
        'password' => $DB_PASS,
        'database' => $DB_NAME,
        'charset' => 'utf-8',
        'target' => $target
    );
$bak = new DatabaseTool($config);
$bak->backup();

function addDirToZip($path, $zip) {
        global $OJ_DATA;
        if ($path=="data/0") return;
        $handler = opendir($path); //打开当前文件夹由$path指定。
        /*
        循环的读取文件夹下的所有文件和文件夹
        其中$filename = readdir($handler)是每次循环的时候将读取的文件名赋值给$filename，
        为了不陷于死循环，所以还要让$filename !== false。
        一定要用!==，因为如果某个文件名如果叫'0'，或者某些被系统认为是代表false，用!=就会停止循环
        */
        while (($filename = readdir($handler)) !== false) {
                if ($filename != "." && $filename != "..") {//文件夹文件名字为'.'和‘..’，不要对他们进行操作
                        if (is_dir($path . "/" . $filename)) {// 如果读取的某个对象是文件夹，则递归
                                addDirToZip($path . "/" . $filename, $zip);
                        } else { //将文件加入zip对象
                                $zip->addFile($path . "/" . $filename);
                        }
                }
        }
        @closedir($path);
}

$zip = new ZipArchive();
$ztar=dirname($target)."/backup_".(date('Y-m-d H:i:s')).".zip";;
//echo $ztar;
if ($zip->open($ztar, ZipArchive::CREATE) === TRUE) {
        chdir( dirname($OJ_DATA) );
        if(is_dir("data"))
                addDirToZip("data", $zip); //测试数据
        chdir(realpath(dirname(dirname(__FILE__))));
        if($OJ_SaaS_ENABLE && $DOMAIN!=$domain){
                if(is_dir("upload/$domain"))
                        addDirToZip("upload/$domain", $zip); //子域题目图片
        }else{
                if(is_dir("upload/"))
                        addDirToZip("upload", $zip); //题目图片
        }
        chdir($OJ_DATA."/0");
        if(file_exists($db_file)){
                $zip->addFile($db_file);
        }
        $zip->close(); //关闭处理的zip文件
}

?>


<?php
}else{
?>
<br>
<br>
   <form method="post" action="backup.php">
        <input type="submit" name="do" value="Backup">
   </form>

<?php


}
?>

<button onclick="phpfm(0)" >查看备份文件</button>
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
</div>
