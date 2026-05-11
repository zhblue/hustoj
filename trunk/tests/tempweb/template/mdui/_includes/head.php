<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="author" content="Ren Baoshuo">
<meta name="generator" content="HUSTOJ">
<link rel="icon" href="/favicon.ico">

<!-- Title -->
<title><?php echo ($page_title?$page_title.' - ':'').$OJ_NAME; ?></title>

<!-- MDUI -->
<?php if($MDUI_OFFLINE) { ?>
    <link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"; ?>/assets/css/mdui.min.css">
    <script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"; ?>/assets/js/mdui.min.js"></script>
<?php } else { ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/mdui@1.0.1/dist/css/mdui.min.css">
    <script src="https://cdn.jsdelivr.net/npm/mdui@1.0.1/dist/js/mdui.min.js"></script>
<?php } ?>
<script>
var $ = mdui.$;
</script>
<style>
@media (min-width: 840px) {
    .mdui-container {
        padding-top: 80px;
    }
}

@media (min-width: 480px) {
    .mdui-container {
        padding-top: 60px;
    }
}

.mdui-container {
    padding-top: 40px;
    padding-bottom: 60px;
    position: relative;
}

.mdui-select-menu {
    min-height: 300px !important;
}
</style>

<!-- Fonts -->
<?php if(isset($MDUI_OFFLINE) && $MDUI_OFFLINE) { ?>
    <link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"; ?>/assets/css/fonts.css">
<?php } else { ?>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital@0;1&family=Open+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        as="style" onload="this.onload=null, this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto+Mono:ital@0;1&family=Open+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap"></noscript>
<?php } ?>

<!-- Styles -->
<style>
    code {
        font-family: 'Roboto Mono', 'Consolas', monospace !important;
    }
    body {
        font-family: 'Open Sans', 'Roboto', 'Noto', 'Helvetica', 'Arial', sans-serif !important;
    }
</style>

<!-- Scripts -->
<script>
    console.log('\n %c HUSTOJ %c https://github.com/zhblue/hustoj %c\n', 'color: #fadfa3; background: #000000; padding:5px 0;', 'background: #fadfa3; padding:5px 0;', '');
    console.log('\n %c Theme Author %c Baoshuo (@renbaoshuo) %c https://baoshuo.ren %c\n', 'color: #fadfa3; background: #000000; padding:5px 0;', 'background: #fadfa3; padding:5px 0;', 'background: #ffbf33; padding:5px 0;', '');
    console.log('\n %c MDUI v1.0.1 %c https://mdui.org %c\n', 'color: #ffffff; background: #030307; padding:5px 0;', 'background: #673ab7; padding:5px 0;', '');
    console.log('\n GitHub Homepage: https://github.com/zhblue/hustoj \n Document: https://zhblue.github.io/hustoj \n Bug report URL: https://github.com/zhblue/hustoj/issues \n \n%c ★ Please give us a star on GitHub! ★ %c \n', 'color: red;', '')
</script>

<?php 
if(stripos($_SERVER['REQUEST_URI'],"template")!==false) exit();

$url = basename($_SERVER['REQUEST_URI']);
$dir = basename(getcwd());

if($dir == "discuss3") $path_fix="../";
else $path_fix="";

if(isset($OJ_NEED_LOGIN) && $OJ_NEED_LOGIN
        && ($url!='loginpage.php' && $url!='lostpassword.php'
            && $url!='lostpassword2.php' && $url!='registerpage.php')
        && !isset($_SESSION[$OJ_NAME.'_'.'user_id'])) {
    header("location:".$path_fix."loginpage.php");
    exit();
}

$_SESSION[$OJ_NAME.'_'.'profile_csrf'] = rand();

if($OJ_ONLINE){
    require_once($path_fix.'include/online.php');
    $on = new online();
}
?>

<!-- KaTeX -->
<script> var katex_options = { delimiters: [ {left: '$$', right: '$$', display: true}, {left: '$', right: '$', display: false}, {left: '\\(', right: '\\)', display: false}, {left: '\\[', right: '\\]', display: true} ] }; </script>
<?php if(isset($MDUI_OFFLINE) && $MDUI_OFFLINE) { ?>
    <link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"; ?>/assets/css/katex.min.css"
        as="style" onload="this.onload=null, this.rel='stylesheet'">
    <script defer src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"; ?>/assets/js/katex.min.js"></script>
    <script defer src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"; ?>/assets/js/auto-render.min.js"
        onload="renderMathInElement(document.body, katex_options);"></script>
<?php } else { ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.12.0/dist/katex.min.css"
        as="style" onload="this.onload=null, this.rel='stylesheet'">
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.12.0/dist/katex.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/katex@0.12.0/dist/contrib/auto-render.min.js"
        onload="renderMathInElement(document.body, katex_options);"></script>
<?php } ?>

<!-- Highlight.js -->
<?php if(isset($MDUI_OFFLINE) && $MDUI_OFFLINE) { ?>
    <link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"; ?>/assets/css/atom-one-light.min.css">
    <script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"; ?>/assets/js/highlight.pack.js"
        onload="hljs.initHighlightingOnLoad();"></script>
<?php } else { ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@highlightjs/cdn-assets@10.5.0/styles/atom-one-light.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@highlightjs/cdn-assets@10.5.0/highlight.min.js"
        onload="hljs.initHighlightingOnLoad();"></script>
    <!-- Pascal --><script src="https://cdn.jsdelivr.net/npm/@highlightjs/cdn-assets@10.5.0/languages/delphi.min.js"></script>
<?php } ?>
