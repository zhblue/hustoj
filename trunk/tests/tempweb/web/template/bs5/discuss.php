<?php $show_title="$MSG_BBS - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="bi bi-chat-left-text"></i> <?php echo $MSG_BBS?></h4>
            <?php if(isset($_SESSION[$OJ_NAME.'_user_id'])){ ?>
            <a class="btn btn-primary btn-sm" href="discuss.php?action=post"><i class="bi bi-pen"></i> <?php echo $MSG_NEW_THREAD?></a>
            <?php } ?>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th><?php echo $MSG_TITLE?></th>
                    <th class="d-none d-md-table-cell"><?php echo $MSG_AUTHOR?></th>
                    <th class="d-none d-md-table-cell"><?php echo $MSG_REPLY?></th>
                    <th class="d-none d-md-table-cell"><?php echo $MSG_TIME?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(isset($view_discuss)){
                    foreach($view_discuss as $row){
                    ?>
                    <tr>
                        <td><a href="discuss.php?action=view&id=<?php echo $row['thread_id']?>"><?php echo htmlentities($row['title'],ENT_QUOTES,'utf-8')?></a></td>
                        <td class="d-none d-md-table-cell"><a href="userinfo.php?user=<?php echo htmlentities($row['author_id'],ENT_QUOTES,'utf-8')?>"><?php echo htmlentities($row['author_id'],ENT_QUOTES,'utf-8')?></a></td>
                        <td class="d-none d-md-table-cell"><?php echo $row['reply_count']?></td>
                        <td class="d-none d-md-table-cell"><?php echo substr($row['post_time'],5,11)?></td>
                    </tr>
                    <?php }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php if(isset($pages) && $pages>1){ ?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <li class="page-item"><a class="page-link" href="discuss.php?page=1">&laquo;&laquo;</a></li>
        <li class="page-item"><a class="page-link" href="discuss.php?page=<?php echo $page==1?1:$page-1?>">&laquo;</a></li>
        <?php for($i=$spage; $i<=$epage; $i++){ ?>
        <li class="page-item <?php if($page==$i) echo 'active'; ?>"><a class="page-link" href="discuss.php?page=<?php echo $i?>"><?php echo $i?></a></li>
        <?php } ?>
        <li class="page-item"><a class="page-link" href="discuss.php?page=<?php echo $page==$pages?$pages:$page+1?>">&raquo;</a></li>
        <li class="page-item"><a class="page-link" href="discuss.php?page=<?php echo $pages?>">&raquo;&raquo;</a></li>
    </ul>
</nav>
<?php } ?>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
