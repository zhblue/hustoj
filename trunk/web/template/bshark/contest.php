<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        <?php echo $view_title; ?> - <?php echo $MSG_CONTEST; ?> - <?php echo $OJ_NAME; ?>
    </title>
    <?php require("./template/bshark/header-files.php"); ?>
</head>

<body>
    <?php require("./template/bshark/nav.php"); ?>
    <div class="ui container bsharkMain">
        <div class="ui stackable grid">
            <div class="eleven wide column">
                <div class="card">
                    <div class="card-body">
                        <h2>C<?php echo $view_cid; ?>: <?php echo $view_title; ?>
                        </h2>
                        <span class="ui label <?php
                        if ($now > $end_time)
                            echo "grey";
                        else if ($now < $start_time)
                            echo "green";
                        else
                            echo "red";
                        ?>">
                            <?php
                            if ($now > $end_time)
                                echo $MSG_Ended;
                            else if ($now < $start_time)
                                echo "未开始";
                            else
                                echo $MSG_Running;
                            ?>
                        </span>
                        
                        <span class="ui label <?php
                        if ($view_private == '0')
                            echo "blue";
                        else
                            echo "red";
                        ?>">
                            <?php
                            if ($view_private == '0')
                                echo $MSG_Public;
                            else
                                echo $MSG_Private;
                            ?>
                        </span>
                        <h3>
                            <?php echo $MSG_CONTEST; ?><?php echo $MSG_TIME; ?>
                        </h3>
                        <span class="ui label basic black">
                            <?php echo $view_start_time; ?>
                        </span>~<span class="ui label basic black"><?php echo $view_end_time; ?></span> 
                        <?php if (isset($OJ_RANK_LOCK_PERCENT)&&$OJ_RANK_LOCK_PERCENT!=0) { ?>
                        Lock Board Time: <?php echo date("Y-m-d H:i:s", $view_lock_time) ?><br>
                        <?php } ?>
                        现在:<span
                            class="ui blue label" id="nowdate"></span>
                        <h3>
                            <?php echo $MSG_CONTEST . $MSG_Description; ?>
                        </h3>
                        <blockquote>
                            <?php echo $view_description ?>
                        </blockquote>
                        <hr />
                        <?php if (time() > $start_time) { ?>
                            <table id='problemset' class='ui compact table'>
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th><?php echo $MSG_PROBLEM_ID ?></th>
                                        <th>
                                            <?php echo $MSG_TITLE ?>
                                        </th>
                                        <!-- <td><?php echo $MSG_SOURCE ?></td>  -->
                                        <th>
                                            <?php echo $MSG_AC ?>
                                        </th>
                                        <th>
                                            <?php echo $MSG_SUBMIT ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cnt = 0;
                                    foreach ($view_problemset as $row) {
                                        if ($cnt)
                                            echo "<tr class='oddrow'>";
                                        else
                                            echo "<tr class='evenrow'>";
                                        foreach ($row as $table_cell) {
                                            if (trim($table_cell) == "<div class='label label-danger'>N</div>") {
                                                echo "<td class='left red marked'>";
                                                echo "</td>";
                                            } else if (trim($table_cell) == "<div class='label label-success'>Y</div>") {
                                                echo "<td class='left green marked'>";
                                                echo "</td>";
                                            } else {
                                                echo "<td>";
                                                echo "\t" . $table_cell;
                                                echo "</td>";
                                            }
                                        }
                                        echo "</tr>";
                                        $cnt = 1 - $cnt;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="five wide column">
                <div class="card" style="padding: 0;">
                    <div class="ui vertical fluid menu problemAction">
                        <a class="active item" href='contest.php?cid=<?php echo $view_cid ?>'>
                            <?php echo $MSG_CONTEST; ?>C<?php echo $cid; ?>
                        </a></li>
                        <a class="item" href='status.php?cid=<?php echo $view_cid ?>'>
                            <?php echo $MSG_STATUS; ?>
                        </a>
                        <a class="item" href='contestrank.php?cid=<?php echo $view_cid ?>'>
                            <?php echo $MSG_STANDING; ?>
                        </a>
                        <a class="item" href='contestrank-oi.php?cid=<?php echo $view_cid ?>'>OI-<?php echo $MSG_STANDING; ?></a>
                        <a class="item" href='conteststatistics.php?cid=<?php echo $view_cid ?>'>
                            <?php echo $MSG_STATISTICS; ?>
                        </a>
                        <?php if(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])) {?>
                            <a href="suspect_list.php?cid=<?php echo $view_cid?>" class="item"><?php echo $MSG_IP_VERIFICATION?></a>
                            <a href="user_set_ip.php?cid=<?php echo $view_cid?>" class="item"><?php echo $MSG_SET_LOGIN_IP?></a>
                            <a target="_blank" href="admin/contest_edit.php?cid=<?php echo $view_cid ?>" class="item"><?php echo $MSG_EDIT.$MSG_CONTEST;?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require("./template/bshark/footer.php"); ?>
    <?php require("./template/bshark/footer-files.php"); ?>
    <script>
        var diff = new Date("<?php echo date("Y/m/d H:i:s") ?>").getTime() - new Date().getTime();
        //alert(diff);
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
            n = y + "-" + mon + "-" + d + " " + (h >= 10 ? h : "0" + h) + ":" + (m >= 10 ? m : "0" + m) + ":" + (s >= 10 ? s : "0" + s);
            //alert(n);
            document.getElementById('nowdate').innerHTML = n;
            setTimeout("clock()", 1000);
        }
        clock();
    </script>
</body>

</html>
