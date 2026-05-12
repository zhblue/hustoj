<?php $show_title="$MSG_NEWS - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-newspaper"></i> <?php echo htmlentities($row['title'], ENT_QUOTES, 'utf-8')?></h4>
        <small class="text-muted"><?php echo htmlentities($row['time'], ENT_QUOTES, 'utf-8')?></small>
    </div>
    <div class="card-body">
        <?php echo bbcode_to_html($row['content'])?>
    </div>
</div>

<div class="text-center">
    <a class="btn btn-outline-primary" href="index.php"><i class="bi bi-house"></i> <?php echo $MSG_HOME?></a>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
