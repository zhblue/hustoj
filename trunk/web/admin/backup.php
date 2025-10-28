<?php
require_once("admin-header.php");

if (!isset($_SESSION[$OJ_NAME.'_administrator'])){
    echo "<a href='../loginpage.php'>Please Login First!</a>";
    exit(1);
}
?>

<div class="padding">
<br><br><br><br>

<?php
if(isset($_POST['do'])){

require_once(dirname(__FILE__)."/../include/backup.php");

// 安全时间字符串，避免冒号
$time_str = date('Y-m-d_H:i:s');
$target = $OJ_DATA."/0/".$DB_NAME."_".$time_str.".sql";
$db_file = basename($target);
$dirpath = dirname($target);

// 确保目录存在且可写
if (!file_exists($dirpath)) {
    mkdir($dirpath, 0755, true);
}

$config = [
    'host' => $DB_HOST,
    'port' => 3306,
    'user' => $DB_USER,
    'password' => $DB_PASS,
    'database' => $DB_NAME,
    'charset' => 'utf8mb4',
    'target' => $target
];

$bak = new DatabaseTool($config);
if (!$bak->backup()) {
    echo "数据库备份失败: ".$bak->getError();
    exit;
}

// 修正 addDirToZip 函数
function addDirToZip($path, $zip) {
    global $OJ_DATA;
    if ($path == "data/0") return;
    $handler = opendir($path);
    if(!$handler) return;

    while (($filename = readdir($handler)) !== false) {
        if ($filename != "." && $filename != "..") {
            $fullpath = realpath(rtrim($path,'/')."/".$filename);
                if(file_exists($fullpath)){
                    if (is_dir($fullpath)) {
                        addDirToZip($fullpath, $zip);
                    } else {
                        $zip->addFile($fullpath, $fullpath); // 保留相对路径
                    }
                }
        }
    }
    closedir($handler);
}

// ZIP 文件路径安全化
$ztar = dirname($target)."/backup_".$time_str.".zip";
$zip = new ZipArchive();

if ($zip->open($ztar, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    chdir(dirname($OJ_DATA));
    if (is_dir("data"))
        addDirToZip("data", $zip); // 测试数据
    chdir(realpath(dirname(dirname(__FILE__))));
    if ($OJ_SaaS_ENABLE && $DOMAIN != $domain) {
        if (is_dir("upload/$domain"))
            addDirToZip(realpath("upload/$domain"), $zip); // 子域题目图片
    } else {
        if (is_dir("upload/"))
            addDirToZip(realpath("upload"), $zip); // 题目图片
    }
    chdir($OJ_DATA."/0");
    if (file_exists($db_file)) {
        $zip->addFile($db_file);
    }

    if (!$zip->close()) {
        echo "ZIP 文件生成失败，请检查目标目录权限: $ztar";
        exit;
    }
} else {
    echo "无法创建 ZIP 文件: $ztar";
    exit;
}

?>

<?php
}else{
?>
<br><br>
<form method="post" action="backup.php">
    <input type="submit" name="do" value="<?php echo $MSG_BACKUP_DATABASE ?>">
</form>

<?php
}
?>

<button onclick="phpfm(0)">查看备份文件</button>
<script src='../template/bs3/jquery.min.js'></script>
<script>
function phpfm(pid){
    $.post("phpfm.php",{'frame':3,'pid':pid,'pass':''},function(data,status){
        if(status=="success"){
            document.location.href="phpfm.php?frame=3&pid="+pid;
        }
    });
}
</script>
</div>
