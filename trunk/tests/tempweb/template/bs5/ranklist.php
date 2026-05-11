<?php $show_title=isset($MSG_RANKLIST) ? "$MSG_RANKLIST - $OJ_NAME" : "Ranklist - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h4 class="mb-0"><i class="bi bi-bar-chart me-2"></i><?php echo isset($MSG_RANKLIST) ? $MSG_RANKLIST : 'Ranklist'?></h4>
        <form action="ranklist.php" method="get" class="d-flex">
            <input class="form-control form-control-sm me-2" type="text" name="user_id" placeholder="<?php echo isset($MSG_USER_ID) ? $MSG_USER_ID : 'User ID'?>" value="<?php echo isset($_GET['user_id'])?htmlentities($_GET['user_id'],ENT_QUOTES,'utf-8'):''?>">
            <button class="btn btn-sm btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="card-body p-0">
        <?php if(isset($view_rank) && is_array($view_rank) && count($view_rank) > 0){ ?>
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="width:50px">#</th>
                        <th><?php echo isset($MSG_USER) ? $MSG_USER : 'User'?></th>
                        <th class="d-none d-md-table-cell"><?php echo isset($MSG_NICK) ? $MSG_NICK : 'Nick'?></th>
                        <th class="text-center"><?php echo isset($MSG_SOVLED) ? $MSG_SOVLED : 'Solved'?></th>
                        <th class="text-center"><?php echo isset($MSG_SUBMIT) ? $MSG_SUBMIT : 'Submit'?></th>
                        <th class="text-center">
                            <?php echo isset($MSG_RATIO) ? $MSG_RATIO : 'Ratio'?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 0;
                    foreach($view_rank as $row){
                        $rank++;
                    ?>
                    <tr>
                        <td class="text-center">
                            <?php if($rank == 1){ ?>
                            <span class="badge bg-warning text-dark">🥇</span>
                            <?php } elseif($rank == 2){ ?>
                            <span class="badge bg-secondary">🥈</span>
                            <?php } elseif($rank == 3){ ?>
                            <span class="badge" style="background:#cd7f32">🥉</span>
                            <?php } else { ?>
                            <span class="badge bg-light text-dark"><?php echo $rank?></span>
                            <?php } ?>
                        </td>
                        <td><?php echo isset($row[1]) ? $row[1] : ''?></td>
                        <td class="d-none d-md-table-cell"><?php echo isset($row[2]) ? $row[2] : ''?></td>
                        <td class="text-center">
                            <span class="badge bg-success"><?php echo isset($row[4]) ? $row[4] : '0'?></span>
                        </td>
                        <td class="text-center"><?php echo isset($row[5]) ? $row[5] : '0'?></td>
                        <td>
                            <?php
                            $ratio = isset($row[6]) ? $row[6] : '0%';
                            $ratio_num = floatval(str_replace('%', '', $ratio));
                            $color = $ratio_num >= 80 ? 'success' : ($ratio_num >= 50 ? 'info' : 'secondary');
                            ?>
                            <div class="progress mb-0" style="height:6px;min-width:80px">
                                <div class="progress-bar bg-<?php echo $color?>" style="width:<?php echo $ratio_num?>%"></div>
                            </div>
                            <small class="text-body-secondary"><?php echo $ratio?></small>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
        <div class="p-4 text-center text-body-secondary">
            <i class="bi bi-inbox" style="font-size:2rem"></i>
            <p class="mb-0 mt-2">No ranking data available.</p>
        </div>
        <?php } ?>
    </div>

    <?php if(isset($pages) && $pages > 1){ ?>
    <div class="card-footer">
        <nav aria-label="Ranklist pagination">
            <ul class="pagination justify-content-center mb-0 flex-wrap">
                <li class="page-item"><a class="page-link" href="ranklist.php?page=1">&laquo;&laquo;</a></li>
                <li class="page-item"><a class="page-link" href="ranklist.php?page=<?php echo (isset($page) && $page > 1) ? $page-1 : 1?>">&laquo;</a></li>
                <?php for($i = isset($spage) ? $spage : 1; $i <= (isset($epage) ? $epage : 1); $i++){ ?>
                <li class="page-item <?php if(isset($page) && $page == $i) echo 'active'?>"><a class="page-link" href="ranklist.php?page=<?php echo $i?>"><?php echo $i?></a></li>
                <?php } ?>
                <li class="page-item"><a class="page-link" href="ranklist.php?page=<?php echo isset($pages) ? $pages : 1?>">&raquo;&raquo;</a></li>
            </ul>
        </nav>
    </div>
    <?php } ?>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
