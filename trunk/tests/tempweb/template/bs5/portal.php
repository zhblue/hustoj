<?php $show_title=isset($MSG_TODO) ? "$MSG_TODO - $OJ_NAME" : "Portal - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-card-checklist"></i> <?php echo isset($MSG_TODO) ? $MSG_TODO : 'TODO'?></h4>
    </div>
    <div class="card-body">
        <?php if(isset($view_problemset)) echo $view_problemset; ?>
        <?php if(isset($view_contest)) echo $view_contest; ?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
