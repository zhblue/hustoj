<!DOCTYPE html>
<html lang="cn">
<head>
    <?php include('_includes/head.php') ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <!-- <div class="mdui-btn-group" style="width: 100% !important;">
            <?php
                if (!isset($page)) $page = 1;
                $page = intval($page);
                $section = 10;
                $start = $page > $section ? $page - $section : 1;
                $end = $page + $section > $view_total_page ? $view_total_page : $page + $section;
            ?>
            <?php if($page == 1) { ?>
                <button class="mdui-btn" disabled>
                        <i class="mdui-icon material-icons">chevron_left</i>
                </button>
            <?php } else { ?>
                <a class="mdui-btn" href="problemset.php?page=<?php echo $page-1; ?>">
                    <i class="mdui-icon material-icons">chevron_left</i>
                </a>
            <?php } ?>
            <?php if ($start > 1) { ?>
                <a class="mdui-btn" href="problemset.php?page=1"></a>
                <button class="mdui-btn" disabled>...</button>
            <?php } ?>
            <?php
                for ( $i = $start; $i <= $end; $i++ ) {
                    echo '<a class="mdui-btn '.($page == $i ? "mdui-btn-active" : "").'" href="problemset.php?page='.$i.'">'.$i.'</a>';
                }
            ?>
            <?php if ($end < $view_total_page) { ?>
                <button class="mdui-btn" disabled>...</button>
                <a class="mdui-btn" href="problemset.php?page=<?php echo $view_total_page; ?>"></a>
            <?php } ?>
            <?php if($page == $view_total_page) { ?>
                <button class="mdui-btn" disabled>
                        <i class="mdui-icon material-icons">chevron_right</i>
                </button>
            <?php } else { ?>
                <a class="mdui-btn" href="problemset.php?page=<?php echo $page+1; ?>">
                    <i class="mdui-icon material-icons">chevron_right</i>
                </a>
            <?php } ?>
        </div> -->
        <!-- Search -->
        <div class="mdui-row-xs-1 mdui-row-sm-2">
            <form action="problem.php">
                <div class="mdui-textfield mdui-textfield-floating-label mdui-col">
                    <label class="mdui-textfield-label">题目跳转</label>
                    <input class="mdui-textfield-input" name="id" style="width: calc(100% - 40px); display: inline-block;" type="text">
                    <button class="mdui-btn mdui-btn-icon" type="sumbit">
                        <i class="mdui-icon material-icons" style="top: 35%; left: 40%;">send</i>
                    </button>
                </div>
            </form>
            <form>
                <div class="mdui-textfield mdui-textfield-floating-label mdui-col">
                    <label class="mdui-textfield-label">题目搜索</label>
                    <input class="mdui-textfield-input" name="search" type="text"
                        style="width: calc(100% - 40px); display: inline-block;"
                        value="<?php echo $_GET["search"] ?>">
                    <button class="mdui-btn mdui-btn-icon" type="sumbit">
                        <i class="mdui-icon material-icons" style="top: 35%; left: 40%;">search</i>
                    </button>
                </div>
            </form>
        </div>
        <table id="problemset" width="90%" class="mdui-table mdui-table-hoverable mdui-m-y-4">
            <thead>
                <tr>
                    <?php if (isset($_SESSION[$OJ_NAME.'_'.'user_id'])){?>
                          <th>状态</th>
                    <?php } ?>
                    <th>编号</th>
                    <th>标题</th>
                    <th>分类</th>
                    <th>通过</th>
                    <th>提交</th>
                    <th>通过率</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $cnt = 0;
                foreach ( $result as $row ) {
                    echo "<tr>";
                    if(isset($_SESSION[$OJ_NAME.'_'.'user_id'])) {
                        if (isset($sub_arr[$row['problem_id']])) {
                            echo '<td style="color: green;">AC</td>';
                        }
                        else {
                            echo '<td></td>';
                        }
                    }
                    echo '<td>'.$row["problem_id"].'</td>';
                    echo '<td><a class="mdui-text-color-theme-accent" href="problem.php?id='.$row["problem_id"].'">'.$row["title"].'</a></td>';
                    echo '<td><a class="mdui-text-color-theme-accent" href="problemset.php?search='.urlencode($row["source"]).'">'.$row["source"].'</td>';
                    echo '<td><a class="mdui-text-color-theme-accent" href="status.php?problem_id='.$row["problem_id"].'&jresult=4">'.$row["accepted"].'</td>';
                    echo '<td><a class="mdui-text-color-theme-accent" href="status.php?problem_id='.$row["problem_id"].'">'.$row["submit"].'</td>';
                    echo '<td>'.sprintf("%.02lf%%", 100 * $row['accepted'] / ($row['submit'] || 1)).'</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
        <div class="mdui-btn-group mdui-float-right">
            <?php
                if (!isset($page)) $page = 1;
                $page = intval($page);
                $section = 10;
                $start = $page > $section ? $page - $section : 1;
                $end = $page + $section > $view_total_page ? $view_total_page : $page + $section;
            ?>
            <?php if($page == 1) { ?>
                <button class="mdui-btn" disabled>
                        <i class="mdui-icon material-icons">chevron_left</i>
                </button>
            <?php } else { ?>
                <a class="mdui-btn" href="problemset.php?page=<?php echo $page-1; ?>">
                    <i class="mdui-icon material-icons">chevron_left</i>
                </a>
            <?php } ?>
            <?php if ($start > 1) { ?>
                <a class="mdui-btn" href="problemset.php?page=1"></a>
                <button class="mdui-btn" disabled>...</button>
            <?php } ?>
            <?php
                for ( $i = $start; $i <= $end; $i++ ) {
                    echo '<a class="mdui-btn '.($page == $i ? "mdui-btn-active" : "").'" href="problemset.php?page='.$i.'">'.$i.'</a>';
                }
            ?>
            <?php if ($end < $view_total_page) { ?>
                <button class="mdui-btn" disabled>...</button>
                <a class="mdui-btn" href="problemset.php?page=<?php echo $view_total_page; ?>"></a>
            <?php } ?>
            <?php if($page == $view_total_page) { ?>
                <button class="mdui-btn" disabled>
                        <i class="mdui-icon material-icons">chevron_right</i>
                </button>
            <?php } else { ?>
                <a class="mdui-btn" href="problemset.php?page=<?php echo $page+1; ?>">
                    <i class="mdui-icon material-icons">chevron_right</i>
                </a>
            <?php } ?>
        </div>
    </div>
</body>
</html>
