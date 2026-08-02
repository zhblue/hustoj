<?php $show_title="$MSG_SOURCE - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<div class="padding">
    <div style="margin-top: 0px; margin-bottom: 14px; padding-bottom: 0px; " >
        <p class="transition visible">
           <h1 style="margin-left: 10px; display: inline-block; "><?php echo $MSG_SOURCE?></h1>
        </p>
        <div class="ui existing segment">
        <?php echo file_get_contents("category.html"); ?>
        </div>
        <div id='cates' class="ui existing segment">
        <?php echo $view_category?>
        </div>
    </div>
<script>
        $(document).ready(function(){
                $('#cates').dblclick(function(){
                        let cates="系统中有如下的分类，帮我找到它们的相互联系（难度递进或者大类包含小类），用一个html5+js脚本展现，可以用词云或树状的形式显示，每个分类标签用超链接指向 形如 /problemset.php?search=关键词 的URL，点击时弹出新窗口。可以使用/template/syzoj/js/echarts.min.js\n "+$(this).text();
                        console.log(cates);
                        console.log("复制上面的提示词,找AI给你编写category.html");
                });
        });
</script>
<?php include("template/$OJ_TEMPLATE/footer.php");?>

