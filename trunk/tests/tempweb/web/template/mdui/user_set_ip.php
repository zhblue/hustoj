<!DOCTYPE html>
<html lang="en">

<head>
    <?php $page_title = "指定登录IP"; ?>
    <?php include('_includes/head.php') ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
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
        <div class="mdui-row mdui-m-y-4" style="display: flex; justify-content: center;">
            <div class="mdui-card mdui-col-sm-6" style="text-align: left;">
                <div class="mdui-card-primary">
                <?php if ($result2 == "changed") { ?>
                    <script> mdui.alert("成功将用户 <?php echo htmlentities($_POST['user_id'], ENT_QUOTES, 'UTF-8'); ?> 的登录 IP 修改为 <?php echo $ip; ?> ."); </script>
                <?php } ?>
                    <div class="mdui-card-primary-title" style="text-align: center">指定登录 IP</div>
                    <!-- <div class="mdui-card-primary-subtitle"></div> -->
                </div>
                <form id="changeip" action="user_set_ip.php" method="post" role="form"
                    class="mdui-m-a-2 mdui-card-content">
                    <input type="hidden" name="cid" value="<?php echo $view_cid?>" />
                    <div style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon" style="display: inline-block; top: 40px; position: absolute;">
                            <i class="mdui-icon material-icons">account_circle</i>
                        </a>
                        <div class="mdui-textfield mdui-textfield-floating-label"
                            style="width: calc(100% - 50px); display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">用户名</label>
                            <?php if (isset($_POST["user_id"])) { ?>
                                <input class="mdui-textfield-input" name="user_id" type="text"
                                    value="<?php echo htmlentities($_POST["user_id"], ENT_QUOTES, 'UTF-8'); ?>" required />
                            <?php } else if (isset($_GET["user_id"])) { ?>
                                <input class="mdui-textfield-input" name="user_id" type="text"
                                    value="<?php echo htmlentities($_GET["user_id"],ENT_QUOTES,'UTF-8') ; ?>" required />
                            <?php } else { ?>
                                <input class="mdui-textfield-input" name="user_id" type="text" required />
                            <?php } ?>
                            <div class="mdui-textfield-error">请输入用户名</div>
                        </div>
                    </div>
                    <div style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon" style="display: inline-block; top: 40px; position: absolute;">
                            <i class="mdui-icon material-icons">location_on</i>
                        </a>
                        <div class="mdui-textfield mdui-textfield-floating-label"
                            style="width: calc(100% - 50px); display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">IP</label>
                            <input class="mdui-textfield-input" name="ip" type="text" autocomplete="off" required />
                            <div class="mdui-textfield-error">请输入要指定的 IP</div>
                        </div>
                    </div>
                    <div class="mdui-card-actions mdui-m-t-4">
                        <div class="mdui-float-right">
                            <button name="submit" type="submit" class="mdui-btn mdui-m-a-0 mdui-color-theme-accent">提交</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
