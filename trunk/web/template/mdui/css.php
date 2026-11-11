<?php
if (__FILE__ === $_SERVER['SCRIPT_FILENAME']) {
    // 当前文件就是用户直接请求的入口脚本
    http_response_code(403);
    exit('Direct access is not allowed.');
}

    $dir=basename(getcwd());
    if($dir=="discuss3"||$dir=="admin") $path_fix="../";
    else $path_fix="";
?>

<!-- 新 Bootstrap 核心 CSS 文件 -->
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/bs3/"?>bootstrap.min.css">

<?php if(!isset($OJ_FLAT)||!$OJ_FLAT){?>
<!-- 可选的Bootstrap主题文件（一般不用引入） -->
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/bs3/"?>bootstrap-theme.min.css">
<?php }?>
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/bs3/$OJ_CSS"?>">
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/bs3/"?>mathjax.css">
