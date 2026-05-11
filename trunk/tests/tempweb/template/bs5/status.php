<?php $show_title="$MSG_STATUS - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h4 class="mb-0"><i class="bi bi-check2-square"></i> <?php echo isset($MSG_STATUS) ? $MSG_STATUS : 'Status'?></h4>
        <?php if(!isset($view_status) || empty($view_status)){ ?>
        <div class="spinner-border spinner-border-sm text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <?php } ?>
    </div>
    <div class="card-body">
        <form action="status.php" method="get" class="row g-3 mb-3">
            <div class="col-auto">
                <input class="form-control" type="text" name="problem_id" placeholder="<?php echo isset($MSG_PROBLEM_ID) ? $MSG_PROBLEM_ID : 'Problem ID'?>" value="<?php echo isset($_GET['problem_id'])?htmlentities($_GET['problem_id'],ENT_QUOTES,'utf-8'):''?>">
            </div>
            <div class="col-auto">
                <input class="form-control" type="text" name="user_id" placeholder="<?php echo isset($MSG_USER_ID) ? $MSG_USER_ID : 'User ID'?>" value="<?php echo isset($_GET['user_id'])?htmlentities($_GET['user_id'],ENT_QUOTES,'utf-8'):''?>">
            </div>
            <div class="col-auto">
                <select class="form-select" name="jresult">
                    <option value="">-- <?php echo isset($MSG_RESULT) ? $MSG_RESULT : 'Result'?> --</option>
                    <?php
                    if(isset($jresult)){
                    foreach($jresult as $v=>$name){
                        $selected = (isset($_GET['jresult']) && $_GET['jresult']==$v) ? "selected" : "";
                        echo "<option value='$v' $selected>$name</option>";
                    }}
                    ?>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> <?php echo isset($MSG_SEARCH) ? $MSG_SEARCH : 'Search'?></button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle">
                <thead class="table-dark">
                    <tr>
                        <th><?php echo isset($MSG_RUNID) ? $MSG_RUNID : 'RunID'?></th>
                        <th><?php echo isset($MSG_USER) ? $MSG_USER : 'User'?></th>
                        <th><?php echo isset($MSG_PROBLEM) ? $MSG_PROBLEM : 'Problem'?></th>
                        <th><?php echo isset($MSG_RESULT) ? $MSG_RESULT : 'Result'?></th>
                        <th><?php echo isset($MSG_LANG) ? $MSG_LANG : 'Lang'?></th>
                        <th><?php echo isset($MSG_TIME) ? $MSG_TIME : 'Time'?></th>
                        <th><?php echo isset($MSG_MEMORY) ? $MSG_MEMORY : 'Memory'?></th>
                        <th><?php echo isset($MSG_CODE_LENGTH) ? $MSG_CODE_LENGTH : 'Code'?></th>
                        <th><?php echo isset($MSG_SUBMIT_TIME) ? $MSG_SUBMIT_TIME : 'Time'?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(isset($view_status) && is_array($view_status)){
                    foreach($view_status as $row){
                    ?>
                    <tr>
                        <td><?php echo isset($row[0]) ? $row[0] : ''?></td>
                        <td><?php echo isset($row[1]) ? $row[1] : ''?></td>
                        <td><?php echo isset($row[2]) ? $row[2] : ''?></td>
                        <td><?php echo isset($row[3]) ? $row[3] : ''?></td>
                        <td><?php echo (isset($row[4]) && isset($language_name[$row[4]])) ? $language_name[$row[4]] : ''?></td>
                        <td><?php echo isset($row[5]) ? $row[5] : ''?> ms</td>
                        <td><?php echo isset($row[6]) ? $row[6] : ''?> KB</td>
                        <td><?php echo isset($row[7]) ? $row[7] : ''?></td>
                        <td><?php echo isset($row[8]) ? substr($row[8],5,11) : ''?></td>
                    </tr>
                    <?php }} ?>
                </tbody>
            </table>
        </div>

        <?php if(isset($pages) && $pages>1){ ?>
        <nav aria-label="Status pagination">
            <ul class="pagination justify-content-center mb-0 flex-wrap">
                <li class="page-item"><a class="page-link" href="status.php?page=1&<?php echo isset($str_page) ? $str_page : ''?>">&laquo;&laquo;</a></li>
                <li class="page-item"><a class="page-link" href="status.php?page=<?php echo (isset($page) && $page>1) ? $page-1 : 1?>&<?php echo isset($str_page) ? $str_page : ''?>">&laquo;</a></li>
                <?php for($i=isset($spage)?$spage:1; $i<=(isset($epage)?$epage:1); $i++){ ?>
                <li class="page-item <?php if(isset($page) && $page==$i) echo 'active'; ?>"><a class="page-link" href="status.php?page=<?php echo $i?>&<?php echo isset($str_page) ? $str_page : ''?>"><?php echo $i?></a></li>
                <?php } ?>
                <li class="page-item"><a class="page-link" href="status.php?page=<?php echo isset($pages)?$pages:1?>&<?php echo isset($str_page) ? $str_page : ''?>">&raquo;&raquo;</a></li>
            </ul>
        </nav>
        <?php } ?>
    </div>
</div>

<script>
$(document).ready(function(){
    <?php if(isset($OJ_AUTO_REFRESH) && $OJ_AUTO_REFRESH){ ?>
    // Show refresh spinner briefly
    $("table").after('<div id="refreshHint" class="text-center text-body-secondary py-2"><span class="spinner-border spinner-border-sm me-1"></span>Auto-refresh in 30s...</div>');
    setTimeout(function(){
        $("#refreshHint").remove();
        location.reload();
    }, 30000);
    <?php } ?>
});
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
