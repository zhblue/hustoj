<?php
    $path_fix="";
    $dir=basename(getcwd());
    if( $OJ_CDN_URL=="" && ($dir=="discuss3"||$dir=="admin"||$dir=="include")) $path_fix="../";
    else $path_fix="";
?>
<!-- Bootstrap 5 CSS -->
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/bootstrap.min.css">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/bootstrap-icons.css">
<!-- KaTeX CSS -->
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/katex.min.css">
<!-- Custom template CSS -->
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/style.css">
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/tomorrow.css">
<?php if (file_exists(dirname(__FILE__)."/css/$OJ_CSS")){ ?>
<link href="<?php echo $path_fix."template/$OJ_TEMPLATE"?>/css/<?php echo $OJ_CSS?>" rel="stylesheet">
<?php } ?>
<?php if (file_exists(dirname(__FILE__)."/$OJ_CSS")){ ?>
<link href="<?php echo $path_fix."template/$OJ_TEMPLATE"?>/<?php echo $OJ_CSS?>" rel="stylesheet">
<?php } ?>
