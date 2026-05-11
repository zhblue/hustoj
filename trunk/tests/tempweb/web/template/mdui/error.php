<!DOCTYPE html>
<html lang="cn">

<head>
    <?php $page_title = "错误"; ?>
    <?php include('_includes/head.php'); ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <div class="jumbotron">
            <?php echo $view_errors; ?>
        </div>
    </div>
</body>

</html>