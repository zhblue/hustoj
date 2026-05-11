<?php 
require_once("./template/bshark/theme.conf.php");?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0" />

<!-- KaTeX核心css -->
<!--link href="https://cdn.bootcss.com/KaTeX/0.10.2/katex.min.css" rel="stylesheet"-->

<!-- iconfont图标css -->
<link rel="stylesheet" href="./template/bshark/iconfont.css">
<script src="<?php echo $OJ_CDN_URL?>template/bshark/iconfont.js"></script>

<!-- MathJax -->
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL."template/$OJ_TEMPLATE/"?>katex.min.css">
<link rel="stylesheet" href="<?php echo $OJ_CDN_URL."template/$OJ_TEMPLATE/"?>mathjax.css">

<!-- Highlightjs 核心文件 -->
<link href="<?php echo $OJ_CDN_URL?>template/bshark/highlight/styles/tomorrow.css" rel="stylesheet">
<script src="<?php echo $OJ_CDN_URL?>template/bshark/highlight/highlight.pack.js"></script>

<script>
hljs.initHighlightingOnLoad();
</script>

<!-- ChartJs 核心文件 -->
<script src="<?php echo $OJ_CDN_URL?>template/bshark/chartjs/Chart.bundle.js"></script>

<!-- SweetAlert 核心文件 -->
<link href="<?php echo $OJ_CDN_URL?>template/bshark/statics/semantic.min.css" rel="stylesheet">
<script src="<?php echo $OJ_CDN_URL?>template/bshark/statics/jquery.min.js"></script>
<script src="<?php echo $OJ_CDN_URL?>template/bshark/statics/semantic.min.js"></script>
<link href="<?php echo $OJ_CDN_URL?>template/bshark/statics/sweetalert2.min.css" rel="stylesheet">
<script src="<?php echo $OJ_CDN_URL?>template/bshark/statics/sweetalert2.min.js"></script>
<script>
    const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})
</script>

<!-- 主题核心css文件（light and dark） -->
<link href="<?php echo $OJ_CDN_URL?>template/bshark/main-<?php echo $THEME_MOD;?>.css?ver=23.1.7" rel="stylesheet">
