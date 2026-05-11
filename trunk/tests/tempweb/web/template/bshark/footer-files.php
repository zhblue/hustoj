<!-- KaTeX核心js -->
<!--script src="https://cdn.bootcss.com/KaTeX/0.10.2/katex.min.js"></script-->
<!--script src="https://cdn.bootcss.com/KaTeX/0.10.2/contrib/auto-render.min.js"></script-->

<script>
        $("form").append("<div id='csrf' />");
        $("#csrf").load("<?php echo $path_fix ?>csrf.php");
</script>

<!-- WangEditor编辑器文件-->
<script src="./template/bshark/wangEditor/wangEditor.min.js"></script>

<?php if (isset($OJ_MARKDOWN) && $OJ_MARKDOWN) { ?>
        <script src="<?php echo $OJ_CDN_URL . $path_fix . "template/$OJ_TEMPLATE/" ?>marked.min.js"></script>
<?php } ?>

<!-- 主题核心js -->
<?php
$url = basename($_SERVER['REQUEST_URI']);
$realurl = basename($_SERVER['REQUEST_URI']);
$url = str_replace(strrchr($url, "?"), "", $url);
if ($url != 'loginpage.php' && $url != 'registerpage.php') { ?>
        <script src="./template/bshark/main.js"></script>
<?php } ?>

<!-- KaTeX AutoRender 开启后用$就能引用数学公式 -->
<!--script>
        renderMathInElement(document.body,
     {
                            delimiters: [
                                    {left: "$$", right: "$$", display: true},
                                    {left: "$", right: "$", display: false}
                            ]
                    }
    );
</script-->