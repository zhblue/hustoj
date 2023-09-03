<?php $show_title="$MSG_PROBLEMS - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<?php
$upload_dir = '/home/judge/src/web/upload/';
$error_msg = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileToUpload'])) {
    $target_file = $upload_dir . basename($_FILES["fileToUpload"]["name"]);

    // 文件上传逻辑
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $success_msg = "文件 " . basename($_FILES["fileToUpload"]["name"]) . " 已成功上传!";
    } else {
        $error_msg = "上传文件出错！";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文件上传</title>
    <p><b>注意：</b>
    <br>1.不要把这个当云盘，不要传太大文件。
    <br>2.如果传一个已经有的同名文件，会直接覆盖以前的文件，所以这一点上请注意文件名的命名
    </p>
    
</head>
<body>

<h2>文件上传</h2>

<form action="upload.php" method="post" enctype="multipart/form-data">
    选择要上传的文件：
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="上传文件" name="submit">
</form>

<?php if($error_msg): ?>
    <div style="color: red;"><?php echo $error_msg; ?></div>
<?php endif; ?>

<?php if($success_msg): ?>
    <div style="color: green;"><?php echo $success_msg; ?></div>
<?php endif; ?>


<table border="1">
    <thead>
        <tr>
            <th>文件名</th>
            <th>文件大小</th>
            <th>上传时间</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        foreach($fileDetails as $file) {
            $formattedTime = date('Y-m-d H:i:s', $file['time']);  // 格式化时间
            $formattedSize = round($file['size'] / 1024, 2) . ' KB'; // 将文件大小转换为KB并格式化
            echo "<tr>
                    <td><a href='/upload/{$file['name']}' target='_blank'>{$file['name']}</a></td>
                    <td>$formattedSize</td>
                    <td>$formattedTime</td>
                  </tr>";
        }
        ?>
    </tbody>
</table>
</body>
</html>
