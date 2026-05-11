<?php $show_title=isset($MSG_ERROR_INFO) ? "$MSG_ERROR_INFO - $OJ_NAME" : "Balloon List - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-trophy"></i> Balloon List</h4>
    </div>
    <div class="card-body">
        <form class="row g-3 mb-3" action="balloon.php" method="get">
            <div class="col-auto">
                <label>Contest ID:</label>
                <input type="text" name="cid" class="form-control" value="<?php echo isset($cid) ? $cid : ''?>">
            </div>
            <div class="col-auto">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">Check</button>
            </div>
            <?php if(isset($cid)){ ?>
            <div class="col-auto">
                <label>&nbsp;</label>
                <a href="balloon.php?cid=<?php echo $cid?>" class="btn btn-outline-danger w-100" onclick="return confirm('Delete All Tasks?')">Clean</a>
            </div>
            <?php } ?>
        </form>

        <?php if(isset($view_balloon) && is_array($view_balloon)){ ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th><?php echo isset($MSG_USER_ID) ? $MSG_USER_ID : 'User ID'?></th>
                        <th><?php echo isset($MSG_COLOR) ? $MSG_COLOR : 'Color'?></th>
                        <th><?php echo isset($MSG_STATUS) ? $MSG_STATUS : 'Status'?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($view_balloon as $row){ ?>
                    <tr>
                        <?php foreach($row as $cell){ ?>
                        <td><?php echo $cell?></td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } ?>
    </div>
</div>

<script>
$(document).ready(function(){
    $("td:contains('<?php echo isset($view_user) ? $view_user : ''?>')").css("background-color", "<?php echo isset($ball_color) && isset($view_pid) ? ($ball_color[$view_pid] ?? '') : ''?>");
});
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
