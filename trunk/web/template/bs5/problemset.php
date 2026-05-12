<?php $show_title="$MSG_PROBLEMSET - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<?php if(!isset($_GET['ajax'])){ ?>
<div class="card">
    <div class="card-header">
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item"><a class="page-link" href="problemset.php?page=1">&laquo;&laquo;</a></li>
                <?php
                if ( !isset( $page ) )$page = 1;
                $page = intval( $page );
                $section = 8;
                $start = $page > $section ? $page - $section : 1;
                $end = $page + $section > $view_total_page ? $view_total_page : $page + $section;
                for ( $i = $start; $i <= $end; $i++ ) {
                    echo "<li class='" . ( $page == $i ? "active " : "" ) . "page-item'><a class='page-link' href='problemset.php?page=" . $i . htmlentities($postfix,ENT_QUOTES,'UTF-8'). "'>" . $i . "</a></li>";
                }
                ?>
                <li class="page-item"><a class="page-link" href="problemset.php?page=<?php echo $view_total_page?>">&raquo;&raquo;</a></li>
            </ul>
        </nav>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <form class="d-flex" action="problem.php">
                    <input class="form-control me-2" type='text' name='id' placeholder="<?php echo $MSG_PROBLEM_ID?>">
                    <button class="btn btn-outline-primary" type='submit'><i class="bi bi-search"></i></button>
                </form>
            </div>
            <div class="col-md-6">
                <form class="d-flex" action="problem.php">
                    <input class="form-control me-2" type="text" name="search" placeholder="<?php echo $MSG_TITLE.', '.$MSG_SOURCE?>">
                    <button class="btn btn-outline-primary" type='submit'><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table id='problemset' class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th></th>
                        <th class="d-none d-md-table-cell text-center"><?php echo $MSG_PROBLEM_ID?></th>
                        <th class="text-center"><?php echo $MSG_TITLE?></th>
                        <th class="d-none d-md-table-cell text-center"><?php echo $MSG_SOURCE?></th>
                        <th class="text-center"><?php echo $MSG_SOVLED?></th>
                        <th class="text-center"><?php echo $MSG_SUBMIT?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cnt = 0;
                    foreach ( $view_problemset as $row ) {
                        $class = $cnt ? 'oddrow' : 'evenrow';
                        $cnt = 1 - $cnt;
                    ?>
                    <tr class="<?php echo $class ?>">
                        <td></td>
                        <td class="d-none d-md-table-cell text-center"><?php echo $row[1]?></td>
                        <td><?php echo $row[2]?></td>
                        <td class="d-none d-md-table-cell"><?php echo $row[3]?></td>
                        <td class="text-center"><?php echo $row[4]?></td>
                        <td class="text-center"><?php echo $row[5]?></td>
                    </tr>
                    <?php } ?>
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
                    <td class="d-none d-md-table-cell text-center"><?php echo $MSG_PROBLEM_ID?></td>
                    <td class="text-center"><?php echo $MSG_TITLE?></td>
                    <td class="d-none d-md-table-cell text-center"><?php echo $MSG_SOURCE?></td>
                    <td class="text-center"><?php echo $MSG_SOVLED?></td>
                    <td class="text-center"><?php echo $MSG_SUBMIT?></td>
                </tr>
            </thead>
            <tbody>
                <?php
                $cnt = 0;
                foreach ( $view_problemset as $row ) {
                    $class = $cnt ? 'oddrow' : 'evenrow';
                    $cnt = 1 - $cnt;
                ?>
                <tr class="<?php echo $class ?>">
                    <td></td>
                    <td class="d-none d-md-table-cell text-center"><?php echo $row[1]?></td>
                    <td><?php echo $row[2]?></td>
                    <td class="d-none d-md-table-cell"><?php echo $row[3]?></td>
                    <td class="text-center"><?php echo $row[4]?></td>
                    <td class="text-center"><?php echo $row[5]?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
<?php } ?>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
