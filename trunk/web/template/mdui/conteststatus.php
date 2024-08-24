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
    <?php $page_title = "状态 - ".$cid.": ".$row['title']; ?>
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
            <?php }?>

            <div align=center class="input-append">
                <div class="mdui-center mdui-row-sm-6 mdui-row-xs-1">
                    <form id="simform" class="" action="status.php" method="get">
                        <div class="mdui-textfield mdui-col">
                            <input class="mdui-textfield-input" type="text" name="problem_id"
                                value="<?php echo htmlspecialchars($problem_id, ENT_QUOTES); ?>"
                                placeholder="题目编号">
                        </div>
                        <div class="mdui-textfield mdui-col">
                            <input class="mdui-textfield-input" type="text" name="user_id"
                                value="<?php echo htmlspecialchars($user_id, ENT_QUOTES); ?>"
                                placeholder="用户">
                        </div>
                        <?php if (isset($cid)) { ?>
                            <input type="hidden" name="cid" value="<?php echo $cid; ?>">
                        <?php } ?>

                        <div class="mdui-col mdui-p-a-1 mdui-p-t-2">
                            <span class="mdui-m-r-2">语言</span>
                            <div style="width: calc(100% - 50px); display: inline-block;">
                                <select class="mdui-select" name="language" mdui-select="{position: 'bottom'}">
                                    <option value="-1">All</option>
                                    <?php
                                        if (isset($_GET['language'])) {
                                            $selectedLang = intval($_GET['language']);
                                        }
                                        else {
                                            $selectedLang = -1;
                                        }
                                        $lang_count = count($language_ext);
                                        $langmask = $OJ_LANGMASK;
                                        $lang = (~((int)$langmask))&((1<<($lang_count))-1);
                                        for ($i=0; $i<$lang_count; $i++) {
                                            if ($lang & (1<<$i)) {
                                                echo '<option value="'.$i.'" '.($selectedLang == $i ? "selected" : "").'>'
                                                    .$language_name[$i]
                                                    .'</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="mdui-col mdui-p-a-1 mdui-p-t-2">
                            <span class="mdui-m-r-2">结果</span>
                            <div style="width: calc(100% - 50px); display: inline-block;">
                                <select class="mdui-select mdui-col" name="jresult" mdui-select="{position: 'bottom'}">
                                    <?php
                                if (isset($_GET['jresult']))
                                    $jresult_get = intval($_GET['jresult']);
                                else
                                    $jresult_get = -1;

                                if ($jresult_get>=12 || $jresult_get<0)
                                    $jresult_get = -1;
                                if ($jresult_get==-1)
                                    echo "<option value='-1' selected>All</option>";
                                else
                                    echo "<option value='-1'>All</option>";
                                    
                                for ($j=0; $j<12; $j++) {
                                    $i = ($j+4)%12;
                                    if ($i==$jresult_get)
                                        echo "<option value='".$jresult_get."' selected>".$jresult[$i]."</option>";
                                    else
                                        echo "<option value='".$i."'>".$jresult[$i]."</option>";
                                }
                            ?>
                                </select>
                            </div>
                        </div>

                        <?php
                            if (isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'source_browser'])) {
                                if (isset($_GET['showsim']))
                                    $showsim = intval($_GET['showsim']);
                                else
                                    $showsim = 0;

                                echo '<div class="mdui-col mdui-p-a-1 mdui-p-t-2">'
                                    .'<span class="mdui-m-r-2">SIM</span>'
                                    .'<div style="width: calc(100% - 50px); display: inline-block;">'
                                    .'<select id="appendedInputButton" class="mdui-select mdui-col" mdui-select="" name="showsim" onchange="document.getElementById(\'simform\').submit();">'
                                    .'<option value=0 '.($showsim==0?'selected':'').'>All</option>'
                                    .'<option value="50" '.($showsim==50?'selected':'').'>50</option>'
                                    .'<option value="60" '.($showsim==60?'selected':'').'>60</option>'
                                    .'<option value="70" '.($showsim==70?'selected':'').'>70</option>'
                                    .'<option value="80" '.($showsim==80?'selected':'').'>80</option>'
                                    .'<option value="90" '.($showsim==90?'selected':'').'>90</option>'
                                    .'<option value="100" '.($showsim==100?'selected':'').'>100</option>'
                                    .'</select>'
                                    .'</div>'
                                    .'</div>';
                            }
                        ?>
                        <button class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-col mdui-m-t-2" type="submit">查询</button>
                    </form>
                </div>
            </div>

            <div class="mdui-table-fluid mdui-m-y-4">
                <table  id="result-tab"  class="mdui-table mdui-table-hoverable mdui-center" width=80%>
                    <thead>
                        <tr>
                            <th class="mdui-table-col-numeric">提交编号</th>
                            <th>用户</th>
		<th><?php echo $MSG_NICK?></th>
                            <th>题目编号</th>
                            <th>结果</th>
                            <th>内存</th>
                            <th>耗时</th>
                            <th>语言</th>
                            <th>代码长度</th>
                            <th>提交时间</th>
                            <?php if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])) { ?>
                                <th><?php echo $MSG_JUDGER; ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $cnt = 0;
                            foreach ($view_status as $row) {
                                echo '<tr>';
                                foreach ($row as $table_cell) {
                                    echo '<td>'.$table_cell.'</td>';
                                }
                                echo '</tr>';
                            }
                        ?>
                        <script> console.log(<?php echo json_encode($result); ?>); </script>
                    </tbody>
                </table>
            </div>

            <div class="mdui-btn-group" style="width: 100% !important;">
                <?php
                    echo '<a class="mdui-btn mdui-float-left" href="status.php?'.$str2.'">'
                        .'<i class="mdui-icon material-icons">first_page</i>'
                        .'<span class="mdui-m-l-1">Top</span>'
                        .'</a>';
                    if (isset($_GET['prevtop'])) {
                        echo '<a class="mdui-btn mdui-float-left" href="status.php?'.$str2.'&top='.intval($_GET['prevtop']).'">'
                            .'<i class="mdui-icon material-icons">navigate_before</i>'
                            .'<span class="mdui-m-l-1">Prev</span>'
                            .'</a>';
                    } else {
                        echo '<a class="mdui-btn mdui-float-left" href="status.php?'.$str2.'&top='.($top+50).'">'
                            .'<i class="mdui-icon material-icons">navigate_before</i>'
                            .'<span class="mdui-m-l-1">Prev</span>'
                            .'</a>';
                    }
                    echo '<a class="mdui-btn mdui-float-right" href="status.php?'.$str2.'&top='.$bottom.'&prevtop='.$top.'">'
                        .'<span class="mdui-m-r-1">Next</span>'
                        .'<i class="mdui-icon material-icons">navigate_next</i>'
                        .'</a>';
                ?>
            </div>

        </div>

    </div>

    <script>
    var judge_result = [<?php
  foreach ($judge_result as $result) {
        echo "'$result',";
    } ?> ''];

    var judge_color = [<?php
    foreach ($judge_color as $result) {
        echo "'$result',";
    } ?> ''];
    var oj_mark= <?php echo "'$OJ_MARK'";?>;
    </script>

    <script src="template/<?php echo $OJ_TEMPLATE?>/auto_refresh.js"></script>

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
