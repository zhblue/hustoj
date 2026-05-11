<?php    if(stripos($_SERVER['REQUEST_URI'],"template"))exit(); ?>
<!DOCTYPE html>
<html lang="cn">

<head>
    <?php include('_includes/head.php'); ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <form action="ranklist.php">
            <div class="mdui-textfield" align="center" style="width: 100%">
                <input class="mdui-textfield-input" type="text" name="prefix" placeholder="用户"
                    value="<?php echo htmlentities($_GET["prefix"], ENT_QUOTES, "UTF-8")?>"
                    style="width: calc(50% - 40px); display: inline-block;">
                <button class="mdui-btn mdui-btn-icon" type="sumbit">
                    <i class="mdui-icon material-icons" style="top: 35%; left: 40%;">search</i>
                </button>
            </div>
        </form>
        <table align=right>
            <tr>
                <td>
                    <a href=ranklist.php?scope=d>Day</a>
                    <a href=ranklist.php?scope=w>Week</a>
                    <a href=ranklist.php?scope=m>Month</a>
                    <a href=ranklist.php?scope=y>Year</a>
                    &nbsp;
                </td>
            <tr>
        </table>
        <table class="mdui-table mdui-m-y-4" width="90%" align="center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>用户</th>
                    <th>昵称</th>
                    <th>通过</th>
                    <th>提交</th>
                    <th>通过率</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($view_rank as $row){
                        echo '<tr>';
                        foreach($row as $table_cell){
                            echo '<td>'.$table_cell.'</td>';
                        }
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
<!-- <div class="mdui-btn-group">
  <button type="button" class="mdui-btn"><i class="mdui-icon material-icons">format_align_left</i></button>
  <button type="button" class="mdui-btn mdui-btn-active"><i class="mdui-icon material-icons">format_align_center</i></button>
  <button type="button" class="mdui-btn"><i class="mdui-icon material-icons">format_align_right</i></button>
  <button type="button" class="mdui-btn"><i class="mdui-icon material-icons">format_align_justify</i></button>
</div> -->
            <?php
                echo '<div class="mdui-btn-group mdui-float-right">';

                $qs="";
                $nstart=isset($_GET["start"])?intval($_GET["start"]):0;
                if(isset($_GET['prefix'])){
                    $qs.="&prefix=".htmlentities($_GET['prefix'],ENT_QUOTES,"utf-8");
                }
                if(isset($scope)){
                    $qs.="&scope=".htmlentities($scope,ENT_QUOTES,"utf-8");
                }
                for($i = 0; $i <$view_total ; $i += $page_size) {
                    echo '<a class="mdui-btn'.($i == $nstart?" mdui-btn-active":"").'" href="./ranklist.php?start='.strval ( $i ).$qs. '">'
                        .strval ($i+1)."-".strval ($i+$page_size).'</a>';
                    if ($i % 2000 == 500) echo "<br>";
                }
                echo '</div>';
            ?>
    </div>
</body>

</html>
