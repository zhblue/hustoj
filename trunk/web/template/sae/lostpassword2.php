<html>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title><?php echo $view_title?></title>
        <link rel=stylesheet href='./template/<?php echo $OJ_TEMPLATE?>/<?php echo isset($OJ_CSS)?$OJ_CSS:"hoj.css" ?>' type='text/css'>
</head>
<body>
<div id="wrapper">
        <?php require_once("oj-header.php");?>
        <div class="container">
                <div class="row">
                        <div class="span12">
                                <div class="hero-unit">
                                        <p>
                                                <span>您的密码被重置为:</span>
                                                <?php print_r($_SESSION['lost_key']); ?>
                                                <div>推荐您登录以后即刻修改密码以防忘记此随机密码</div>
                                        </p>
                                </div>
                        </div>
                </div>
        </div>

        <div id=foot>
                <?php require_once("oj-footer.php");?>

        </div><!--end foot-->
</div><!--end wrapper-->
</body>
</html>

