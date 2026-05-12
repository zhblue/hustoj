<?php $show_title="$MSG_STATUS - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-trophy"></i> <?php echo $row['title']?> - <?php echo $MSG_STATUS?></h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-dark">
                    <tr>
                        <th><?php echo $MSG_RUN_ID?></th>
                        <th><?php echo $MSG_USER?></th>
                        <th><?php echo $MSG_PROBLEM?></th>
                        <th><?php echo $MSG_RESULT?></th>
                        <th><?php echo $MSG_LANG?></th>
                        <th><?php echo $MSG_TIME?></th>
                        <th><?php echo $MSG_MEMORY?></th>
                        <th><?php echo $MSG_SUBMIT_TIME?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($view_status as $row){
                    ?>
                    <tr>
                        <td><?php echo $row['solution_id']?></td>
                        <td><a href="userinfo.php?user=<?php echo htmlentities($row['user_id'],ENT_QUOTES,'utf-8')?>"><?php echo htmlentities($row['user_id'],ENT_QUOTES,'utf-8')?></a></td>
                        <td><a href="problem.php?cid=<?php echo $cid?>&pid=<?php echo $row['num']?>"><?php echo $PID[$row['num']]?></a></td>
                        <td><span class="<?php echo $row['result']==4?'text-success':''?>"><?php echo $jresult[$row['result']]?></span></td>
                        <td><?php echo $language_name[$row['language']]?></td>
                        <td><?php echo $row['time']?> ms</td>
                        <td><?php echo $row['memory']?> KB</td>
                        <td><?php echo substr($row['in_date'],5,11)?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
