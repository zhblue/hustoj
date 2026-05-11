<?php $show_title=isset($MSG_HELP_ONLINE) ? "$MSG_HELP_ONLINE - $OJ_NAME" : "Online Users - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-people"></i> <?php echo isset($MSG_HELP_ONLINE) ? $MSG_HELP_ONLINE : 'Online Users'?></h4>
    </div>
    <div class="card-body">
        <?php if(isset($users) && is_array($users)){ ?>
        <div class="table-responsive mb-4">
            <table class="table table-sm table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>IP</th>
                        <th>URI</th>
                        <th>Refer</th>
                        <th>Stay Time</th>
                        <th>UA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $u){ if(!empty($u)){ ?>
                    <tr>
                        <td>
                            <?php echo isset($u['ip']) ? $u['ip'] : ''?>
                            <?php // $location->getlocation($u['ip']) ?>
                        </td>
                        <td><?php echo isset($u['uri']) ? $u['uri'] : ''?></td>
                        <td><?php echo isset($u['refer']) ? $u['refer'] : ''?></td>
                        <td><?php echo isset($u['lastmove'], $u['firsttime']) ? sprintf("%dmin %dsec", ($u['lastmove']-$u['firsttime'])/60, ($u['lastmove']-$u['firsttime'])%60) : ''?></td>
                        <td><?php echo isset($u['ua']) ? $u['ua'] : ''?></td>
                    </tr>
                    <?php }} ?>
                </tbody>
            </table>
        </div>
        <?php } ?>

        <?php if(isset($view_online)) echo $view_online; ?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
