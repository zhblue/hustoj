<!DOCTYPE html>
<html lang="cn">
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

<head>
    <?php $page_title = "排名 - ".$cid.": ".$row['title']; ?>
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

            <?php if (isset($_GET['cid'])) { ?>
                <div class="mdui-card mdui-m-y-2" style="text-align: center;">
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
            <?php $rank = 1; ?>
            <div class="mdui-float-right">
                <a href="contestrank.xls.php?cid=<?php echo $cid; ?>"
                    class="mdui-btn mdui-ripple mdui-color-purple mdui-btn-dense">
                    <i class="mdui-icon material-icons">file_download</i>
                    &nbsp;下载排名
                </a>
                <h4><?php if (isset($locked_msg)) echo $locked_msg; ?></h4>
                <?php if ($OJ_MEMCACHE) { ?>
                    <?php if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])) { ?>
                        <a href="contestrank3.php?cid=<?php echo $cid; ?>">滚榜</a>';
                        <a href="contestrank2.php?cid=<?php echo $cid; ?>">Replay</a>';
                    <?php } ?>
                <?php } ?>
            </div>

            <div class="">
                <table id="rank" class="mdui-table mdui-table-hoverable" width="80%">
                    <thead>
                    <tr class='toprow'>
                        <th><?php echo $MSG_STANDING?></th>
                        <th><?php echo $MSG_USER?></th>
                        <th><?php echo $MSG_NICK?></th>
                        <th><?php echo $MSG_SOVLED?></th>
                        <th><?php echo $MSG_CONTEST_PENALTY?></th>
                                        <?php
                                        for ($i=0; $i<$pid_cnt; $i++) {
                                        if (time() < $end_time) {  //during contest/exam time
                                                echo '<th><a href="problem.php?cid='.$cid.'&pid='.$i.'">'.$PID[$i].'</a></th>';
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
                                                echo "<th>$PID[$i]</th>";
                                    else
                                        echo "<th><a href='problem.php?id=".$tpid."'>".$PID[$i]."</a></th>";
                                            }
                                        }
                                        ?>
                    </tr>
                    </thead>

                    <tbody>
                    <?php
                    $cnt=0;
                                for ($i=0; $i<$user_cnt; $i++) {
                                if ($i&1)
                                echo "<tr class='oddrow'>";
                        else
                        echo "<tr class='evenrow'>";
                                
                                $nick = $U[$i]->nick;
                                echo "<td class='text-center'>";
                                        if ($nick[0]!="*")
                                            echo $rank++;
                                        else
                                            echo "*";
                                echo "</td>";

                                    $uuid = $U[$i]->user_id;
                                    if (isset($_GET['user_id']) && $uuid==$_GET['user_id'])
                                    echo "<td class='text-center'bgcolor=#ffff77>";
                                    else
                                        echo"<td class='text-center'>";
                                    echo "<a name=\"$uuid\" href=userinfo.php?user=$uuid>$uuid</a>";
                                    echo "</td>";

                                    echo "<td class='text-center'><a href=userinfo.php?user=$uuid>".htmlentities($U[$i]->nick,ENT_QUOTES,"UTF-8")."</a></td>";

                                    $usolved = $U[$i]->solved;
                                    echo "<td class='text-center'><a href=status.php?user_id=$uuid&cid=$cid>$usolved</a></td>";

                                    echo "<td class='text-center'>".sec2str($U[$i]->time)."</td>";

                                    for ($j=0; $j<$pid_cnt; $j++) {
                                        $bg_color = "eeeeee";
                                        if (isset($U[$i]->p_ac_sec[$j])&&$U[$i]->p_ac_sec[$j]>0){
                                            $aa = 0x33+$U[$i]->p_wa_num[$j]*32;
                                            $aa = $aa>0xaa?0xaa:$aa;
                                            $aa = dechex($aa);
                                            $bg_color = "$aa"."ff"."$aa";
                                            //$bg_color="aaffaa";
                                            if ($uuid==$first_blood[$j]) {
                                                $bg_color = "aaaaff";
                                            }
                                        }
                                        else if(isset($U[$i]->p_wa_num[$j]) && $U[$i]->p_wa_num[$j]>0) {
                                            $aa = 0xaa-$U[$i]->p_wa_num[$j]*10;
                                            $aa = $aa>16?$aa:16;
                                            $aa = dechex($aa);
                                            $bg_color = "ff$aa$aa";
                                        }
                                        echo "<td class='well' style='background-color:#$bg_color'>";
                                        if (isset($U[$i])) {
                                            if (isset($U[$i]->p_ac_sec[$j]) && $U[$i]->p_ac_sec[$j]>0)
                                                echo sec2str($U[$i]->p_ac_sec[$j]);
                                            if (isset($U[$i]->p_wa_num[$j]) && $U[$i]->p_wa_num[$j]>0)
                                                echo "(-".$U[$i]->p_wa_num[$j].")";
                                        }
                                    }
                                    echo "</tr>\n";
                                }
                    ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>


    <script>
        function getTotal(rows) {
            var total = 0;
            for (var i=0; i<rows.length && total==0; i++) {
                try {
                    total = parseInt(rows[rows.length-i].cells[0].innerHTML);
                    if (isNaN(total))
                        total = 0;
                }
                catch (e) {
                }
            }
            return total;
        }

        function metal() {
            var tb = window.document.getElementById('rank');
            var rows = tb.rows;
            var header="";
            try {
                <?php 
                //若有队伍从未进行过任何提交，数据库solution表里不会有数据，榜单上该队伍不存在，总rows数量不等于报名参赛队伍数量，奖牌比例的计算会出错
                //解决办法：可以为现场赛采用人为设定有效参赛队伍数$OJ_ON_SITE_TEAM_TOTAL，值为0时则采用榜单计算。详情见db_info.inc.php
                if (isset($OJ_ON_SITE_TEAM_TOTAL) && $OJ_ON_SITE_TEAM_TOTAL!=0)
                    echo "var total=".$OJ_ON_SITE_TEAM_TOTAL.";";
                else
                    echo "var total=getTotal(rows);";
                ?>

                //alert(total);
                for (var i=1; i<rows.length; i++) {
                    var cell = rows[i].cells[0];
                    var acc = rows[i].cells[3];
                    var ac = parseInt(acc.innerText);

                    if (isNaN(ac))
                        ac = parseInt(acc.textContent);
                    
                    if (cell.innerHTML!="*" && ac>0) {
                        var r = parseInt(cell.innerHTML);
                        if (r==1) {
                            cell.innerHTML = "Winner";
                            //cell.style.cssText="background-color:gold;color:red";
                            cell.className = "badge btn-warning center-block";
                        }

                        if (r>1 && r<=total*.05+1)
                            cell.className = "badge btn-warning center-block";

                        if (r>total*.05+1 && r<=total*.20+1)
                            cell.className = "badge center-block";

                        if(r>total*.20+1 && r<=total*.45+1)
                            cell.className = "badge btn-danger center-block";

                        if(r>total*.45+1 && ac>0)
                            cell.className = "badge badge-info center-block";
                    }

                }

                <?php if (isset($OJ_CONTEST_RANK_FIX_HEADER)&&$OJ_CONTEST_RANK_FIX_HEADER){?>
                for (var i=0; i<rows.length; i++) {
                    header+="<tr style='height:23px'>";
                    for(var j=0;j<5;j++){
                      header+=rows[i].cells[j].outerHTML;
                      rows[i].cells[j].hidden=true;
                      rows[i].cells[j].innerHTML="";
                    }
                    header+="</tr>";
                }
                $("#tbheader").append("<table class=oddrow >"+header+"</table>");
                <?php } ?>

            }
            catch (e) {
                //alert(e);
            }
        }

        <?php if($OJ_SHOW_METAL) { ?>
            metal();
        <?php } ?>        
    </script>

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

    <style>
        .well {
            background-image:none;
            padding:1px;
            text-align: center;
        }

        td {
            white-space:nowrap;
        }
    </style>

</body>
</html>
