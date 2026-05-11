<!DOCTYPE html>
<html lang="cn">

<head>
    <?php $page_title = "竟赛/作业列表"; ?>
    <?php include('_includes/head.php'); ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <form method="post" action="contest.php">
            <div class="mdui-textfield" align="center" style="width: 100%">
                <input class="mdui-textfield-input" type="text" name="keyword" placeholder="竟赛/作业名称"
                    value="<?php echo htmlentities($_POST['keyword'], ENT_QUOTES, "UTF-8")?>"
                    style="width: calc(70% - 40px); display: inline-block;">
                <button class="mdui-btn mdui-btn-icon" type="sumbit">
                    <i class="mdui-icon material-icons" style="top: 35%; left: 40%;">search</i>
                </button>
            </div>
        </form>
        <div class="mdui-chip mdui-m-y-4 mdui-float-right" align="center">
            <span class="mdui-chip-icon"><i class="mdui-icon material-icons">access_time</i></span>
            <span class="mdui-chip-title">系统时间：<span id="nowdate" style="font-weight: 700;"></span></span>
        </div>

        <table class="mdui-table mdui-m-b-2" width="90%">
            <thead>
                <tr>
                    <td>编号</td>
                    <td>名称</td>
                    <td>状态</td>
                    <td>是否开放</td>
                    <td>创建人</td>
                </tr>
            </thead>
            <tbody>
                <?php
                    $cnt=0;
                    foreach($view_contest as $row){
                        if ($cnt)
                        echo "<tr class='oddrow'>";
                        else
                        echo "<tr class='evenrow'>";
                        $i=0;
                        foreach($row as $table_cell){
                        if($i==2) echo "<td class=text-left>";
                        else echo "<td>";
                        echo "\t".$table_cell;
                        echo "</td>";
                        $i++;
                        }
                        echo "</tr>";
                        $cnt=1-$cnt;
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
                <a class="mdui-btn" href="contest.php?page=<?php echo $page-1; ?>">
                    <i class="mdui-icon material-icons">chevron_left</i>
                </a>
            <?php } ?>
            <?php if ($start > 1) { ?>
                <a class="mdui-btn" href="contest.php?page=1"></a>
                <button class="mdui-btn" disabled>...</button>
            <?php } ?>
            <?php
                for ( $i = $start; $i <= $end; $i++ ) {
                    echo '<a class="mdui-btn '.($page == $i ? "mdui-btn-active" : "").'" href="contest.php?page='.$i.'">'.$i.'</a>';
                }
            ?>
            <?php if ($end < $view_total_page) { ?>
                <button class="mdui-btn" disabled>...</button>
                <a class="mdui-btn" href="contest.php?page=<?php echo $view_total_page; ?>"></a>
            <?php } ?>
            <?php if($page == $view_total_page) { ?>
                <button class="mdui-btn" disabled>
                    <i class="mdui-icon material-icons">chevron_right</i>
                </button>
            <?php } else { ?>
                <a class="mdui-btn" href="contest.php?page=<?php echo $page+1; ?>">
                    <i class="mdui-icon material-icons">chevron_right</i>
                </a>
            <?php } ?>
        </div>
    </div>
    <script>
    var diff = new Date("<?php echo date("Y/m/d H:i:s")?>").getTime() - new Date().getTime();
    function clock() {
        var x, h, m, s, n, xingqi, y, mon, d;
        var x = new Date(new Date().getTime() + diff);
        y = x.getYear() + 1900;
        if (y > 3000) y -= 1900;
        mon = x.getMonth() + 1;
        d = x.getDate();
        xingqi = x.getDay();
        h = x.getHours();
        m = x.getMinutes();
        s = x.getSeconds();
        n = y + "-" + (mon >= 10 ? mon : "0" + mon) + "-" + (d >= 10 ? d : "0" + d) + " " + (h >= 10 ? h : "0" + h) +
            ":" + (m >= 10 ? m : "0" + m) + ":" + (s >= 10 ? s : "0" + s);
        //alert(n);
        document.getElementById('nowdate').innerHTML = n;
        setTimeout("clock()", 1000);
    }

    clock();
    </script>
</body>

</html>