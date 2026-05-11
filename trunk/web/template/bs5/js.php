<?php
    $path_fix="";
    $dir=basename(getcwd());
    if( $OJ_CDN_URL=="" && ($dir=="discuss3"||$dir=="admin"||$dir=="include")) $path_fix="../";
    else $path_fix="";
?>
<!-- jQuery (required by Bootstrap 5 JS) -->
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/js/jquery.min.js"></script>
<!-- Bootstrap 5 JS Bundle (includes Popper) -->
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/js/bootstrap.min.js"></script>
<!-- marked.js (Markdown parser) -->
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/js/marked.min.js"></script>
<!-- KaTeX (math rendering) -->
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/js/katex.min.js"></script>
<!-- MathJax (math rendering fallback) -->
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/js/tex-chtml.js"></script>
