<?php $show_title=isset($title) ? "Contest RankList (OI) -- $title - $OJ_NAME" : "Contest OI RankList - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0">
                    <i class="bi bi-trophy"></i>
                    <?php echo isset($cid) ? "Contest $cid" : ''?>
                    <?php echo isset($title) ? ' - ' . htmlentities($title, ENT_QUOTES, 'utf-8') : ''?>
                    <span class="badge bg-info ms-2">OI Mode</span>
                </h4>
                <?php if(isset($start_time) && isset($end_time)){ ?>
                <small class="text-body-secondary">
                    <?php echo date('Y-m-d H:i', $start_time) ?> ~
                    <?php echo date('Y-m-d H:i', $end_time) ?>
                    <?php if(isset($lock) && $lock){ ?>
                        | <span class="text-danger"><i class="bi bi-lock"></i> Board Locked</span>
                    <?php } ?>
                </small>
                <?php } ?>
            </div>
            <div class="btn-group">
                <a class="btn btn-sm btn-outline-primary" href="contestrank.xls.php?cid=<?php echo isset($cid) ? $cid : ''?>">
                    <i class="bi bi-download"></i> <?php echo isset($MSG_DOWNLOAD) ? $MSG_DOWNLOAD : 'Download'?>
                </a>
                <a class="btn btn-sm btn-outline-success" href="contestrank4.php?cid=<?php echo isset($cid) ? $cid : ''?>">
                    <i class="bi bi-arrow-repeat"></i> Refresh
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <?php
        $users = isset($U) ? $U : array();
        $cnt = isset($user_cnt) ? $user_cnt : 0;
        $pcnt = isset($pid_cnt) ? $pid_cnt : 0;
        ?>
        <?php if($cnt > 0){ ?>
        <div style="overflow-x:auto">
            <table class="table table-bordered table-sm text-center align-middle mb-0" style="table-layout:fixed">
                <thead class="table-dark">
                    <tr>
                        <th style="width:50px">#</th>
                        <th style="width:100px"><?php echo isset($MSG_USER) ? $MSG_USER : 'User'?></th>
                        <th style="width:80px"><?php echo isset($MSG_NICK) ? $MSG_NICK : 'Nick'?></th>
                        <th style="width:50px"><?php echo isset($MSG_SOVLED) ? $MSG_SOVLED : 'Solved'?></th>
                        <th style="width:70px"><?php echo isset($MSG_SCORE) ? $MSG_SCORE : 'Score'?></th>
                        <?php for($i = 0; $i < $pcnt; $i++){ ?>
                            <th style="width:50px">
                                <a href="problem.php?cid=<?php echo isset($cid) ? $cid : ''?>&pid=<?php echo $i?>">
                                    <?php echo isset($PID[$i]) ? $PID[$i] : chr(65+$i)?>
                                </a>
                            </th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $rank = 1;
                    for($i = 0; $i < $cnt; $i++){
                        $u = $U[$i] ?? null;
                        if(!$u) continue;
                        $uuid = $u->user_id ?? '';
                        $nick = $u->nick ?? '';
                        $usolved = $u->solved ?? 0;
                        $utotal = $u->total ?? 0;
                    ?>
                    <tr>
                        <td>
                            <?php
                            if($rank == 1) echo '<span class="badge bg-warning text-dark">🥇</span>';
                            else if($rank == 2) echo '<span class="badge bg-secondary">🥈</span>';
                            else if($rank == 3) echo '<span class="badge" style="background:#cd7f32">🥉</span>';
                            else echo '<span class="badge bg-light text-dark">'.$rank.'</span>';
                            $rank++;
                            ?>
                        </td>
                        <td>
                            <?php if(isset($_GET['user_id']) && $uuid == $_GET['user_id']){ ?>
                                <span style="background:#ffff77">
                            <?php } ?>
                            <a href="userinfo.php?user=<?php echo urlencode($uuid)?>"><?php echo htmlentities($uuid)?></a>
                            <?php if(isset($_GET['user_id']) && $uuid == $_GET['user_id']){ ?></span><?php } ?>
                        </td>
                        <td><small><?php echo htmlentities($nick, ENT_QUOTES, 'utf-8')?></small></td>
                        <td class="fw-bold"><?php echo $usolved?></td>
                        <td class="fw-bold text-primary"><?php echo $utotal?></td>
                        <?php
                        for($j = 0; $j < $pcnt; $j++){
                            $score = $u->p_pass_rate[$j] ?? null;
                            if($score !== null && $score > 0){
                                if($score == 100) $cls = 'bg-success';
                                else if($score >= 50) $cls = 'bg-warning text-dark';
                                else $cls = 'bg-secondary';
                                echo '<td><span class="badge '.$cls.'">'.round($score,1).'</span></td>';
                            } else {
                                echo '<td><span class="text-body-secondary">-</span></td>';
                            }
                        }
                        ?>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
        <div class="p-4 text-center text-body-secondary">
            <p class="mb-0">No ranking data available.</p>
        </div>
        <?php } ?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
