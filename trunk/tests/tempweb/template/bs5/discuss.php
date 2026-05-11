<?php $show_title=isset($MSG_BBS) ? "$MSG_BBS - $OJ_NAME" : "BBS - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-chat-left-text me-2"></i><?php echo isset($MSG_BBS) ? $MSG_BBS : 'Discussion'?></h4>
        <?php if(isset($_SESSION[$OJ_NAME.'_user_id'])){ ?>
        <a class="btn btn-primary btn-sm" href="discuss.php?action=post">
            <i class="bi bi-pen"></i> <?php echo isset($MSG_NEW_THREAD) ? $MSG_NEW_THREAD : 'New Thread'?>
        </a>
        <?php } ?>
    </div>
    <div class="card-body p-0">
        <?php if(isset($view_discuss) && is_array($view_discuss) && count($view_discuss) > 0){ ?>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th><?php echo isset($MSG_TITLE) ? $MSG_TITLE : 'Title'?></th>
                        <th class="d-none d-md-table-cell"><?php echo isset($MSG_AUTHOR) ? $MSG_AUTHOR : 'Author'?></th>
                        <th class="d-none d-md-table-cell text-center"><?php echo isset($MSG_REPLY) ? $MSG_REPLY : 'Replies'?></th>
                        <th class="d-none d-md-table-cell"><?php echo isset($MSG_TIME) ? $MSG_TIME : 'Time'?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($view_discuss as $row){ ?>
                    <tr>
                        <td>
                            <a href="discuss.php?action=view&id=<?php echo $row['thread_id'] ?? ''?>" class="text-decoration-none">
                                <?php echo htmlentities($row['title'] ?? '', ENT_QUOTES, 'utf-8')?>
                            </a>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <a href="userinfo.php?user=<?php echo urlencode($row['author_id'] ?? '')?>">
                                <?php echo htmlentities($row['author_id'] ?? '', ENT_QUOTES, 'utf-8')?>
                            </a>
                        </td>
                        <td class="d-none d-md-table-cell text-center">
                            <span class="badge bg-secondary"><?php echo $row['reply_count'] ?? 0?></span>
                        </td>
                        <td class="d-none d-md-table-cell"><small class="text-body-secondary"><?php echo isset($row['post_time']) ? substr($row['post_time'], 5, 11) : ''?></small></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
        <!-- Empty state with placeholder -->
        <div class="p-5 text-center">
            <div class="bg-light rounded p-4">
                <i class="bi bi-chat-left-text" style="font-size:3rem;color:#ccc"></i>
                <p class="text-body-secondary mt-2 mb-0">No discussions yet. Be the first to start one!</p>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<?php if(isset($pages) && $pages > 1){ ?>
<nav aria-label="Discuss pagination">
    <ul class="pagination justify-content-center flex-wrap">
        <li class="page-item"><a class="page-link" href="discuss.php?page=1">&laquo;&laquo;</a></li>
        <li class="page-item"><a class="page-link" href="discuss.php?page=<?php echo (isset($page) && $page > 1) ? $page-1 : 1?>">&laquo;</a></li>
        <?php for($i = isset($spage) ? $spage : 1; $i <= (isset($epage) ? $epage : 1); $i++){ ?>
        <li class="page-item <?php if(isset($page) && $page == $i) echo 'active'?>"><a class="page-link" href="discuss.php?page=<?php echo $i?>"><?php echo $i?></a></li>
        <?php } ?>
        <li class="page-item"><a class="page-link" href="discuss.php?page=<?php echo isset($pages) ? $pages : 1?>">&raquo;&raquo;</a></li>
    </ul>
</nav>
<?php } ?>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
