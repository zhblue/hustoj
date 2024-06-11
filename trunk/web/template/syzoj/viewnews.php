<?php $show_title="$MSG_NEWS - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
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

<?php include("template/$OJ_TEMPLATE/footer.php");?>
