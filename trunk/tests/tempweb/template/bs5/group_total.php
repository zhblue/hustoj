<?php $show_title=isset($view_title) ? "$view_title - $OJ_NAME" : "Group Total - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-bar-chart"></i> <?php echo isset($MSG_GROUP_NAME) ? $MSG_GROUP_NAME : 'Group'?> <?php echo isset($MSG_STATISTICS) ? $MSG_STATISTICS : 'Statistics'?></h4>
    </div>
    <div class="card-body">
        <?php if(isset($view_statistics)) echo $view_statistics; ?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
