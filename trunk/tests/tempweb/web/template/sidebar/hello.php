<?php
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
