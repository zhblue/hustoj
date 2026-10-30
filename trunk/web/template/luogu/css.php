<?php
        $dir = basename(getcwd());
        if ($OJ_CDN_URL == "" && ($dir == "discuss3" || $dir == "admin" || $dir == "include")) $path_fix = "../";
        else $path_fix = "";
        $lg_base = $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE";
?>
<link rel="stylesheet" href="<?php echo $lg_base ?>/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo $lg_base ?>/css/semantic.min.css?v=0.1">
<link rel="stylesheet" href="<?php echo $lg_base ?>/css/katex.min.css">
<link rel="stylesheet" href="<?php echo $lg_base ?>/css/tomorrow.css">
<link rel="stylesheet" href="<?php echo $lg_base ?>/css/FiraMono.css">
<link rel="stylesheet" href="<?php echo $lg_base ?>/css/latin.css">
<link rel="stylesheet" href="<?php echo $lg_base ?>/css/slide.css">
<link rel="stylesheet" href="<?php echo $lg_base ?>/css/luogu.css?v=1.0">

<?php if (file_exists(dirname(__FILE__)."/css/$OJ_CSS")) { ?>
<link href="<?php echo $path_fix."template/$OJ_TEMPLATE"?>/css/<?php echo $OJ_CSS?>?v=0.11" rel="stylesheet">
<?php } ?>

<?php if (file_exists(dirname(__FILE__)."/$OJ_CSS")) { ?>
<link href="<?php echo $path_fix."template/$OJ_TEMPLATE"?>/<?php echo $OJ_CSS?>?v=0.1" rel="stylesheet">
<?php } ?>
