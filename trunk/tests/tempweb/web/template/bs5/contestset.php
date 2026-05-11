<?php $show_title="$MSG_CONTESTSET - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-collection"></i> <?php echo $MSG_CONTESTSET?></h4>
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
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($view_contest as $row){
                    ?>
                    <tr>
                        <td><a href="contest.php?cid=<?php echo $row['contest_id']?>"><?php echo $row['title']?></a></td>
                        <td><?php echo $row['start_time']?></td>
                        <td><?php echo $row['end_time']?></td>
                        <td><span class="badge bg-<?php echo strtotime($row['end_time'])<time()?'secondary':'info'?>"><?php echo $row['contest_type']?></span></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
