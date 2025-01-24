<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        <?php echo $MSG_STATISTICS; ?> - <?php $OJ_NAME; ?>
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
                        <h2>C<?php echo $view_cid; ?>: <?php echo $view_title; ?> - <?php echo $MSG_STATISTICS; ?>
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
                        <table id=cs class="ui table center aligned" width=100%>
                            <thead>
                                <tr class=toprow>
                                    <th></th>
                                    <th>AC</th>
                                    <th>PE</th>
                                    <th>WA</th>
                                    <th>TLE</th>
                                    <th>MLE</th>
                                    <th>OLE</th>
                                    <th>RE</th>
                                    <th>CE</th>
                                    <th>TR</th>
                                    <th>|</th>
                                    <th>Total</th>
                                    <?php
                                    $i = 0;
                                    foreach ($language_name as $lang) {
                                        if (isset($R[$pid_cnt][$i + 11]))
                                            echo "<th>$language_name[$i]</th>";
                                        else
                                            echo ""; //"<th></th>";
                                        $i++;
                                    }
                                    ?>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                for ($i = 0; $i < $pid_cnt; $i++) {
                                    if (!isset($PID[$i]))
                                        $PID[$i] = "";

                                    if ($i & 1)
                                        echo "<tr>";
                                    else
                                        echo "<tr>";

                                    if (time() < $end_time) { //during contest/exam time
                                        echo "<td><a href=problem.php?cid=$cid&pid=$i>$PID[$i]</a></td>";
                                    } else { //over contest/exam time
                                        //check the problem will be use remained contest/exam
                                        $sql = "SELECT `problem_id` FROM `contest_problem` WHERE (`contest_id`=? AND `num`=?)";
                                        $tresult = pdo_query($sql, $cid, $i);

                                        $tpid = $tresult[0][0];
                                        $sql = "SELECT `problem_id` FROM `problem` WHERE `problem_id`=? AND `problem_id` IN (
									SELECT `problem_id` FROM `contest_problem` WHERE `contest_id` IN (
										SELECT `contest_id` FROM `contest` WHERE (`defunct`='N' AND now()<`end_time`)
									)
								)";
                                        $tresult = pdo_query($sql, $tpid);

                                        if (intval($tresult) != 0) //if the problem will be use remained contes/exam */
                                            echo "<td>$PID[$i]</td>";
                                        else
                                            echo "<td><a href='problem.php?id=" . $tpid . "'>" . $PID[$i] . "</a></td>";
                                    }

                                    for ($j = 0; $j < count($language_name) + 11; $j++) {
                                        //	if (!isset($R[$i][$j]))
                                        //		$R[$i][$j]="";
                                        if ($j < 11 || isset($R[$pid_cnt][$j]))
                                            echo "<td>" . $R[$i][$j] . "</td>";
                                    }
                                    echo "</tr>";
                                }

                                echo "<tr>";
                                echo "<td>Total</td>";

                                for ($j = 0; $j < count($language_name) + 11; $j++) {
                                    //	if(!isset($R[$i][$j]))
                                    //		$R[$i][$j]="";
                                
                                    if ($j < 11 || isset($R[$pid_cnt][$j]))
                                        echo "<td>" . $R[$i][$j] . "</td>";

                                }
                                echo "</tr>";

                                ?>
                            </tbody>
                        </table>
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
                        <a class="active item" href='conteststatistics.php?cid=<?php echo $view_cid ?>'>
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
