<?php $show_title="$MSG_RECENT_CONTEST - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-calendar-event"></i> <?php echo $MSG_RECENT_CONTEST?></h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><?php echo $MSG_CONTEST_NAME?></th>
                        <th><?php echo $MSG_START_TIME?></th>
                        <th><?php echo $MSG_END_TIME?></th>
                        <th><?php echo $MSG_STATUS?></th>
                        <th><?php echo $MSG_TYPE?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($view_recent_contest as $row){
                        $start_time = strtotime($row['start_time']);
                        $end_time = strtotime($row['end_time']);
                        $now = time();
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
                        <td><a href="contest.php?cid=<?php echo $row['contest_id']?>"><?php echo $row['title']?></a></td>
                        <td><?php echo $row['start_time']?></td>
                        <td><?php echo $row['end_time']?></td>
                        <td class="<?php echo $class?>"><?php echo $status?></td>
                        <td><span class="badge bg-info"><?php echo $row['contest_type']?></span></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
