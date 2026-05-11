<?php $show_title="$MSG_PROBLEM.$MSG_STATISTICS - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-bar-chart"></i> <?php echo $MSG_PROBLEM?> <?php echo $id?> <?php echo $MSG_STATISTICS?></h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><?php echo $MSG_RESULT?></th>
                        <th class="text-center"><?php echo $MSG_COUNT?></th>
                        <th class="text-center"><?php echo $MSG_RATIO?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($view_problem_status as $row){
                    ?>
                    <tr>
                        <td><?php echo $jresult[$row['result']]?></td>
                        <td class="text-center"><?php echo $row['cnt']?></td>
                        <td class="text-center"><?php echo $total>0 ? round($row['cnt']*100/$total,1).'%' : '0%'?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
