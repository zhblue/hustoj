<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Error - <?php echo $OJ_NAME; ?></title>
    <?php require("./template/bshark/header-files.php"); ?>
</head>

<body>
    <?php require("./template/bshark/nav.php"); ?>
    <div class="ui container bsharkMain">
        <div class="card">
            <div class="card-body">
                <h2>访问错误！</h2>
                <?php
                function GetCurUrl()
                {
                    return 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
                }
                ?>
                <p>请求地址:<?php echo GetCurUrl(); ?><br>错误信息:<?php echo $view_errors; ?></p>
                <hr />
                <div class="btn-group">
                    <button class="ui button blue" onclick="location.href='<?php echo $OJ_HOME; ?>'">主页</button>
                    <button class="ui basic button blue" onclick="history.back(-1)">返回上一页</button>
                </div>
            </div>
        </div>
    </div>
    <?php require("./template/bshark/footer.php"); ?>
    <?php require("./template/bshark/footer-files.php"); ?>
</body>

</html>