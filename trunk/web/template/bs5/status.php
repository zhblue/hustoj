<?php $show_title="$MSG_STATUS - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4><i class="bi bi-check2-square"></i> <?php echo $MSG_STATUS?></h4>
    </div>
    <div class="card-body">
        <form action="status.php" method="get" class="row g-3 mb-3">
            <div class="col-auto">
                <input class="form-control" type="text" name="problem_id" placeholder="<?php echo $MSG_PROBLEM_ID?>" value="<?php echo isset($_GET['problem_id'])?htmlentities($_GET['problem_id'],ENT_QUOTES,'utf-8'):''?>">
            </div>
            <div class="col-auto">
                <input class="form-control" type="text" name="user_id" placeholder="<?php echo $MSG_USER_ID?>" value="<?php echo isset($_GET['user_id'])?htmlentities($_GET['user_id'],ENT_QUOTES,'utf-8'):''?>">
            </div>
            <div class="col-auto">
                <select class="form-select" name="jresult">
                    <option value="">-- <?php echo $MSG_RESULT?> --</option>
                    <?php
                    foreach($jresult as $v=>$name){
                        $selected = (isset($_GET['jresult']) && $_GET['jresult']==$v) ? "selected" : "";
                        echo "<option value='$v' $selected>$name</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> <?php echo $MSG_SEARCH?></button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead class="table-dark">
                    <tr>
                        <th><?php echo $MSG_RUN_ID?></th>
                        <th><?php echo $MSG_USER?></th>
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
                    foreach($view_status as $row){
                        $cid = isset($row['contest_id']) ? $row['contest_id'] : 0;
                        $pid = isset($row['problem_id']) ? $row['problem_id'] : $row[4];
                    ?>
                    <tr>
                        <td><?php echo $row['solution_id']?></td>
                        <td><a href="userinfo.php?user=<?php echo htmlentities($row['user_id'],ENT_QUOTES,'utf-8')?>"><?php echo htmlentities($row['user_id'],ENT_QUOTES,'utf-8')?></a></td>
                        <td>
                            <?php if($cid > 0){ ?>
                            <a href="problem.php?cid=<?php echo $cid?>&pid=<?php echo $pid?>"><?php echo $row['problem_id']?></a>
                            <?php } else { ?>
                            <a href="problem.php?id=<?php echo $row['problem_id']?>"><?php echo $row['problem_id']?></a>
                            <?php } ?>
                        </td>
                        <td>
                            <?php
                            $result_class = $row['result']==4 ? 'text-success' : ($row['result']>=5 && $row['result']!=6 ? 'text-danger' : '');
                            ?>
                            <span class="<?php echo $result_class?>"><?php echo $jresult[$row['result']]?></span>
                        </td>
                        <td><?php echo $language_name[$row['language']]?></td>
                        <td><?php echo $row['time']?> ms</td>
                        <td><?php echo $row['memory']?> KB</td>
                        <td><?php echo $row['code_length']?></td>
                        <td><?php echo substr($row['in_date'],5,11)?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if(isset($pages) && $pages>1){ ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item"><a class="page-link" href="status.php?page=1&<?php echo $str_page?>">&laquo;&laquo;</a></li>
                <li class="page-item"><a class="page-link" href="status.php?page=<?php echo $page==1?1:$page-1?>&<?php echo $str_page?>">&laquo;</a></li>
                <?php for($i=$spage; $i<=$epage; $i++){ ?>
                <li class="page-item <?php if($page==$i) echo 'active'; ?>"><a class="page-link" href="status.php?page=<?php echo $i?>&<?php echo $str_page?>"><?php echo $i?></a></li>
                <?php } ?>
                <li class="page-item"><a class="page-link" href="status.php?page=<?php echo $page==$pages?$pages:$page+1?>&<?php echo $str_page?>">&raquo;</a></li>
                <li class="page-item"><a class="page-link" href="status.php?page=<?php echo $pages?>&<?php echo $str_page?>">&raquo;&raquo;</a></li>
            </ul>
        </nav>
        <?php } ?>
    </div>
</div>

<script>
$(document).ready(function(){
    // Auto-refresh
    <?php if(isset($OJ_AUTO_REFRESH) && $OJ_AUTO_REFRESH){ ?>
    setTimeout(function(){
        location.reload();
    }, 30000);
    <?php } ?>
});
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
