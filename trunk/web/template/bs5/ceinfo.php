<?php $show_title="$MSG_COMPILATION_ERROR - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header bg-danger text-white">
        <h4 class="mb-0"><i class="bi bi-exclamation-octagon"></i> <?php echo $MSG_COMPILATION_ERROR?></h4>
    </div>
    <div class="card-body">
        <pre class="bg-dark text-white p-3" style="overflow-x:auto;"><?php echo htmlentities($view_ceinfo,ENT_QUOTES,'utf-8')?></pre>
    </div>
    <div class="card-footer text-center">
        <a class="btn btn-primary" href="javascript:history.back()"><i class="bi bi-arrow-left"></i> <?php echo $MSG_BACK?></a>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
