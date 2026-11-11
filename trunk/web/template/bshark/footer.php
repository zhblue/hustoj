<?php
if (__FILE__ === $_SERVER['SCRIPT_FILENAME']) {
    // 当前文件就是用户直接请求的入口脚本
    http_response_code(403);
    exit('Direct access is not allowed.');
}
?>
<div class="ui center aligned container" style="margin-top: 50px;">
    <p>GPLv2 licensed by <a href='https://github.com/zhblue/hustoj'>HUSTOJ</a>
        <?php echo date('Y'); ?><br>
        THEME bshark by <a href="https://github.com/yemaster">yemaster</a>.<br>

        <?php if (isset($OJ_BEIAN) && $OJ_BEIAN) { ?>
            <a href='http://beian.miit.gov.cn/' target='_blank'>
                <?php echo $OJ_BEIAN ?>
            </a>
        <?php } ?>
    </p>
    <br>
</div>