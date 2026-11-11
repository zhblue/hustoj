<?php
if (__FILE__ === $_SERVER['SCRIPT_FILENAME']) {
    // 当前文件就是用户直接请求的入口脚本
    http_response_code(403);
    exit('Direct access is not allowed.');
}
?><?php $show_title="$MSG_FAQ - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<div class="padding">
    <h1 class="ui center aligned header"><?php echo $MSG_FAQ ?></h1>
    <div style="font-content">
             <?php echo $view_faqs?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
