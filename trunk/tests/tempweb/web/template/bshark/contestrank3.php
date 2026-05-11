<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        <?php echo $MSG_RANKLIST; ?> - <?php echo $OJ_NAME; ?>
    </title>
    <?php require("./template/bshark/header-files.php"); ?>

    <link rel="stylesheet" type="text/css" href="template/<?php echo $OJ_TEMPLATE ?>/scrollboard.css">
</head>

<body>
    <?php require("./template/bshark/nav.php"); ?>

    <?php require("./template/bshark/footer.php"); ?>
    <?php require("./template/bshark/footer-files.php"); ?>
    <script type="text/javascript" src="include/jquery.tablesorter.js?v=0.1"></script>
    <script type="text/javascript" src="template/<?php echo $OJ_TEMPLATE ?>/scrollboard.js?v=0.09"></script>
    <script type="text/javascript">

        var board = new Board(<?php echo $problem_num ?>, new Array(<?php echo $gold_num ?>, <?php echo $silver_num ?>, <?php echo $bronze_num ?>), StringToDate("<?php echo $start_time_str ?>"), StringToDate("<?php echo $lock_time_str ?>"),<?php echo $cid ?>);

        board.showInitBoard();
        $('html').click(function (e) {
            board.keydown();
        });
        $('html').keydown(function (e) {
            if (e.keyCode == 13)
                board.keydown();
        });
    </script>
</body>

</html>