<?php $show_title="$MSG_RANKLIST - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4><i class="bi bi-trophy"></i> <?php echo $row['title']?> - <?php echo $MSG_RANKLIST?></h4>
        <p class="mb-0 text-muted"><?php echo $MSG_START_TIME?>: <?php echo $row['start_time']?> | <?php echo $MSG_END_TIME?>: <?php echo $row['end_time']?></p>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center">#</th>
                        <th><?php echo $MSG_USER?></th>
                        <?php foreach($pid_list as $pid){ ?>
                        <th class="text-center"><a href="problem.php?cid=<?php echo $cid?>&pid=<?php echo $pid?>"><?php echo $PID[$pid]?></a></th>
                        <?php } ?>
                        <th class="text-center"><?php echo $MSG_SOVLED?></th>
                        <th class="text-center"><?php echo $MSG_PENALTY?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank=0;
                    foreach($view_rank as $row){
                        $rank++;
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $rank?></td>
                        <td><a href="userinfo.php?user=<?php echo htmlentities($row['user_id'],ENT_QUOTES,'utf-8')?>"><?php echo htmlentities($row['user_id'],ENT_QUOTES,'utf-8')?></a></td>
                        <?php for($i=0;$i<$pcount;$i++){ ?>
                        <td class="text-center <?php echo isset($row['ac_time'][$i]) && $row['ac_time'][$i]>0 ? ($row['wa_count'][$i]>0 ? 'text-warning' : 'text-success') : 'text-muted'?>">
                            <?php
                            if(isset($row['ac_time'][$i]) && $row['ac_time'][$i]>0){
                                echo $row['ac_time'][$i]."'";
                                if($row['wa_count'][$i]>0) echo " (-".$row['wa_count'][$i].")";
                            } else if(isset($row['wa_count'][$i]) && $row['wa_count'][$i]>0){
                                echo "(-".$row['wa_count'][$i].")";
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>
                        <?php } ?>
                        <td class="text-center text-success fw-bold"><?php echo $row['solved']?></td>
                        <td class="text-center"><?php echo $row['penalty']?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
