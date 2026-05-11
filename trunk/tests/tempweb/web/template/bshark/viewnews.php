<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        <?php echo $MSG_NEWS; ?> - <?php echo $OJ_NAME; ?>
    </title>
    <?php require("./template/bshark/header-files.php"); ?>

</head>

<body>
    <?php require("./template/bshark/nav.php"); ?>
    <div class="ui container bsharkMain">
        <div class="ui stackable grid">
            <div class="eleven wide column">
                <div class="card">
                    <div class="card-body">
                        <h2>
                            <?php echo $MSG_NEWS; ?> - <?php echo $news_title; ?>
                        </h2>
                        <div class="ui divider"></div>
                        <?php echo bbcode_to_html($news_content); ?>
                    </div>
                </div>
            </div>
            <div class="five wide column">
                <div class="card">
                    <div class="card-body">
                        <h4>
                            <?php echo $MSG_NEWS; ?>
                        </h4>
                        <p><?php echo $MSG_USER; ?>：<?php echo $news_writer; ?><br>
                            <?php echo $MSG_SUBMIT_TIME; ?>：<?php echo $news_date; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require("./template/bshark/footer.php"); ?>
    <?php require("./template/bshark/footer-files.php"); ?>
</body>

</html>