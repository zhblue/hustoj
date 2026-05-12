<?php $show_title="$MSG_STATISTICS - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-eye"></i> <?php echo $row['title']?> - <?php echo $MSG_STATISTICS?></h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body text-center">
                        <h3 class="mb-0"><?php echo $total_users?></h3>
                        <small><?php echo $MSG_USERS?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body text-center">
                        <h3 class="mb-0"><?php echo $total_submissions?></h3>
                        <small><?php echo $MSG_SUBMISSIONS?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light mb-3">
                    <div class="card-body text-center">
                        <h3 class="mb-0"><?php echo $total_accepted?></h3>
                        <small><?php echo $MSG_ACCEPTED?></small>
                    </div>
                </div>
            </div>
        </div>

        <h5><?php echo $MSG_PROBLEM?> <?php echo $MSG_STATISTICS?></h5>
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><?php echo $MSG_PROBLEM?></th>
                        <th class="text-center"><?php echo $MSG_SUBMIT?></th>
                        <th class="text-center"><?php echo $MSG_AC?></th>
                        <th class="text-center"><?php echo $MSG_RATIO?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($problem_stats as $row){
                    ?>
                    <tr>
                        <td><a href="problem.php?cid=<?php echo $cid?>&pid=<?php echo $row['num']?>"><?php echo $PID[$row['num']]?></a></td>
                        <td class="text-center"><?php echo $row['submit']?></td>
                        <td class="text-center text-success"><?php echo $row['ac']?></td>
                        <td class="text-center"><?php echo $row['submit']>0 ? round($row['ac']*100/$row['submit'],1).'%' : '0%'?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
