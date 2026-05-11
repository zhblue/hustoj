<?php
// ceinfo backend sets: $view_reinfo, $view_title, $id
// Language variables used: $MSG_SOURCE_CODE, $MSG_COMPILE_INFO, $MSG_ERROR_EXPLAIN
$msg_compile = isset($MSG_COMPILE_INFO) ? $MSG_COMPILE_INFO : 'Compilation Info';
$msg_source = isset($MSG_SOURCE_CODE) ? $MSG_SOURCE_CODE : 'Source Code';
$msg_explain = isset($MSG_ERROR_EXPLAIN) ? $MSG_ERROR_EXPLAIN : 'Explanation';
?>
<?php $show_title=isset($id) ? "$id - $msg_compile - $OJ_NAME" : "CE Info - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header bg-danger text-white">
        <h4 class="mb-0"><i class="bi bi-exclamation-octagon"></i> <?php echo $msg_compile?></h4>
    </div>
    <div class="card-body">
        <?php if(isset($view_reinfo) && $view_reinfo){ ?>
        <pre class="bg-dark text-light p-3 rounded" style="overflow-x:auto;white-space:pre-wrap;word-break:break-all;font-size:13px;"><?php echo $view_reinfo?></pre>
        <?php } else { ?>
        <div class="alert alert-danger">No compilation error information available.</div>
        <?php } ?>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-lightbulb"></i> <?php echo $msg_explain?></h4>
    </div>
    <div class="card-body">
        <div id="errexp" class="text-body-secondary">Loading AI explanation...</div>
    </div>
</div>

<div class="text-center mb-3">
    <a class="btn btn-outline-primary" href="javascript:history.back()">
        <i class="bi bi-arrow-left"></i> <?php echo isset($MSG_BACK) ? $MSG_BACK : 'Back'?>
    </a>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
