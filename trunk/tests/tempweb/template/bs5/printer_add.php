<?php $show_title=isset($MSG_PRINTER) ? "$MSG_PRINTER - $OJ_NAME" : "Printer - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-printer"></i> <?php echo isset($MSG_PRINTER) ? $MSG_PRINTER : 'Printer'?></h4>
    </div>
    <div class="card-body">
        <form id="frmSolution" action="printer.php" method="post">
            <div class="mb-3">
                <textarea name="content" id="source" class="form-control font-monospace" rows="20" placeholder="Paste your code here..."></textarea>
            </div>
            <div class="text-center">
                <input type="submit" class="btn btn-primary" value="<?php echo isset($MSG_PRINTER) ? $MSG_PRINTER : 'Print'?>">
            </div>
        </form>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
