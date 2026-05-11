<?php $show_title=isset($title) ? "Contest RankList -- $title - $OJ_NAME" : "Contest Rank - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-bar-chart"></i> Contest RankList -- <?php echo isset($title) ? htmlentities($title, ENT_QUOTES, 'utf-8') : ''?></h4>
        <?php if(isset($cid)){ ?>
        <a href="contestrank.xls.php?cid=<?php echo intval($cid)?>" class="btn btn-sm btn-outline-primary">Download</a>
        <?php } ?>
    </div>
    <div class="card-body p-0">
        <div style="overflow-x:auto;">
            <?php if(isset($view_rank)) echo $view_rank; ?>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
