<?php $show_title=isset($news_title) ? "$news_title - $OJ_NAME" : "News - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-newspaper"></i> <?php echo isset($news_title) ? htmlentities($news_title, ENT_QUOTES, 'utf-8') : ''?></h4>
        <small class="text-body-secondary"><?php echo isset($news_date) ? $news_date : ''?></small>
    </div>
    <div class="card-body">
        <?php if(isset($news_content)){ ?>
        <?php echo bbcode_to_html($news_content)?>
        <?php } else { ?>
        <div class="alert alert-danger">公告不存在！</div>
        <?php } ?>
    </div>
</div>

<div class="text-center">
    <a class="btn btn-outline-primary" href="index.php">
        <i class="bi bi-house"></i> <?php echo isset($MSG_HOME) ? $MSG_HOME : 'Home'?>
    </a>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
