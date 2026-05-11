<?php $show_title=isset($row['title']) ? $row['title']." - $MSG_STATUS - $OJ_NAME" : "Status - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0">
            <i class="bi bi-trophy"></i> <?php echo isset($row['title']) ? htmlentities($row['title'], ENT_QUOTES, 'utf-8') : ''?>
            <span class="badge bg-secondary ms-1"><?php echo isset($MSG_STATUS) ? $MSG_STATUS : 'Status'?></span>
        </h4>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th><?php echo isset($MSG_RUN_ID) ? $MSG_RUN_ID : 'RunID'?></th>
                        <th><?php echo isset($MSG_USER) ? $MSG_USER : 'User'?></th>
                        <th><?php echo isset($MSG_PROBLEM) ? $MSG_PROBLEM : 'Problem'?></th>
                        <th><?php echo isset($MSG_RESULT) ? $MSG_RESULT : 'Result'?></th>
                        <th><?php echo isset($MSG_LANG) ? $MSG_LANG : 'Lang'?></th>
                        <th><?php echo isset($MSG_TIME) ? $MSG_TIME : 'Time'?></th>
                        <th><?php echo isset($MSG_MEMORY) ? $MSG_MEMORY : 'Memory'?></th>
                        <th><?php echo isset($MSG_SUBMIT_TIME) ? $MSG_SUBMIT_TIME : 'Submit Time'?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(isset($view_status) && is_array($view_status)){
                    foreach($view_status as $row){
                        $result_idx = intval($row['result'] ?? 0);
                        if($result_idx == 4) $color = 'success';
                        elseif(in_array($result_idx, [5,6])) $color = 'danger';
                        elseif($result_idx == 7) $color = 'warning';
                        elseif(in_array($result_idx, [8,9])) $color = 'info';
                        else $color = 'secondary';
                    ?>
                    <tr>
                        <td><?php echo isset($row['solution_id']) ? $row['solution_id'] : ''?></td>
                        <td>
                            <a href="userinfo.php?user=<?php echo urlencode($row['user_id'] ?? '')?>">
                                <?php echo htmlentities($row['user_id'] ?? '', ENT_QUOTES, 'utf-8')?>
                            </a>
                        </td>
                        <td>
                            <a href="problem.php?cid=<?php echo isset($cid) ? $cid : ''?>&pid=<?php echo isset($row['num']) ? $row['num'] : ''?>">
                                <?php echo isset($PID) && isset($row['num']) ? $PID[$row['num']] : (isset($row['num']) ? $row['num'] : '')?>
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $color?>">
                                <?php echo isset($jresult[$result_idx]) ? $jresult[$result_idx] : $result_idx?>
                            </span>
                        </td>
                        <td><?php echo isset($language_name[$row['language']]) ? $language_name[$row['language']] : ''?></td>
                        <td><?php echo isset($row['time']) ? $row['time'] : ''?> ms</td>
                        <td><?php echo isset($row['memory']) ? $row['memory'] : ''?> KB</td>
                        <td><?php echo isset($row['in_date']) ? substr($row['in_date'], 5, 11) : ''?></td>
                    </tr>
                    <?php }} ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
