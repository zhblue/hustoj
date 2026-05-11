<?php $show_title=isset($MSG_FAQ) ? "$MSG_FAQ - $OJ_NAME" : "FAQ - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-question-circle"></i> <?php echo isset($MSG_FAQ) ? $MSG_FAQ : 'FAQ'?></h4>
    </div>
    <div class="card-body">
        <?php if(isset($view_faqs) && $view_faqs){ ?>
        <?php echo $view_faqs; ?>
        <?php } else { ?>
        <div class="alert alert-info">No FAQ content available.</div>
        <?php } ?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
