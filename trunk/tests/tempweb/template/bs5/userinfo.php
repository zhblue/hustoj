<?php $show_title=(isset($MSG_USER_INFO)?$MSG_USER_INFO:"User Info")." - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="row g-4">
    <!-- Left sidebar -->
    <div class="col-lg-4">
        <!-- User card -->
        <div class="card mb-3">
            <div class="card-header text-center py-3">
                <div class="mb-2">
                    <i class="bi bi-person-circle" style="font-size:64px;color:#6c757d"></i>
                </div>
                <h5 class="mb-0"><?php echo htmlentities($user ?? '', ENT_QUOTES, 'utf-8')?></h5>
                <small class="text-body-secondary"><?php echo htmlentities($nick ?? '', ENT_QUOTES, 'utf-8')?></small>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-building me-2"></i><?php echo isset($MSG_SCHOOL) ? $MSG_SCHOOL : 'School'?></span>
                    <span class="text-end text-break" style="max-width:60%"><?php echo htmlentities($school ?? '', ENT_QUOTES, 'utf-8')?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-envelope me-2"></i><?php echo isset($MSG_EMAIL) ? $MSG_EMAIL : 'Email'?></span>
                    <span class="text-end text-break" style="max-width:60%"><?php echo htmlentities($email ?? '', ENT_QUOTES, 'utf-8')?></span>
                </li>
                <li class="list-group-item">
                    <i class="bi bi-calendar3 me-2"></i><?php echo isset($MSG_REG_TIME) ? $MSG_REG_TIME : 'Reg.Time'?>: <?php echo $reg_time ?? ''?>
                </li>
            </ul>
        </div>

        <!-- Statistics card -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i><?php echo isset($MSG_STATISTICS) ? $MSG_STATISTICS : 'Statistics'?></h5>
            </div>
            <div class="card-body">
                <div class="row text-center g-2">
                    <div class="col-6">
                        <div class="p-3 rounded bg-success bg-opacity-10 border">
                            <div class="h3 mb-0 text-success fw-bold"><?php echo isset($solved) ? $solved : 0?></div>
                            <small class="text-body-secondary text-uppercase"><?php echo isset($MSG_SOVLED) ? $MSG_SOVLED : 'Solved'?></small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded bg-primary bg-opacity-10 border">
                            <div class="h3 mb-0 text-primary fw-bold"><?php echo isset($submit) ? $submit : 0?></div>
                            <small class="text-body-secondary text-uppercase"><?php echo isset($MSG_SUBMIT) ? $MSG_SUBMIT : 'Submit'?></small>
                        </div>
                    </div>
                </div>
                <?php if(isset($ratio)){ ?>
                <div class="mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-body-secondary"><?php echo isset($MSG_RATIO) ? $MSG_RATIO : 'Ratio'?></small>
                        <small class="fw-bold"><?php echo $ratio?>%</small>
                    </div>
                    <div class="progress" style="height:8px">
                        <div class="progress-bar bg-success" role="progressbar" style="width:<?php echo $ratio?>%"></div>
                        <div class="progress-bar bg-danger" role="progressbar" style="width:<?php echo 100-$ratio?>%"></div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div><!-- /left col -->

    <!-- Right content -->
    <div class="col-lg-8">
        <!-- Solved list -->
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="bi bi-list-ol me-2"></i><?php echo isset($MSG_SOVLED) ? $MSG_SOVLED : 'Solved'?> <?php echo isset($MSG_LIST) ? $MSG_LIST : 'List'?>
                    <span class="badge bg-success ms-2"><?php echo isset($solved) ? $solved : 0?></span>
                </h4>
            </div>
            <div class="card-body p-0">
                <?php if(isset($solved_list) && is_array($solved_list) && count($solved_list) > 0){ ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><?php echo isset($MSG_PROBLEM) ? $MSG_PROBLEM : 'Problem'?></th>
                                <th><?php echo isset($MSG_TITLE) ? $MSG_TITLE : 'Title'?></th>
                                <th><?php echo isset($MSG_AC_TIME) ? $MSG_AC_TIME : 'AC Time'?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($solved_list as $row){ ?>
                            <tr>
                                <td><a href="problem.php?id=<?php echo $row['problem_id'] ?? ''?>"><?php echo $row['problem_id'] ?? ''?></a></td>
                                <td><?php echo htmlentities($row['title'] ?? '', ENT_QUOTES, 'utf-8')?></td>
                                <td><small class="text-body-secondary"><?php echo isset($row['in_date']) ? substr($row['in_date'], 5, 11) : ''?></small></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } else { ?>
                <div class="p-4 text-center text-body-secondary">
                    <i class="bi bi-inbox" style="font-size:2rem"></i>
                    <p class="mb-0 mt-2">No solved problems yet.</p>
                </div>
                <?php } ?>
            </div>
        </div>
    </div><!-- /right col -->
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
