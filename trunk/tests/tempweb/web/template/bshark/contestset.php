<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        <?php echo $MSG_CONTEST; ?> - <?php echo $OJ_NAME; ?>
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
                        <h2 class="ui header">
                            <?php echo $MSG_CONTEST; ?>
                        </h2>
                        <?php if (isset($_GET["my"])) { ?>
                            <a class="ui button primary" href="contest.php">
                                <?php echo $MSG_VIEW_ALL_CONTESTS ?>
                            </a>
                        <?php } ?>
                        <table class="ui single line fluid compact table">
                            <thead>
                                <tr>
                                    <th>
                                        <?php echo $MSG_CONTEST_ID ?>
                                    </th>
                                    <th>
                                        <?php echo $MSG_CONTEST_NAME ?>
                                    </th>
                                    <th>
                                        <?php echo $MSG_CONTEST_STATUS ?>
                                    </th>
                                    <th>
                                        <?php echo $MSG_CONTEST_OPEN ?>
                                    </th>
                                    <th>
                                        <?php echo $MSG_CONTEST_CREATOR ?>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($view_contest as $row) {
                                    echo "<tr>";
                                    foreach ($row as $table_cell) {
                                        echo "<td>";
                                        echo "\t" . $table_cell;
                                        echo "</td>";
                                    }
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <div class="ui container center aligned">
                            <div class="ui borderless tiny menu pagination">
                                <a class="icon item" href="contest.php?page=1"><i
                                        class="angle double left icon"></i></a>
                                <?php
                                if (!isset($page))
                                    $page = 1;
                                $page = intval($page);
                                $section = 8;
                                $start = $page > $section ? $page - $section : 1;
                                $end = $page + $section > $view_total_page ? $view_total_page : $page + $section;
                                for ($i = $start; $i <= $end; $i++) {
                                    echo "<a class='" . ($page == $i ? "active " : "") . "item' href='contest.php?page=" . $i . (isset($_GET['my']) ? "&my" : "") . "'>" . $i . "</a>";
                                }
                                ?>
                                <a class="item" href="contest.php?page=<?php echo $view_total_page ?>"><i
                                        class="angle double right icon"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="five wide column">
                <div class="card">
                    <div class="card-body">
                        <h2 class="ui header">现在时间</h2>
                        <span id=nowdate></span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h2 class="ui header">
                            <?php echo $MSG_SEARCH; ?>
                        </h2>
                        <form method=post action=contest.php class="form-inline">
                            <div class="ui action fluid input">
                                <input type="text" name="keyword"
                                    value="<?php if (isset($_POST['keyword']))
                                        echo htmlentities($_POST['keyword'], ENT_QUOTES, "UTF-8") ?>"
                                        placeholder="<?php echo $MSG_CONTEST_NAME ?>">
                                <button type=submit class="ui button">
                                    <?php echo $MSG_SEARCH; ?>
                                </button>
                            </div>
                        </form>
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