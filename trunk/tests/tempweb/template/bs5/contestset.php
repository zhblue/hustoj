<?php $show_title=isset($MSG_CONTEST) ? "$MSG_CONTEST - $OJ_NAME" : "Contest - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <ul class="nav nav-tabs mb-0" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?php echo !isset($_GET['my'])?'active':''?>" href="contest.php">
                    <i class="bi bi-collection"></i> <?php echo isset($MSG_ALL_CONTEST) ? $MSG_ALL_CONTEST : 'All Contests'?>
                </a>
            </li>
            <?php if(isset($_SESSION[$OJ_NAME.'_user_id'])){ ?>
            <li class="nav-item">
                <a class="nav-link <?php echo isset($_GET['my'])?'active':''?>" href="contest.php?my">
                    <i class="bi bi-person"></i> <?php echo isset($MSG_MY_CONTEST) ? $MSG_MY_CONTEST : 'My Contests'?>
                </a>
            </li>
            <?php } ?>
        </ul>
    </div>
    <div class="card-body p-0">
        <?php if(isset($view_contest) && is_array($view_contest) && count($view_contest) > 0){ ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th><?php echo isset($MSG_CONTEST_ID) ? $MSG_CONTEST_ID : 'ID'?></th>
                        <th><?php echo isset($MSG_CONTEST_NAME) ? $MSG_CONTEST_NAME : 'Name'?></th>
                        <th class="d-none d-md-table-cell"><?php echo isset($MSG_START_TIME) ? $MSG_START_TIME : 'Start'?></th>
                        <th class="d-none d-md-table-cell"><?php echo isset($MSG_END_TIME) ? $MSG_END_TIME : 'End'?></th>
                        <th><?php echo isset($MSG_STATUS) ? $MSG_STATUS : 'Status'?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($view_contest as $row){ ?>
                    <?php
                    // Backend sets: [0]=contest_id, [1]=title_html, [2]=status_html,
                    // [4]=public OR [5]=private, [6]=creator
                    $cid = $row[0] ?? '';
                    $title_html = $row[1] ?? '';
                    $status_html = $row[2] ?? '';
                    ?>
                    <tr>
                        <td><?php echo $cid?></td>
                        <td><?php echo $title_html?></td>
                        <td class="d-none d-md-table-cell"><small><?php echo $status_html?></small></td>
                        <td>
                            <?php
                            // public = [4], private = [5]
                            $type_html = ($row[4] ?? '') ?: ($row[5] ?? '');
                            echo $type_html;
                            ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
        <div class="p-4 text-center text-body-secondary">
            <i class="bi bi-inbox" style="font-size:2rem"></i>
            <p class="mb-0 mt-2">No contests available.</p>
        </div>
        <?php } ?>
    </div>

    <?php if(isset($pages) && $pages > 1){ ?>
    <div class="card-footer">
        <nav aria-label="Contest pagination">
            <ul class="pagination justify-content-center mb-0 flex-wrap">
                <li class="page-item"><a class="page-link" href="contest.php?page=1<?php echo isset($_GET['my'])?'&my':''?>">&laquo;&laquo;</a></li>
                <li class="page-item"><a class="page-link" href="contest.php?page=<?php echo (isset($page) && $page > 1) ? $page-1 : 1?><?php echo isset($_GET['my'])?'&my':''?>">&laquo;</a></li>
                <?php for($i = isset($spage) ? $spage : 1; $i <= (isset($epage) ? $epage : 1); $i++){ ?>
                <li class="page-item <?php if(isset($page) && $page == $i) echo 'active'?>"><a class="page-link" href="contest.php?page=<?php echo $i?><?php echo isset($_GET['my'])?'&my':''?>"><?php echo $i?></a></li>
                <?php } ?>
                <li class="page-item"><a class="page-link" href="contest.php?page=<?php echo isset($pages) ? $pages : 1?><?php echo isset($_GET['my'])?'&my':''?>">&raquo;&raquo;</a></li>
            </ul>
        </nav>
    </div>
    <?php } ?>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
