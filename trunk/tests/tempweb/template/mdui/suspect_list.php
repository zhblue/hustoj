<!DOCTYPE html>
<html lang="cn">

<head>
    <?php $page_title = 'IP验证'; ?>
    <?php include('_includes/head.php'); ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <div class="jumbotron">
        <?php if (isset($_GET['cid'])) {?>
            <div class="mdui-card" style="text-align: center;">
                <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title"><?php echo $view_title ?></div>
                    <div class="mdui-card-primary-subtitle">ID: <?php echo $view_cid?></div>
                </div>
                <div class="mdui-card-content"><?php echo $view_description?></div>
                <div class="mdui-chip mdui-m-y-1" align="center">
                    <span class="mdui-chip-title">系统时间：<span id="nowdate" style="font-weight: 700;"></span></span>
                </div>
                <br>

                <?php if (isset($OJ_RANK_LOCK_PERCENT)&&$OJ_RANK_LOCK_PERCENT!=0) { ?>
                    Lock Board Time: <?php echo date("Y-m-d H:i:s", $view_lock_time) ?><br />
                <?php } ?>

                <div class="mdui-chip">
                    <span class="mdui-chip-icon"><i class="mdui-icon material-icons">face</i></span>
                    <span class="mdui-chip-title">
                        <span>状态：</span>
                        <?php if ($now > $end_time) { ?>
                            <b class="">已结束</b>
                        <?php } else if ($now < $start_time) { ?>
                            <b class="">未开始</b>
                        <?php } else { ?>
                            <b class="">进行中</b>
                        <?php } ?>
                    </span>
                </div>

                <div class="mdui-chip">
                    <span class="mdui-chip-icon"><i class="mdui-icon material-icons">remove_red_eye</i></span>
                    <span class="mdui-chip-title">
                        <?php if ($view_private=='0') { ?>
                            <b class="">公开</b>
                        <?php } else { ?>
                            <b class="">私有</b>
                        <?php } ?>
                    </span>
                </div>
                
                <div class="mdui-chip">
                    <span class="mdui-chip-icon"><i class="mdui-icon material-icons">access_time</i></span>
                    <span class="mdui-chip-title">
                        <span>开始时间：</span>
                        <b><?php echo $view_start_time?></b>
                    </span>
                </div>
                <div class="mdui-chip">
                    <span class="mdui-chip-icon"><i class="mdui-icon material-icons">access_time</i></span>
                    <span class="mdui-chip-title">
                        <span>结束时间：</span>
                        <b><?php echo $view_end_time?></b>
                    </span>
                </div>

                <div class="mdui-card-actions mdui-m-y-2">
                    <a href="contest.php?cid=<?php echo $cid?>"
                        class="mdui-btn mdui-ripple mdui-color-blue-600">问题</a>
                    <a href="status.php?cid=<?php echo $view_cid?>"
                        class="mdui-btn mdui-ripple mdui-color-blue-600">提交</a>
                    <a href="contestrank.php?cid=<?php echo $view_cid?>"
                        class="mdui-btn mdui-ripple mdui-color-blue-600">排名</a>
                    <a href="contestrank-oi.php?cid=<?php echo $view_cid?>"
                        class="mdui-btn mdui-ripple mdui-color-blue-600">OI排名</a>
                    <a href="conteststatistics.php?cid=<?php echo $view_cid?>"
                        class="mdui-btn mdui-ripple mdui-color-blue-600">统计</a>
                    <a href="suspect_list.php?cid=<?php echo $view_cid?>"
                        class="mdui-btn mdui-ripple mdui-color-purple">IP验证</a>
                    <?php if(  isset($_SESSION[$OJ_NAME.'_'.'administrator'])
                            || isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])) { ?>
                        <a href="user_set_ip.php?cid=<?php echo $view_cid?>"
                            class="mdui-btn mdui-ripple mdui-color-red">指定登录IP</a>
                        <a target="_blank" href="../../admin/contest_edit.php?cid=<?php echo $view_cid?>"
                            class="mdui-btn mdui-ripple mdui-color-red">编辑</a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

            <div>
                <center>
                    <?php echo $MSG_CONTEST_SUSPECT1?><br><br>
                    <table class="table-hover table-striped text-center" align=center width=90% border=0>
                        <tr>
                            <td>IP address</td>
                            <td>Used ID</td>
                            <td>Trace</td>
                            <td>Time</td>
                            <td>IP address count</td>
                        </tr>

                        <?php
                            foreach ($result1 as $row) {
                                echo "<tr>";
                                echo "<td>".$row['ip']."</td>";
                                echo "<td>".$row['user_id']."</td>";
                                echo "<td>";
                                    echo "<a href='../userinfo.php?user=".$row['user_id']."'><sub>".$MSG_USERINFO."</sub></a> <sub>/</sub> ";
                                    echo "<a href='../status.php?cid=$contest_id&user_id=".$row['user_id']."'><sub>".$MSG_CONTEST." ".$MSG_SUBMIT."</sub></a>";
                                echo "</td>";
                                echo "<td>".$row['in_date'];
                                echo "<td>".$row['c']."</td>";
                                echo "</tr>";
                            }
                        ?>

                    </table>
                </center>
            </div>

            <br><br>

            <div>
                <center>
                    <?php echo $MSG_CONTEST_SUSPECT2?><br><br>
                    <table class="table-hover table-striped text-center" align=center width=90% border=0>
                        <tr>
                            <td>Used ID</td>
                            <td>Trace</td>
                            <td>Used IP address</td>
                            <td>Time</td>
                            <td>IP address count</td>
                        </tr>

                        <?php
          foreach ($result2 as $row) {
            echo "<tr>";
              echo "<td>".$row['user_id']."</td>";
              echo "<td>";
                echo "<a href='../userinfo.php?user=".$row['user_id']."'><sub>".$MSG_USERINFO."</sub></a> <sub>/</sub> ";
                echo "<a href='../status.php?cid=$contest_id&user_id=".$row['user_id']."'><sub>".$MSG_CONTEST." ".$MSG_SUBMIT."</sub></a>";
              echo "</td>";
              echo "<td>".$row['ip'];
              echo "<td>".$row['time'];
              echo "<td>".$row['c'];
              echo "</tr>";
          }
          ?>
                    </table>
                </center>
            </div>

        </div>

    </div>

    <!-- /container -->
    <!-- Bootstrap core JavaScript
================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>

    <script src="include/sortTable.js"></script>

    <script>
    var diff = new Date("<?php echo date("Y/m/d H:i:s")?>").getTime() - new Date().getTime();
    //alert(diff);
    function clock() {
        var x, h, m, s, n, xingqi, y, mon, d;
        var x = new Date(new Date().getTime() + diff);
        y = x.getYear() + 1900;

        if (y > 3000)
            y -= 1900;

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