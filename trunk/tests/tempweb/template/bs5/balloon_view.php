<?php $show_title=isset($MSG_ERROR_INFO) ? "$MSG_ERROR_INFO - $OJ_NAME" : "Balloon - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header bg-success text-white">
        <h4 class="mb-0"><i class="bi bi-ticket"></i> Balloon Ticket</h4>
    </div>
    <div class="card-body text-center">
        <h2><?php echo isset($view_user) ? htmlentities($view_user, ENT_QUOTES, 'utf-8') : ''?></h2>
        <p class="h5 text-body-secondary">
            <?php echo isset($view_school) ? htmlentities($view_school, ENT_QUOTES, 'utf-8') : ''?> -
            <?php echo isset($view_nick) ? htmlentities($view_nick, ENT_QUOTES, 'utf-8') : ''?>
        </p>
        <p class="h4">
            Problem <?php echo isset($PID) && isset($view_pid) ? ($PID[$view_pid] ?? $view_pid) : (isset($view_pid) ? $view_pid : '')?>
        </p>
        <?php
        $fb = isset($_GET['fb']) && intval($_GET['fb']) == 1;
        $color = isset($ball_color, $view_pid) ? ($ball_color[$view_pid] ?? '') : '';
        $name = isset($ball_name, $view_pid) ? ($ball_name[$view_pid] ?? '') : '';
        ?>
        <p class="h5">
            Balloon Color:
            <span style="color:<?php echo $color?>"><?php echo $name?><?php echo $fb ? ' First Blood!' : ''?></span>
        </p>

        <div class="mt-3">
            <button class="btn btn-primary me-2" onclick="window.print();">
                <i class="bi bi-printer"></i> <?php echo isset($MSG_PRINTER) ? $MSG_PRINTER : 'Print'?>
            </button>
            <button class="btn btn-outline-secondary" onclick="location.href='balloon.php?id=<?php echo isset($id) ? $id : ''?>&cid=<?php echo isset($cid) ? $cid : ''?>';">
                <i class="bi bi-check-circle"></i> <?php echo isset($MSG_PRINT_DONE) ? $MSG_PRINT_DONE : 'Done'?>
            </button>
        </div>

        <?php if(isset($view_map)) echo $view_map; ?>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
