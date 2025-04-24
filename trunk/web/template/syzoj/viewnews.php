<?php $show_title="$MSG_NEWS - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<script src="<?php echo "template/bs3/"?>marked.min.js"></script>
<div class="padding">
    <h1><?php echo $news_title ?></h1>
    <p style="margin-bottom: 5px; ">
        <b style="margin-right: 30px; "><i class="edit icon"></i><a class="black-link"
                href="userinfo.php?user=<?php echo $news_writer ?>"> <?php echo $news_writer ?></a></b>
        <b style="margin-right: 30px; "><i class="calendar icon"></i> <?php echo $news_date ?></b>
    </p>
    <div class="ui existing segment" style="overflow-y:overlay;">
        <div id="content" class="font-content"><?php echo bbcode_to_html($news_content)?></div>
    </div>
</div>
<script>
  $(document).ready(function(){
                marked.use({
                  // 开启异步渲染
                  async: true,
                  pedantic: false,
                  gfm: true,
                  mangle: false,
                  headerIds: false
                });
                $(".md").each(function(){
                        $(this).html(marked.parse($(this).text()));
                });
                // adding note for ```input1  ```output1 in description
                for(let i=1;i<10;i++){
                        $(".language-input"+i).parent().before("<div><?php echo $MSG_Input?>"+i+":</div>");
                        $(".language-output"+i).parent().before("<div><?php echo $MSG_Output?>"+i+":</div>");
                }
        $(".md table tr td").css({
            "border": "1px solid grey",
            "text-align": "center",
            "width": "200px",
            "height": "30px"
        });

        $(".md table th").css({
            "border": "1px solid grey",
            "width": "200px",
            "height": "30px",
            "background-color": "#9e9e9ea1",
            "text-align": "center"
        });
  });
</script>
<?php include("template/$OJ_TEMPLATE/footer.php");?>
