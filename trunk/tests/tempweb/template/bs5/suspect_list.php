<?php $show_title=isset($MSG_IP_VERIFICATION) ? "$MSG_IP_VERIFICATION - $OJ_NAME" : "IP Verification - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-shield-check"></i> <?php echo isset($MSG_IP_VERIFICATION) ? $MSG_IP_VERIFICATION : 'IP Verification'?></h4>
    </div>
    <div class="card-body">
        <?php if(isset($view_suspect)) echo $view_suspect; ?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
