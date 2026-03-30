<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        <?php echo $MSG_STANDING; ?> - <?php echo $OJ_NAME; ?>
    </title>
    <?php require("./template/bshark/header-files.php"); ?>
</head>

<body>
    <?php require("./template/bshark/nav.php"); ?>
    <?php
    if (isset($_GET['cid'])) {
        $cid = intval($_GET['cid']);
        $view_cid = $cid;
        //print $cid;
    
        //check contest valid
        $sql = "SELECT * FROM `contest` WHERE `contest_id`=?";
        $result = pdo_query($sql, $cid);

        $rows_cnt = count($result);
        $contest_ok = true;
        $password = "";

        if (isset($_POST['password']))
            $password = $_POST['password'];

        if (false) {
            $password = stripslashes($password);
        }

        if ($rows_cnt == 0) {
            $view_title = "比赛已经关闭!";
        } else {
            $row = $result[0];
            $view_private = $row['private'];

            if ($password != "" && $password == $row['password'])
                $_SESSION[$OJ_NAME . '_' . 'c' . $cid] = true;

            if ($row['private'] && !isset($_SESSION[$OJ_NAME . '_' . 'c' . $cid]))
                $contest_ok = false;

            if ($row['defunct'] == 'Y')
                $contest_ok = false;

            if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']))
                $contest_ok = true;

            $now = time();
            $start_time = strtotime($row['start_time']);
            $end_time = strtotime($row['end_time']);
            $view_description = $row['description'];
            $view_title = $row['title'];
            $view_start_time = $row['start_time'];
            $view_end_time = $row['end_time'];
        }
    }
    ?>
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
                                echo $MSG_Contest_Pending;
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
                        </span>~<span class="ui label basic black"><?php echo $view_end_time; ?></span> 现在:<span
                            class="ui blue label" id="nowdate"></span>
                        <a class="ui button mini blue" href="contestrank.xls.php?cid=<?php echo $cid ?>">Download xls
                            file</a>
                        <h1 style="color: #db2828;">页面不可用！</h1>
                    </div>
                </div>
            </div>
            <div class="five wide column">
                <div class="card" style="padding: 0;">
                    <div class="ui vertical fluid menu problemAction">
                        <a class="item" href='contest.php?cid=<?php echo $view_cid ?>'>
                            <?php echo $MSG_CONTEST; ?>C<?php echo $cid; ?>
                        </a></li>
                        <a class="item" href='status.php?cid=<?php echo $view_cid ?>'>
                            <?php echo $MSG_STATUS; ?>
                        </a>
                        <a class="active item" href='contestrank.php?cid=<?php echo $view_cid ?>'>
                            <?php echo $MSG_STANDING; ?>
                        </a>
                        <a class="item" href='contestrank-oi.php?cid=<?php echo $view_cid ?>'>OI-<?php echo $MSG_STANDING; ?></a>
                        <a class="item" href='conteststatistics.php?cid=<?php echo $view_cid ?>'>
                            <?php echo $MSG_STATISTICS; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        var diff = new Date("<?php echo date("Y/m/d H:i:s") ?>").getTime() - new Date().getTime();
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
            n = y + "-" + (mon >= 10 ? mon : "0" + mon) + "-" + (d >= 10 ? d : "0" + d) + " " + (h >= 10 ? h : "0" + h) + ":" + (m >= 10 ? m : "0" + m) + ":" + (s >= 10 ? s : "0" + s);

            //alert(n);
            document.getElementById('nowdate').innerHTML = n;
            setTimeout("clock()", 1000);
        }
        clock();
    </script>
    <?php require("./template/bshark/footer.php"); ?>
    <?php require("./template/bshark/footer-files.php"); ?>
</body>

</html>