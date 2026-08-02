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
                        let textToCopy = typeof cates === 'object' ? JSON.stringify(cates) : cates;

                        // 创建一个临时的 textarea 元素
                        let $temp = $('<textarea>').val(textToCopy).appendTo('body').select();
                        try {
                            let successful = document.execCommand('copy');
                            if (successful) {
                                console.log("复制成功！找一个AI去粘贴，将生成的html存为category.html");
                            } else {
                                console.error("复制失败");
                            }
                        } catch (err) {
                            console.error("浏览器不支持或复制出错: ", err);
                        }
                        $temp.remove();
                });

        });
</script>
<?php include("template/$OJ_TEMPLATE/footer.php");?>
