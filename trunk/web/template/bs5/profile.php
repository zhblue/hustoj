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
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong><?php echo $MSG_NICK?>:</strong> <?php echo htmlentities($profile['nick'],ENT_QUOTES,'utf-8')?></li>
                <li class="list-group-item"><strong><?php echo $MSG_SCHOOL?>:</strong> <?php echo htmlentities($profile['school'],ENT_QUOTES,'utf-8')?></li>
                <li class="list-group-item"><strong><?php echo $MSG_EMAIL?>:</strong> <?php echo htmlentities($profile['email'],ENT_QUOTES,'utf-8')?></li>
                <li class="list-group-item"><strong><?php echo $MSG_REG_TIME?>:</strong> <?php echo $profile['reg_time']?></li>
            </ul>
        </div>

        <?php if(isset($submitted_code_ratio)){ ?>
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-chart-pie"></i> <?php echo $MSG_STATISTICS?></h5>
            </div>
            <div class="card-body">
                <p><strong><?php echo $MSG_SOVLED?>:</strong> <span class="text-success fw-bold"><?php echo $solved?></span></p>
                <p><strong><?php echo $MSG_SUBMIT?>:</strong> <?php echo $submit?></p>
                <p><strong><?php echo $MSG_RATIO?>:</strong> <?php echo $submitted_code_ratio?>%</p>
            </div>
        </div>
        <?php } ?>
    </div>

    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-list-ol"></i> <?php echo $MSG_RECENT_PROBLEM?></h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th><?php echo $MSG_PROBLEM?></th>
                                <th><?php echo $MSG_RESULT?></th>
                                <th><?php echo $MSG_LANG?></th>
                                <th><?php echo $MSG_TIME?></th>
                                <th><?php echo $MSG_MEMORY?></th>
                                <th><?php echo $MSG_CODE_LENGTH?></th>
                                <th><?php echo $MSG_SUBMIT_TIME?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($view_solution as $row){
                            ?>
                            <tr>
                                <td><a href="problem.php?id=<?php echo $row['problem_id']?>"><?php echo $row['problem_id']?></a></td>
                                <td><span class="<?php echo $row['result']==4?'text-success':''?>"><?php echo $jresult[$row['result']]?></span></td>
                                <td><?php echo $language_name[$row['language']]?></td>
                                <td><?php echo $row['time']?></td>
                                <td><?php echo $row['memory']?></td>
                                <td><?php echo $row['code_length']?></td>
                                <td><?php echo substr($row['in_date'],5,11)?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
