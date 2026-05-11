<?php $show_title="$MSG_USER_INFO - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-person-circle"></i> <?php echo htmlentities($user,ENT_QUOTES,'utf-8')?></h4>
            </div>
            <div class="card-body text-center">
                <i class="bi bi-person-circle" style="font-size: 80px; color: #ccc;"></i>
                <h4 class="mt-2"><?php echo htmlentities($nick,ENT_QUOTES,'utf-8')?></h4>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong><?php echo $MSG_SCHOOL?>:</strong> <?php echo htmlentities($school,ENT_QUOTES,'utf-8')?></li>
                <li class="list-group-item"><strong><?php echo $MSG_EMAIL?>:</strong> <?php echo htmlentities($email,ENT_QUOTES,'utf-8')?></li>
                <li class="list-group-item"><strong><?php echo $MSG_REG_TIME?>:</strong> <?php echo $reg_time?></li>
            </ul>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-chart-pie"></i> <?php echo $MSG_STATISTICS?></h5>
            </div>
            <div class="card-body">
                <p><strong><?php echo $MSG_SOVLED?>:</strong> <span class="text-success fw-bold"><?php echo $solved?></span></p>
                <p><strong><?php echo $MSG_SUBMIT?>:</strong> <?php echo $submit?></p>
                <div class="progress">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $ratio?>%">
                        <?php echo $ratio?>%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-list-ol"></i> <?php echo $MSG_SOVLED?> <?php echo $MSG_LIST?></h4>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th><?php echo $MSG_PROBLEM?></th>
                            <th><?php echo $MSG_TITLE?></th>
                            <th><?php echo $MSG_AC_TIME?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach($solved_list as $row){
                        ?>
                        <tr>
                            <td><a href="problem.php?id=<?php echo $row['problem_id']?>"><?php echo $row['problem_id']?></a></td>
                            <td><?php echo htmlentities($row['title'],ENT_QUOTES,'utf-8')?></td>
                            <td><?php echo substr($row['in_date'],5,11)?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
