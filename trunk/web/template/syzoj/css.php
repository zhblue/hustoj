<?php
        $dir=basename(getcwd());
        if( $OJ_CDN_URL=="" && ($dir=="discuss3"||$dir=="admin"||$dir=="include")) $path_fix="../";
        else $path_fix="";
?>
<!-- 苹果液体玻璃效果所需SVG滤镜 -->
<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <filter id="customLensFilter" x="0%" y="0%" width="100%" height="100%" filterUnits="objectBoundingBox">
    <feComponentTransfer in="SourceAlpha" result="alpha">
      <feFuncA type="identity" />
    </feComponentTransfer>
    <feGaussianBlur in="alpha" stdDeviation="50" result="blur" />
    <feDisplacementMap in="SourceGraphic" in2="blur" scale="50" xChannelSelector="A" yChannelSelector="A" />
  </filter>
</svg>
<!-- 玻璃效果样式 - 整合到.padding类中 -->

<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/style.css">
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/tomorrow.css">
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/semantic.min.css?v=0.1">
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/katex.min.css">
<link href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/morris.min.css" rel="stylesheet">
<link href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/FiraMono.css" rel="stylesheet">
<link href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/latin.css" rel="stylesheet">
<link href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/css/Exo.css?v=0.1" rel="stylesheet">

<?php if (file_exists(dirname(__FILE__)."/css/$OJ_CSS")){ ?>
<link href="<?php echo $path_fix."template/$OJ_TEMPLATE"?>/css/<?php echo $OJ_CSS?>?v=0.1" rel="stylesheet">
<?php } ?>

<?php if (file_exists(dirname(__FILE__)."/$OJ_CSS")){ ?>
<link href="<?php echo $path_fix."template/$OJ_TEMPLATE"?>/<?php echo $OJ_CSS?>?v=0.1" rel="stylesheet">
<?php } ?>

