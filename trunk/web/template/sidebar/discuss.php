<?php $show_title="$MSG_BBS - $OJ_NAME"; ?>
<?php 

   $view_discuss=ob_get_contents();
    ob_end_clean();
   require_once(dirname(__FILE__)."/../../lang/$OJ_LANG.php");
?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<?php include("include/bbcode.php");?>
<div class="padding">
    <h1><?php echo $news_title ?></h1>
    <div class="ui existing segment">
	<?php echo $view_discuss?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
