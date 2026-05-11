<?php
$calsed = '锦鲤';
$calledid = -1;
$acneed = [10, 20, 30, 50, 80, 100, 200, 300, 500, 800, 1000];
$accall = ["萌新", "小小牛", "小牛", "小犇", "中牛", "中犇", "大牛", "大犇", "神牛", "神犇"];
for ($i = count($accall); $i > 0; $i--) {
    if ($AC < $acneed[$i]) {
        $calsed = $accall[$i - 1];
        $calledid = $i - 1;
    }
}
for ($i = 0; $i <= 11; ++$i) {
    $ped[$i] = 0;
}
$sql = "SELECT * FROM `solution` WHERE `user_id`=?";
$result = pdo_query($sql, $user);
foreach ($result as $row) {
    ++$ped[$row['result']];
} ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        <?php echo $user; ?> - <?php echo $MSG_USERINFO; ?> - <?php echo $OJ_NAME; ?>
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
                        <h2 style="margin: 0">
                            <?php echo $MSG_SUBMIT . $MSG_STATISTICS; ?>
                        </h2>
                        <?php if ($aflag == 0) { ?><canvas id="myChart"></canvas>
                        <?php } else
                            echo "OI比赛进行中，暂时无法查看"; ?>
                        <table class="ui very basic table" style="width:100%">
                            <tbody>
                                <tr>
                                    <th width=30%>
                                        <?php echo $MSG_SUBMIT; ?>
                                    </th>
                                    <td width=70%><a href='./status.php?user_id=<?php echo $user ?>'>
                                            <?php echo $Submit; ?>
                                        </a></td>
                                </tr>
                                <tr>
                                    <th width=30%>
                                        <?php echo $MSG_SOVLED; ?>
                                    </th>
                                    <td width=70%><?php echo $AC; ?></td>
                                </tr>
                                <?php
                                foreach ($view_userstat as $row) {
                                    //i++;
                                    echo "<tr><th>" . $jresult[$row[0]] . "</th><td><a href=./status.php?user_id=$user&jresult=" . $row[0] . " >";
                                    if ($aflag == 0)
                                        echo $row[1];
                                    else
                                        echo '---';
                                    echo "</a></td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="five wide column">
                <div class="card">
                    <div class="card-body" align=center>
                        <img src='<?php echo "http://www.gravatar.com/avatar/".md5(strtolower(trim($email)))."?size=100"; ?>'
                            style="width:100px;height:100px;border-radius:50%;margin-bottom: 1em">
                        <h3><span>
                                <?php echo $user; ?>
                            </span></h3>
                        <div class="ui three column grid">
                            <div class="column">
                                <h1><?php echo $AC; ?></h1>
                                <?php echo $MSG_SOVLED; ?>
                            </div>
                            <div class="column">
                                <h1>
                                    <?php echo $Submit; ?>
                                </h1><?php echo $MSG_SUBMIT; ?>
                            </div>
                            <div class="column">
                                <h1>
                                    <?php echo $Rank; ?>
                                </h1><?php echo $MSG_Number; ?>
                            </div>
                        </div>
                        <br>
                        <table class="ui very basic table" style="width:100%">
                            <tbody>
                                <tr>
                                    <th width=30%>等级</th>
                                    <td width=20%>
                                        <?php echo $calsed; ?>
                                    </td>
                                    <td width=50%>距离<?php echo $accall[$calledid + 1]; ?>还需AC <?php echo $acneed[$calledid + 1] - $AC; ?>题</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h5>
                            <?php echo $MSG_USERINFO; ?>
                        </h5>
                        <table class="ui very basic table" style="width:100%">
                            <tbody>
                                <tr>
                                    <th><?php echo $MSG_USER_ID; ?></th>
                                    <td><span>
                                            <?php echo $user; ?>
                                        </span></td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php echo $MSG_NICK; ?>
                                    </th>
                                    <td><span><?php echo $nick; ?></span></td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php echo $MSG_EMAIL; ?>
                                    </th>
                                    <td><?php echo $email; ?></td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php echo $MSG_SCHOOL; ?>
                                    </th>
                                    <td><?php echo $school; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </script>

    <!-- /body -->
    <!-- /post -->
    <!-- footer -->
    <script>
        var ctx = document.getElementById("myChart").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [<?php echo '"' . $MSG_AC . '", "' . $MSG_WA . '", "' . $MSG_RE . '", "' . $MSG_CE . '", "' . $MSG_TLE . '", "' . $MSG_MLE . '", "' . $MSG_OLE . '"'; ?>],
            datasets: [{
                label: '# of Votes',
                data: [<?php echo $ped[4] . "," . ($ped[6]) . "," . $ped[10] . "," . $ped[11] . "," . $ped[7] . "," . $ped[8] . "," . $ped[5]; ?>],
            backgroundColor: [
                '#4ACF50',
                '#DB2828',
                '#9966FF',
                '#FFCD56',
                '#FF9F40',
                '#36A3EB',
                '#C9CBCF'
            ],
        }]
        },
        options: {
            responsive: true
        }
    });
    </script>
    <?php require("./template/bshark/footer.php"); ?>
    <?php require("./template/bshark/footer-files.php"); ?>
</body>

</html>