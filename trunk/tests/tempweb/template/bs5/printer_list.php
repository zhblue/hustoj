<?php $show_title=isset($MSG_PRINTER) ? "$MSG_PRINTER - $OJ_NAME" : "Printer - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-printer"></i> <?php echo isset($MSG_PRINTER) ? $MSG_PRINTER : 'Printer'?></h4>
    </div>
    <div class="card-body">
        <?php if(isset($view_printer)) echo $view_printer; ?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
