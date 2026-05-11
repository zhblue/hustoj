<?php $show_title="$MSG_MAIL - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="bi bi-envelope"></i> <?php echo $MSG_MAIL?></h4>
            <a class="btn btn-primary btn-sm" href="mail.php?action=compose"><i class="bi bi-pen"></i> <?php echo $MSG_WRITE?></a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th width="15%"><?php echo $MSG_SENDER?></th>
                    <th width="50%"><?php echo $MSG_TITLE?></th>
                    <th width="25%"><?php echo $MSG_TIME?></th>
                    <th width="10%"><?php echo $MSG_STATUS?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($view_mail as $row){
                ?>
                <tr class="<?php echo $row['new_mail']?'fw-bold':''?>">
                    <td><a href="userinfo.php?user=<?php echo htmlentities($row['from_user'],ENT_QUOTES,'utf-8')?>"><?php echo htmlentities($row['from_user'],ENT_QUOTES,'utf-8')?></a></td>
                    <td><a href="mail.php?action=view&id=<?php echo $row['mail_id']?>"><?php echo htmlentities($row['title'],ENT_QUOTES,'utf-8')?></a></td>
                    <td><?php echo $row['in_date']?></td>
                    <td><?php echo $row['new_mail']?'<span class="badge bg-danger">NEW</span>':''?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
