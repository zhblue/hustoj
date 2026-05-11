<?php $show_title="$MSG_SOURCE - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-folder"></i> <?php echo $MSG_SOURCE?></h4>
    </div>
    <div class="card-body">
        <div class="row">
            <?php
            foreach($view_category as $row){
            ?>
            <div class="col-md-4 col-lg-3 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">
                            <a href="problemset.php?search=<?php echo urlencode($row['name'])?>">
                                <span class="badge bg-<?php echo isset($color_theme[$row['id']%count($color_theme)]) ? $color_theme[$row['id']%count($color_theme)] : 'primary'?>">
                                    <?php echo htmlentities($row['name'],ENT_QUOTES,'utf-8')?>
                                </span>
                            </a>
                        </h5>
                        <p class="card-text text-muted mb-0"><?php echo $row['cnt']?> <?php echo $MSG_PROBLEMS?></p>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
