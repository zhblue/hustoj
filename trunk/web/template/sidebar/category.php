<?php
if (__FILE__ === $_SERVER['SCRIPT_FILENAME']) {
    // 当前文件就是用户直接请求的入口脚本
    http_response_code(403);
    exit('Direct access is not allowed.');
}
$show_title="$MSG_SOURCE - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<div class="padding">
    <div style="margin-top: 0px; margin-bottom: 14px; padding-bottom: 0px; " >
        <p class="transition visible">
           <h1 style="margin-left: 10px; display: inline-block; "><?php echo $MSG_SOURCE?></h1>
        </p>
        <div class="ui existing segment">
        <?php echo $view_category?>
        </div>
    </div>
<?php include("template/$OJ_TEMPLATE/footer.php");?>
