<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        <?php echo $MSG_SET_LOGIN_IP; ?> - <?php echo $OJ_NAME; ?>
    </title>

    <?php require("./template/bshark/header-files.php"); ?>
</head>

<body>
    <?php include("template/$OJ_TEMPLATE/nav.php"); ?>
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
                        <?php if (isset($OJ_RANK_LOCK_PERCENT) && $OJ_RANK_LOCK_PERCENT != 0) { ?>
                            Lock Board Time: <?php echo date("Y-m-d H:i:s", $view_lock_time) ?><br>
                        <?php } ?>
                        现在:<span class="ui blue label" id="nowdate"></span>
                        <div class="ui divider"></div>
                        <form action=user_set_ip.php?cid=<?php echo $view_cid ?> method=post class="ui form">
                            <div class="field">
                                <label>
                                    <?php echo $MSG_USER_ID ?>
                                </label>
                                <?php if (isset($_GET['uid'])) { ?>
                                    <div class="col-sm-3"><input name="user_id" class="form-control"
                                            value="<?php echo htmlentities($_GET['uid'], ENT_QUOTES, 'UTF-8'); ?>"
                                            type="text" required></div>
                                <?php } else if (isset($_POST['user_id'])) { ?>
                                        <div class="col-sm-3"><input name="user_id" class="form-control"
                                                value="<?php echo htmlentities($_POST['user_id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                type="text" required></div>
                                <?php } else { ?>
                                        <div class="col-sm-3"><input name="user_id" class="form-control"
                                                placeholder="<?php echo $MSG_USER_ID . "*" ?>" type="text" required></div>
                                <?php } ?>
                            </div>

                            <div class="field">
                                <label>
                                    <?php echo "New IP" ?>
                                </label>
                                <?php if (isset($_POST['ip'])) { ?>
                                    <div class="col-sm-3"><input name="ip" class="form-control"
                                            value="<?php echo htmlentities($_POST['ip'], ENT_QUOTES, 'UTF-8') ?>"
                                            type="text" autocomplete="off" required></div>
                                <?php } else { ?>
                                    <div class="col-sm-3"><input name="ip" class="form-control"
                                            placeholder="<?php echo "?.?.?.?*" ?>" type="text" autocomplete="off" required>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="field">
                                <button name="do" type="hidden" value="do" class="ui button blue">
                                    <?php echo $MSG_SAVE; ?>
                                </button>
                                <button name="submit" type="reset" class="ui button blue basic"><?php echo $MSG_RESET ?></button>
                            </div>
                        </form>
                        <div class="ui divider"></div>

                        <?php
                        if ($result2 == "changed")
                            echo "<h2 style='color:#db2828'>User " . htmlentities($_POST['user_id'], ENT_QUOTES, 'UTF-8') . "'s Login IP Changed to " . $ip . "</h2>";
                        else
                            echo "<h2 style='color:#db2828'>Login IP Change</h2>";
                        ?>
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
                        <a class="item" href='contestrank.php?cid=<?php echo $view_cid ?>'>
                            <?php echo $MSG_STANDING; ?>
                        </a>
                        <a class="item" href='contestrank-oi.php?cid=<?php echo $view_cid ?>'>OI-<?php echo $MSG_STANDING; ?></a>
                        <a class="item" href='conteststatistics.php?cid=<?php echo $view_cid ?>'>
                            <?php echo $MSG_STATISTICS; ?>
                        </a>
                        <?php if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator'])) { ?>
                            <a href="suspect_list.php?cid=<?php echo $view_cid ?>" class="item">
                                <?php echo $MSG_IP_VERIFICATION ?>
                            </a>
                            <a href="user_set_ip.php?cid=<?php echo $view_cid ?>" class="active item">
                                <?php echo $MSG_SET_LOGIN_IP ?>
                            </a>
                            <a target="_blank" href="admin/contest_edit.php?cid=<?php echo $view_cid ?>"
                                class="item">
                                <?php echo $MSG_EDIT . $MSG_CONTEST; ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- /container -->
    <!-- Bootstrap core JavaScrip
================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php"); ?>

    <script src="<?php echo $OJ_CDN_URL ?>include/sortTable.js"></script>

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

</body>

</html>
