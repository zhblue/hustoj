<?php
        $dir=basename(getcwd());
        if($dir=="discuss3"||$dir=="admin"||$dir=="include") $path_fix="../";
        else $path_fix="";
?>
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/style.css">
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/tomorrow.css">
<link rel="stylesheet" href="<?php echo $path_fix."template/$OJ_TEMPLATE"?>/css/semantic.min.css?v=0.1">
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/katex.min.css">
<link href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/morris.min.css" rel="stylesheet">
<link href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/FiraMono.css" rel="stylesheet">
<link href="<?php echo $path_fix."template/$OJ_TEMPLATE"?>/css/latin.css" rel="stylesheet">
<link href="<?php echo $path_fix."template/$OJ_TEMPLATE"?>/css/Exo.css?v=0.1" rel="stylesheet">
