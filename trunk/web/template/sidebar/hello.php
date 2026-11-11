<?php
if (__FILE__ === $_SERVER['SCRIPT_FILENAME']) {
    // 当前文件就是用户直接请求的入口脚本
    http_response_code(403);
    exit('Direct access is not allowed.');
}
  include(dirname(__FILE__)."/header.php");
?>
  <div class="ui <?php echo $ui_class?> icon message">
  <div class="content">
    <div class="header" style="margin-bottom: 10px; " >
      <?php echo $view_hello;?>
    </div>
	当前系统正确提交数量<?php echo $view_count_ac ?>
    <p>
      <a href="javascript:history.go(-1)"><?php echo $MSG_BACK;?></a>
    </p>
  </div>
</div>

<?php include(dirname(__FILE__)."/footer.php");?>
