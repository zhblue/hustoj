<?php $show_title="$MSG_SOURCE - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0">
            <i class="bi bi-file-code"></i> <?php echo $MSG_SOURCE?> -
            <a href="userinfo.php?user=<?php echo htmlentities($author,ENT_QUOTES,'utf-8')?>"><?php echo htmlentities($author,ENT_QUOTES,'utf-8')?></a>
        </h4>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <span class="badge bg-info"><?php echo $MSG_PROBLEM?>: <?php echo isset($problem_id)?$problem_id:$pid?></span>
            <span class="badge bg-secondary"><?php echo $MSG_LANG?>: <?php echo $language_name[$language]?></span>
            <span class="badge bg-success"><?php echo $MSG_AC?>: <?php echo $result==4?'Yes':'No'?></span>
        </div>
        <pre class="bg-dark text-white p-3" style="overflow-x:auto;font-size:14px;"><code><?php echo htmlentities($view_source,ENT_QUOTES,'utf-8')?></code></pre>
    </div>
    <div class="card-footer text-center">
        <a class="btn btn-outline-primary" href="javascript:history.back()"><i class="bi bi-arrow-left"></i> <?php echo $MSG_BACK?></a>
        <a class="btn btn-outline-secondary" href="comparesource.php?sid=<?php echo $sid?>"><i class="bi bi-arrow-left-right"></i> <?php echo $MSG_COMPARE?></a>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
