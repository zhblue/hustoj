<?php $show_title="$MSG_STATISTICS - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-eye"></i> <?php echo isset($row['title']) ? htmlentities($row['title'], ENT_QUOTES, 'utf-8') : ''?> - <?php echo isset($MSG_STATISTICS) ? $MSG_STATISTICS : 'Statistics'?></h4>
    </div>
    <div class="card-body">
        <!-- Summary stat cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card text-center border-primary">
                    <div class="card-body">
                        <h3 class="mb-1 text-primary"><?php echo isset($total_users) ? $total_users : 0?></h3>
                        <small class="text-body-secondary text-uppercase fw-light"><?php echo isset($MSG_USERS) ? $MSG_USERS : 'Users'?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-info">
                    <div class="card-body">
                        <h3 class="mb-1 text-info"><?php echo isset($total_submissions) ? $total_submissions : 0?></h3>
                        <small class="text-body-secondary text-uppercase fw-light"><?php echo isset($MSG_SUBMISSIONS) ? $MSG_SUBMISSIONS : 'Submissions'?></small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center border-success">
                    <div class="card-body">
                        <h3 class="mb-1 text-success"><?php echo isset($total_accepted) ? $total_accepted : 0?></h3>
                        <small class="text-body-secondary text-uppercase fw-light"><?php echo isset($MSG_ACCEPTED) ? $MSG_ACCEPTED : 'Accepted'?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Problem stats -->
        <h5 class="mb-3"><i class="bi bi-list-ol"></i> <?php echo isset($MSG_PROBLEM) ? $MSG_PROBLEM : 'Problem'?> <?php echo isset($MSG_STATISTICS) ? $MSG_STATISTICS : 'Statistics'?></h5>
        <?php if(isset($problem_stats) && is_array($problem_stats)){ ?>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th><?php echo isset($MSG_PROBLEM) ? $MSG_PROBLEM : 'Problem'?></th>
                        <th class="text-center" style="width:100px"><?php echo isset($MSG_SUBMIT) ? $MSG_SUBMIT : 'Submit'?></th>
                        <th class="text-center" style="width:100px"><?php echo isset($MSG_AC) ? $MSG_AC : 'AC'?></th>
                        <th class="text-center" style="width:40%"><?php echo isset($MSG_RATIO) ? $MSG_RATIO : 'AC Ratio'?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($problem_stats as $row){ ?>
                    <?php $pct = isset($row['submit']) && $row['submit'] > 0 ? round($row['ac'] * 100 / $row['submit'], 1) : 0; ?>
                    <tr>
                        <td>
                            <a href="problem.php?cid=<?php echo isset($cid) ? $cid : ''?>&pid=<?php echo isset($row['num']) ? $row['num'] : ''?>">
                                <?php echo isset($PID) && isset($row['num']) ? $PID[$row['num']] : (isset($row['num']) ? $row['num'] : '')?>
                            </a>
                        </td>
                        <td class="text-center"><?php echo isset($row['submit']) ? $row['submit'] : 0?></td>
                        <td class="text-center text-success fw-bold"><?php echo isset($row['ac']) ? $row['ac'] : 0?></td>
                        <td>
                            <div class="progress" style="height:18px">
                                <div class="progress-bar bg-success" role="progressbar" style="width:<?php echo $pct?>%">
                                    <?php echo $pct?>%
                                </div>
                                <?php if($pct < 100){ ?>
                                <div class="progress-bar bg-danger" role="progressbar" style="width:<?php echo 100-$pct?>%"></div>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } ?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
