<?php $show_title = "$MSG_BBS - $OJ_NAME"; ?>
<?php

$view_discuss = ob_get_contents();
ob_end_clean();
require_once(dirname(__FILE__) . "/../../lang/$OJ_LANG.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        <?php echo $MSG_BBS; ?> - <?php echo $OJ_NAME; ?>
    </title>

    <?php require("./template/bshark/header-files.php"); ?>
</head>

<body>
    <?php include("template/bshark/nav.php"); ?>
    <div class="ui container bsharkMain">
        <!-- Main component for a primary marketing message or call to action -->
        <div class="card">
            <div class="card-body">
                <?php include("include/bbcode.php"); ?>
                <h2>
                    <?php echo $news_title ?>
                </h2>
                <div class="ui existing segment">
                    <?php echo $view_discuss ?>
                </div>
            </div>

        </div>

    </div>

    <?php require("./template/bshark/footer.php"); ?>
    <?php require("./template/bshark/footer-files.php"); ?>
</body>

</html>