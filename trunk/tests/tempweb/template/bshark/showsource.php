<?php
$color = array(
    0 => 'grey',
    1 => 'grey',
    2 => 'grey',
    3 => 'grey',
    4 => 'green',
    5 => 'red',
    6 => 'red',
    7 => 'yellow',
    8 => 'blue',
    9 => '',
    10 => 'yellow',
    11 => 'yellow'
);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        <?php echo $MSG_SOURCE_CODE; ?> - <?php echo $OJ_NAME; ?>
    </title>
    <?php require("./template/bshark/header-files.php"); ?>
</head>

<body>
    <?php require("./template/bshark/nav.php"); ?>
    <div class="ui container bsharkMain">
        <div class="card">
            <div class="card-body">
                <h2>
                    <?php echo $MSG_SOURCE_CODE; ?> <?php echo $id; ?>
                </h2>
                <h5>
                    <?php echo $MSG_RESULT; ?>: <span class="ui label" id="status_show">加载中</span>
                </h5>
                <table class="ui very basic table">
                    <tbody>
                        <tr>
                            <th width=20%>题目</th>
                            <td width=30%><a href="problem.php?id=<?php echo $sproblem_id; ?>">P<?php echo $sproblem_id; ?></a></td>
                            <th width=20%>语言</th>
                            <td width=30%>
                                <?php echo $language_name[$slanguage]; ?>
                            </td>
                        </tr>
                        <tr>
                            <th width=20%>用户</th>
                            <td width=30%><a href="userinfo.php?user=<?php echo $suser_id; ?>">
                                    <?php echo $suser_id; ?>
                                </a></td>
                            <th width=20%>时间</th>
                            <td width=30% id="stime">加载中</td>
                        </tr>
                        <tr>
                            <th width=20%>提交时间</th>
                            <td width=30%>
                                <?php echo $sin_date; ?>
                            </td>
                            <th width=20%>空间</th>
                            <td width=30% id='smemory'>加载中</td>
                        </tr>
                    </tbody>
                </table>
                <?php
                echo "<div class=\"card\" style=\"box-shadow:0px 3px 3px #ddd;\"><div class=\"card-body\"><pre><code class=\"" . $language_name[$slanguage] . ";\">";
                if ($ok != true)
                    echo $MSG_WARNING_ACCESS_DENIED;
                else
                    echo htmlentities(str_replace("\n\r", "\n", $view_source), ENT_QUOTES, "utf-8") . "\n" . $auth;
                echo "</div></div></code></pre>";
                ?>
            </div>
        </div>
    </div>
    <?php require("./template/bshark/footer.php"); ?>
    <?php require("./template/bshark/footer-files.php"); ?>

    <script>
        var judge_result = [<?php
        foreach ($judge_result as $result) {
            echo "'$result',";
        }
        ?> ''];
        var color = [<?php
        foreach ($color as $result) {
            echo "'$result',";
        }
        ?> ''];
        var fresh_res = <?php echo $sresult; ?>;
        var is_look = 1;
        if (is_look == 0) fresh_res = '----';
        if (is_look == 1) {
            document.getElementById("status_show").innerHTML = judge_result[fresh_res];
            document.getElementById("stime").innerHTML = stime + '';
            document.getElementById("smemory").innerHTML = smemory + '';
            document.getElementById("status_show").className = "badge badge-" + color[fresh_res];
        }
        else {
            document.getElementById("status_show").innerHTML = "----";
            document.getElementById("stime").innerHTML = "----";
            document.getElementById("smemory").innerHTML = "----";
            document.getElementById("status_show").className = "badge badge-secondary";
        }
        var out = setInterval(getData, 500);
        function getData() {
            if (fresh_res >= 4) window.clearInterval(out);
            $.ajax({
                type: "GET",
                url: "status-ajax.php",
                data: "solution_id=<?php echo $id; ?>",
                success: function (msg) {
                    //alert(msg);
                    fresh_res = msg.split(",")[0];
                    stime = msg.split(",")[2];
                    smemory = msg.split(",")[1];
                    if (is_look == 1) {
                        document.getElementById("status_show").innerHTML = judge_result[fresh_res];
                        document.getElementById("stime").innerHTML = stime + '';
                        document.getElementById("smemory").innerHTML = smemory + '';
                        document.getElementById("status_show").className = "ui label " + color[fresh_res];
                    }
                    else {
                        document.getElementById("status_show").innerHTML = "----";
                        document.getElementById("stime").innerHTML = "----";
                        document.getElementById("smemory").innerHTML = "----";
                        document.getElementById("status_show").className = "ui label";
                    }
                    //alert(fresh_res);
                }
            });
        }
    </script>
</body>

</html>