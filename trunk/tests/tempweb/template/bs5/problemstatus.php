<?php $show_title="$MSG_PROBLEM.$MSG_STATISTICS - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0">
            <i class="bi bi-bar-chart"></i> <?php echo isset($MSG_PROBLEM) ? $MSG_PROBLEM : 'Problem'?> <?php echo isset($id) ? $id : ''?> <?php echo isset($MSG_STATISTICS) ? $MSG_STATISTICS : 'Statistics'?>
        </h4>
    </div>
    <div class="card-body">
        <?php if(isset($view_problem_status) && is_array($view_problem_status)){ ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th><?php echo isset($MSG_RESULT) ? $MSG_RESULT : 'Result'?></th>
                        <th class="text-center" style="width:120px"><?php echo isset($MSG_COUNT) ? $MSG_COUNT : 'Count'?></th>
                        <th class="text-center" style="width:40%"><?php echo isset($MSG_RATIO) ? $MSG_RATIO : 'Ratio'?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = isset($total) ? $total : 0;
                    foreach($view_problem_status as $row){
                        $pct = $total > 0 ? round($row['cnt'] * 100 / $total, 1) : 0;
                        // Bootstrap colors per result
                        $result_idx = intval($row['result']);
                        if($result_idx == 4) {
                            $color = 'success';
                        } elseif($result_idx == 5 || $result_idx == 6) {
                            $color = 'danger';
                        } elseif($result_idx == 7) {
                            $color = 'warning';
                        } elseif($result_idx == 8 || $result_idx == 9) {
                            $color = 'info';
                        } else {
                            $color = 'secondary';
                        }
                    ?>
                    <tr>
                        <td>
                            <span class="badge bg-<?php echo $color?>"><?php echo isset($jresult[$result_idx]) ? $jresult[$result_idx] : $result_idx?></span>
                        </td>
                        <td class="text-center fw-bold"><?php echo $row['cnt']?></td>
                        <td>
                            <div class="progress" style="height:20px">
                                <div class="progress-bar bg-<?php echo $color?>" role="progressbar" style="width:<?php echo $pct?>%">
                                    <?php echo $pct?>%
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
        <p class="text-body-secondary">No statistics available.</p>
        <?php } ?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
