<?php $show_title=isset($MSG_SET_LOGIN_IP) ? "$MSG_SET_LOGIN_IP - $OJ_NAME" : "Set Login IP - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-globe"></i> <?php echo isset($MSG_SET_LOGIN_IP) ? $MSG_SET_LOGIN_IP : 'Set Login IP'?></h4>
    </div>
    <div class="card-body">
        <?php if(isset($view_ip)) echo $view_ip; ?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
