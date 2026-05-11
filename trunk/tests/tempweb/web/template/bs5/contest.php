<?php $show_title="$MSG_CONTEST - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4><i class="bi bi-trophy"></i> <?php echo $MSG_CONTEST?></h4>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item"><a class="nav-link <?php echo !isset($_GET['my'])?'active':''?>" href="contest.php"><?php echo $MSG_ALL_CONTEST?></a></li>
            <?php if(isset($_SESSION[$OJ_NAME.'_user_id'])){ ?>
            <li class="nav-item"><a class="nav-link <?php echo isset($_GET['my'])?'active':''?>" href="contest.php?my"><?php echo $MSG_MY_CONTEST?></a></li>
            <?php } ?>
        </ul>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><?php echo $MSG_CONTEST_ID?></th>
                        <th><?php echo $MSG_CONTEST_NAME?></th>
                        <th class="d-none d-md-table-cell"><?php echo $MSG_START_TIME?></th>
                        <th class="d-none d-md-table-cell"><?php echo $MSG_END_TIME?></th>
                        <th><?php echo $MSG_STATUS?></th>
                        <th><?php echo $MSG_TYPE?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($view_contest as $row){
                        $start_time = strtotime($row['start_time']);
                        $end_time = strtotime($row['end_time']);
                        $now = time();
                        $status = "";
                        $class = "";
                        if($now < $start_time){
                            $status = $MSG_PENDING;
                            $class = "text-info";
                        } else if($now >= $start_time && $now <= $end_time){
                            $status = $MSG_RUNNING;
                            $class = "text-success";
                        } else {
                            $status = $MSG_ENDED;
                            $class = "text-muted";
                        }
                    ?>
                    <tr>
                        <td><?php echo $row['contest_id']?></td>
                        <td><a href="contest.php?cid=<?php echo $row['contest_id']?>"><?php echo $row['title']?></a></td>
                        <td class="d-none d-md-table-cell"><?php echo $row['start_time']?></td>
                        <td class="d-none d-md-table-cell"><?php echo $row['end_time']?></td>
                        <td class="<?php echo $class?>"><?php echo $status?></td>
                        <td><span class="badge bg-info"><?php echo $row['contest_type']?></span></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if(isset($pages) && $pages>1){ ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item"><a class="page-link" href="contest.php?page=1<?php echo isset($_GET['my'])?'&my':''?>">&laquo;&laquo;</a></li>
                <li class="page-item"><a class="page-link" href="contest.php?page=<?php echo $page==1?1:$page-1?><?php echo isset($_GET['my'])?'&my':''?>">&laquo;</a></li>
                <?php for($i=$spage; $i<=$epage; $i++){ ?>
                <li class="page-item <?php if($page==$i) echo 'active'; ?>"><a class="page-link" href="contest.php?page=<?php echo $i?><?php echo isset($_GET['my'])?'&my':''?>"><?php echo $i?></a></li>
                <?php } ?>
                <li class="page-item"><a class="page-link" href="contest.php?page=<?php echo $page==$pages?$pages:$page+1?><?php echo isset($_GET['my'])?'&my':''?>">&raquo;</a></li>
                <li class="page-item"><a class="page-link" href="contest.php?page=<?php echo $pages?><?php echo isset($_GET['my'])?'&my':''?>">&raquo;&raquo;</a></li>
            </ul>
        </nav>
        <?php } ?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
