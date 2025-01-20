<!DOCTYPE html>
<html lang="en">
<head>
    <?php $page_title = '统计'; ?>
    <?php include('_includes/head.php'); ?>



</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <div class="jumbotron">

            <?php
            if (isset($_GET['cid'])) {
                $cid = intval($_GET['cid']);
                $view_cid = $cid;
                //print $cid;

                //check contest valid
                $sql = "SELECT * FROM `contest` WHERE `contest_id`=?";
                $result = pdo_query($sql,$cid);

                $rows_cnt = count($result);
                $contest_ok = true;
                $password = "";

                if (isset($_POST['password']))
                    $password = $_POST['password'];

                if (false) {
                    $password = stripslashes($password);
                }

                if ($rows_cnt==0) {
                    $view_title = "比赛已经关闭!";
                }
                else{
                    $row = $result[0];
                    $view_private = $row['private'];

                    if ($password!="" && $password==$row['password'])
                        $_SESSION[$OJ_NAME.'_'.'c'.$cid] = true;

                    if ($row['private'] && !isset($_SESSION[$OJ_NAME.'_'.'c'.$cid]))
                        $contest_ok = false;

                    if($row['defunct']=='Y')
                        $contest_ok = false;

                    if (isset($_SESSION[$OJ_NAME.'_'.'administrator']))
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
            <?php }?>
            <br>
            <center>
                <h4><?php if (isset($locked_msg)) echo $locked_msg;?></h4>
                <table id="cs" class="mdui-table mdui-table-hoverable" style="overflow: scroll;" width="90%">
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
                            <th>-</th>                            
                            <th>Total</th>
                            <?php 
                            $i = 0;
                            foreach ($language_name as $lang) {
                                // if (isset($R[$pid_cnt][$i+11]) )    
                                    echo "<th>$language_name[$i]</th>";
                                // else
                                //     echo "<th style='display:none'></th>";
                                $i++;
                            }
                            ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        for ($i=0; $i<$pid_cnt; $i++) {
                            if(!isset($PID[$i]))
                                $PID[$i] = "";

                                echo "<tr>";

                            if (time()<$end_time) {  //during contest/exam time
                                echo "<td><a href=problem.php?cid=$cid&pid=$i>$PID[$i]</a></td>";
                            }
                            else {  //over contest/exam time
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

                                if (intval($tresult) != 0)   //if the problem will be use remained contes/exam */
                                    echo "<td>$PID[$i]</td>";
                                else
                                    echo "<td><a href='problem.php?id=".$tpid."'>".$PID[$i]."</a></td>";
                            }

                            for ($j=0; $j<count($language_name)+11; $j++) {
                                if (!isset($R[$i][$j]))
                                    $R[$i][$j]="0";
                                // else {
                                    echo "<td>".$R[$i][$j]."</td>";
                                // }
                            }
                            echo "</tr>";
                        }

                        echo "<tr class='evenrow'>";
                            echo "<td>Total</td>";

                            for ($j=0; $j<count($language_name)+11; $j++) {
                                if(!isset($R[$i][$j]))
                                    $R[$i][$j]="0";
                                
                                echo "<td>".$R[$i][$j]."</td>";
                            }
                        echo "</tr>";

                        ?>
                    </tbody>
                </table>
            </center>

        </div>

    </div>
<script>
    var diff = new Date("<?php echo date("Y/m/d H:i:s")?>").getTime()-new Date().getTime();
    //alert(diff);
    function clock() {
        var x,h,m,s,n,xingqi,y,mon,d;
        var x = new Date(new Date().getTime()+diff);
        y = x.getYear()+1900;

        if (y>3000)
            y -= 1900;

        mon = x.getMonth()+1;
        d = x.getDate();
        xingqi = x.getDay();
        h = x.getHours();
        m = x.getMinutes();
        s = x.getSeconds();
        n = y+"-"+(mon>=10?mon:"0"+mon)+"-"+(d>=10?d:"0"+d)+" "+(h>=10?h:"0"+h)+":"+(m>=10?m:"0"+m)+":"+(s>=10?s:"0"+s);

        //alert(n);
        document.getElementById('nowdate').innerHTML = n;
        setTimeout("clock()",1000);
    }
    clock();
</script>

</body>
</html>
