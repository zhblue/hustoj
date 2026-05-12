<?php $show_title="$MSG_RANKLIST - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4><i class="bi bi-bar-chart"></i> <?php echo $MSG_RANKLIST?></h4>
    </div>
    <div class="card-body">
        <form action="ranklist.php" method="get" class="row g-3 mb-3">
            <div class="col-auto">
                <input class="form-control" type="text" name="user_id" placeholder="<?php echo $MSG_USER_ID?>" value="<?php echo isset($_GET['user_id'])?htmlentities($_GET['user_id'],ENT_QUOTES,'utf-8'):''?>">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> <?php echo $MSG_SEARCH?></button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">#</th>
                        <th><?php echo $MSG_USER?></th>
                        <th><?php echo $MSG_NICK?></th>
                        <th class="text-center"><?php echo $MSG_SOVLED?></th>
                        <th class="text-center"><?php echo $MSG_SUBMIT?></th>
                        <th class="text-center"><?php echo $MSG_RATIO?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank=0;
                    foreach($view_rank as $row){
                        $rank++;
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $rank?></td>
                        <td><a href="userinfo.php?user=<?php echo htmlentities($row['user_id'],ENT_QUOTES,'utf-8')?>"><?php echo htmlentities($row['user_id'],ENT_QUOTES,'utf-8')?></a></td>
                        <td><?php echo htmlentities($row['nick'],ENT_QUOTES,'utf-8')?></td>
                        <td class="text-center text-success fw-bold"><?php echo $row['solved']?></td>
                        <td class="text-center"><?php echo $row['submit']?></td>
                        <td class="text-center"><?php echo $row['submit']>0 ? round($row['solved']*100/$row['submit'],1).'%' : '0%'?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if(isset($pages) && $pages>1){ ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item"><a class="page-link" href="ranklist.php?page=1">&laquo;&laquo;</a></li>
                <li class="page-item"><a class="page-link" href="ranklist.php?page=<?php echo $page==1?1:$page-1?>">&laquo;</a></li>
                <?php for($i=$spage; $i<=$epage; $i++){ ?>
                <li class="page-item <?php if($page==$i) echo 'active'; ?>"><a class="page-link" href="ranklist.php?page=<?php echo $i?>"><?php echo $i?></a></li>
                <?php } ?>
                <li class="page-item"><a class="page-link" href="ranklist.php?page=<?php echo $page==$pages?$pages:$page+1?>">&raquo;</a></li>
                <li class="page-item"><a class="page-link" href="ranklist.php?page=<?php echo $pages?>">&raquo;&raquo;</a></li>
            </ul>
        </nav>
        <?php } ?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
