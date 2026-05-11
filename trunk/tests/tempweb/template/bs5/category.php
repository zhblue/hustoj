<?php $show_title=isset($MSG_SOURCE) ? "$MSG_SOURCE - $OJ_NAME" : "Category - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-folder"></i> <?php echo isset($MSG_SOURCE) ? $MSG_SOURCE : 'Source'?></h4>
    </div>
    <div class="card-body">
        <?php echo isset($view_category) ? $view_category : '<p class="text-body-secondary">No categories available.</p>'; ?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
