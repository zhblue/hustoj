<?php $show_title="$MSG_ERROR - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header bg-danger text-white">
        <h4 class="mb-0"><i class="bi bi-exclamation-triangle"></i> <?php echo $MSG_ERROR?></h4>
    </div>
    <div class="card-body">
        <div class="alert alert-danger">
            <p><?php echo isset($view_errors) ? $view_errors : $error?></p>
        </div>
        <div class="text-center">
            <a class="btn btn-primary" href="javascript:history.back()"><i class="bi bi-arrow-left"></i> <?php echo $MSG_BACK?></a>
            <a class="btn btn-outline-primary" href="/"><i class="bi bi-house"></i> <?php echo $MSG_HOME?></a>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
