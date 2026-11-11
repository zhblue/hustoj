<?php
if (__FILE__ === $_SERVER['SCRIPT_FILENAME']) {
    // 当前文件就是用户直接请求的入口脚本
    http_response_code(403);
    exit('Direct access is not allowed.');
}
?>
<!DOCTYPE html>
<html lang="cn">

<head>
    <?php include('_includes/head.php'); ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <h1>分类</h1>
         <div class="ui existing segment">
        <?php echo $view_category?>
        </div>
    </div>
</body>

</html>
