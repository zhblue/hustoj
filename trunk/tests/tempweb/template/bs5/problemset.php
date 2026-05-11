<?php $show_title=isset($MSG_PROBLEMS) ? "$MSG_PROBLEMS - $OJ_NAME" : "Problem Set - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<?php if(!isset($_GET['ajax'])){ ?>
<div class="card">
    <div class="card-header">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item"><a class="page-link" href="problemset.php?page=1<?php echo isset($postfix)?htmlentities($postfix,ENT_QUOTES,'UTF-8'):''?>">&laquo;&laquo;</a></li>
                <?php
                if ( !isset( $page ) )$page = 1;
                $page = intval( $page );
                $section = 8;
                $start = $page > $section ? $page - $section : 1;
                $end = $page + $section > (isset($view_total_page)?$view_total_page:1) ? (isset($view_total_page)?$view_total_page:1) : $page + $section;
                for ( $i = $start; $i <= $end; $i++ ) {
                    echo "<li class='" . ( $page == $i ? "active " : "" ) . "page-item'><a class='page-link' href='problemset.php?page=" . $i . (isset($postfix)?htmlentities($postfix,ENT_QUOTES,'UTF-8'):'') . "'>" . $i . "</a></li>";
                }
                ?>
                <li class="page-item"><a class="page-link" href="problemset.php?page=<?php echo isset($view_total_page)?$view_total_page:1?>">&raquo;&raquo;</a></li>
            </ul>
        </nav>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <form class="d-flex" action="problem.php">
                    <input class="form-control me-2" type='text' name='id' placeholder="<?php echo isset($MSG_PROBLEM_ID)?$MSG_PROBLEM_ID:'Problem ID'?>">
                    <button class="btn btn-outline-primary" type='submit'><i class="bi bi-search"></i></button>
                </form>
            </div>
            <div class="col-md-6">
                <form class="d-flex" action="problem.php">
                    <input class="form-control me-2" type="text" name="search" placeholder="<?php echo (isset($MSG_TITLE)?$MSG_TITLE:'Title').', '.(isset($MSG_SOURCE)?$MSG_SOURCE:'Source')?>">
                    <button class="btn btn-outline-primary" type='submit'><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table id='problemset' class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th></th>
                        <th class="d-none d-md-table-cell text-center"><?php echo isset($MSG_PROBLEM_ID)?$MSG_PROBLEM_ID:'Problem ID'?></th>
                        <th class="text-center"><?php echo isset($MSG_TITLE)?$MSG_TITLE:'Title'?></th>
                        <th class="d-none d-md-table-cell text-center"><?php echo isset($MSG_SOURCE)?$MSG_SOURCE:'Source'?></th>
                        <th class="text-center"><?php echo isset($MSG_SOVLED)?$MSG_SOVLED:'Solved'?></th>
                        <th class="text-center"><?php echo isset($MSG_SUBMIT)?$MSG_SUBMIT:'Submit'?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(isset($view_problemset) && is_array($view_problemset)){
                    $cnt = 0;
                    foreach ( $view_problemset as $row ) {
                        $class = '';
                        $cnt = 1 - $cnt;
                    ?>
                    <tr class="<?php echo $class ?>">
                        <td><?php echo isset($row[0]) ? $row[0] : ''?></td>
                        <td class="d-none d-md-table-cell text-center"><?php echo isset($row[1]) ? $row[1] : ''?></td>
                        <td><?php echo isset($row[2]) ? $row[2] : ''?></td>
                        <td class="d-none d-md-table-cell"><?php echo isset($row[3]) ? $row[3] : ''?></td>
                        <td class="text-center"><?php echo isset($row[4]) ? $row[4] : ''?></td>
                        <td class="text-center"><?php echo isset($row[5]) ? $row[5] : ''?></td>
                    </tr>
                    <?php }} ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php } else { ?>
        <table id='problemset' class="table table-striped">
            <thead>
                <tr>
                    <td></td>
                    <td class="d-none d-md-table-cell text-center"><?php echo isset($MSG_PROBLEM_ID)?$MSG_PROBLEM_ID:'Problem ID'?></td>
                    <td class="text-center"><?php echo isset($MSG_TITLE)?$MSG_TITLE:'Title'?></td>
                    <td class="d-none d-md-table-cell text-center"><?php echo isset($MSG_SOURCE)?$MSG_SOURCE:'Source'?></td>
                    <td class="text-center"><?php echo isset($MSG_SOVLED)?$MSG_SOVLED:'Solved'?></td>
                    <td class="text-center"><?php echo isset($MSG_SUBMIT)?$MSG_SUBMIT:'Submit'?></td>
                </tr>
            </thead>
            <tbody>
                <?php
                if(isset($view_problemset) && is_array($view_problemset)){
                $cnt = 0;
                foreach ( $view_problemset as $row ) {
                    $class = $cnt ? 'oddrow' : 'evenrow';
                    $cnt = 1 - $cnt;
                ?>
                <tr class="<?php echo $class ?>">
                    <td><?php echo isset($row[0]) ? $row[0] : ''?></td>
                    <td class="d-none d-md-table-cell text-center"><?php echo isset($row[1]) ? $row[1] : ''?></td>
                    <td><?php echo isset($row[2]) ? $row[2] : ''?></td>
                    <td class="d-none d-md-table-cell"><?php echo isset($row[3]) ? $row[3] : ''?></td>
                    <td class="text-center"><?php echo isset($row[4]) ? $row[4] : ''?></td>
                    <td class="text-center"><?php echo isset($row[5]) ? $row[5] : ''?></td>
                </tr>
                <?php }} ?>
            </tbody>
        </table>
<?php } ?>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
