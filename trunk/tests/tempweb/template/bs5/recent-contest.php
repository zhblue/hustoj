<?php $show_title=isset($MSG_RECENT_CONTEST) ? "$MSG_RECENT_CONTEST - $OJ_NAME" : "Recent Contest - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-calendar-event me-2"></i><?php echo isset($MSG_RECENT_CONTEST) ? $MSG_RECENT_CONTEST : 'Recent Contests'?></h4>
    </div>
    <div class="card-body p-0">
        <?php if(isset($view_recent_contest) && is_array($view_recent_contest) && count($view_recent_contest) > 0){ ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th><?php echo isset($MSG_CONTEST_NAME) ? $MSG_CONTEST_NAME : 'Contest Name'?></th>
                        <th><?php echo isset($MSG_START_TIME) ? $MSG_START_TIME : 'Start'?></th>
                        <th><?php echo isset($MSG_END_TIME) ? $MSG_END_TIME : 'End'?></th>
                        <th><?php echo isset($MSG_STATUS) ? $MSG_STATUS : 'Status'?></th>
                        <th><?php echo isset($MSG_TYPE) ? $MSG_TYPE : 'Type'?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($view_recent_contest as $row){
                        $start_time = strtotime($row['start_time'] ?? '');
                        $end_time = strtotime($row['end_time'] ?? '');
                        $now = time();
                        if($now < $start_time){
                            $status = isset($MSG_PENDING) ? $MSG_PENDING : 'Pending';
                            $badge = 'bg-secondary';
                        } else if($now >= $start_time && $now <= $end_time){
                            $status = isset($MSG_RUNNING) ? $MSG_RUNNING : 'Running';
                            $badge = 'bg-success';
                        } else {
                            $status = isset($MSG_ENDED) ? $MSG_ENDED : 'Ended';
                            $badge = 'bg-dark';
                        }
                    ?>
                    <tr>
                        <td>
                            <a href="contest.php?cid=<?php echo $row['contest_id'] ?? ''?>" class="text-decoration-none">
                                <?php echo htmlentities($row['title'] ?? '', ENT_QUOTES, 'utf-8')?>
                            </a>
                        </td>
                        <td><small><?php echo $row['start_time'] ?? ''?></small></td>
                        <td><small><?php echo $row['end_time'] ?? ''?></small></td>
                        <td><span class="badge <?php echo $badge?>"><?php echo $status?></span></td>
                        <td><span class="badge bg-info"><?php echo isset($row['contest_type']) ? $row['contest_type'] : ''?></span></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
        <div class="p-4 text-center text-body-secondary">
            <i class="bi bi-calendar-x" style="font-size:2rem"></i>
            <p class="mb-0 mt-2">No recent contests.</p>
        </div>
        <?php } ?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
