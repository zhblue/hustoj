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
    <div class="card" style="margin: 3% 8% 5% 8%">
        <div class="card-body">
            <h2>
                <?php echo $title ?> - <?php echo $MSG_STANDING; ?>(滚榜)
            </h2>
            <a class="ui button blue" href="contestrank.xls.php?cid=<?php echo $cid ?>">下载xls文件</a>
            <table id=rank class="ui center aligned table">
                <thead>
                    <tr class=toprow align=center>
                        <th class="{sorter:'false'}">Rank
                        <th width=10%>User</th>
                        <th>Nick</th>
                        <th>Solved</th>
                        <th>Penalty</th>
                        <?php
                        $rank = 1;
                        for ($i = 0; $i < $pid_cnt; $i++)
                            echo "<th><a href=problem.php?cid=$cid&pid=$i>$PID[$i]</a></th>";
                        echo "</thead>\n<tbody>";
                        if (false)
                            for ($i = 0; $i < $user_cnt; $i++) {
                                echo "<tr>\n";
                                echo "<td>";
                                $uuid = $U[$i]->user_id;
                                $nick = $U[$i]->nick;
                                if ($nick[0] != "*")
                                    echo $rank++;
                                else
                                    echo "*";
                                $usolved = $U[$i]->solved;
                                if (isset($_GET['user_id']) && $uuid == $_GET['user_id'])
                                    echo "<td bgcolor=#ffff77>";
                                else
                                    echo "<td>";
                                echo "<a name=\"$uuid\" href=userinfo.php?user=$uuid>$uuid</a>";
                                echo "<td><a href=userinfo.php?user=$uuid>" . htmlentities($U[$i]->nick, ENT_QUOTES, "UTF-8") . "</a>";
                                echo "<td><a href=status.php?user_id=$uuid&cid=$cid>$usolved</a>";
                                echo "<td>" . sec2str($U[$i]->time);
                                for ($j = 0; $j < $pid_cnt; $j++) {
                                    $bg_color = "eeeeee";
                                    if (isset($U[$i]->p_ac_sec[$j]) && $U[$i]->p_ac_sec[$j] > 0) {
                                        $aa = 0x33 + $U[$i]->p_wa_num[$j] * 32;
                                        $aa = $aa > 0xaa ? 0xaa : $aa;
                                        $aa = dechex($aa);
                                        $bg_color = "$aa" . "ff" . "$aa";
                                        //$bg_color="aaffaa";
                                        if ($uuid == $first_blood[$j]) {
                                            $bg_color = "aaaaff";
                                        }
                                    } else if (isset($U[$i]->p_wa_num[$j]) && $U[$i]->p_wa_num[$j] > 0) {
                                        $aa = 0xaa - $U[$i]->p_wa_num[$j] * 10;
                                        $aa = $aa > 16 ? $aa : 16;
                                        $aa = dechex($aa);
                                        $bg_color = "ff$aa$aa";
                                    }
                                    echo "<td class=well style='background-color:#$bg_color'>";
                                    if (isset($U[$i])) {
                                        if (isset($U[$i]->p_ac_sec[$j]) && $U[$i]->p_ac_sec[$j] > 0)
                                            echo sec2str($U[$i]->p_ac_sec[$j]);
                                        if (isset($U[$i]->p_wa_num[$j]) && $U[$i]->p_wa_num[$j] > 0)
                                            echo "(-" . $U[$i]->p_wa_num[$j] . ")";
                                    }
                                }
                                echo "</tr>\n";
                            }
                        echo "</tbody></table>";
                        ?>
        </div>
    </div>
    <script>
        function getTotal(rows) {
            var total = 0;
            return rows.length - 1;
            for (var i = 0; i < rows.length && total == 0; i++) {
                try {
                    total = parseInt(rows[rows.length - i].cells[0].innerHTML);
                    if (isNaN(total)) total = 0;
                } catch (e) {

                }
            }
            return total;

        }
        function metal() {
            var tb = window.document.getElementById('rank');
            var rows = tb.rows;
            try {
                var total = getTotal(rows);
                //alert(total);
                for (var i = 1; i < rows.length; i++) {
                    var cell = rows[i].cells[0];
                    var acc = rows[i].cells[3];
                    var ac = parseInt(acc.innerHTML);
                    if (isNaN(ac)) ac = parseInt(acc.textContent);


                    if (cell.innerHTML != "*" && ac > 0) {

                        var r = i;
                        if (r == 1) {
                            cell.innerHTML = "Winner";
                            //cell.style.cssText="background-color:gold;color:red";
                            cell.className = "label yellow ui";
                        } else {
                            cell.innerHTML = r;
                        }
                        if (r > 1 && r <= total * .05 + 1)
                            cell.className = "label ui yellow";
                        if (r > total * .05 + 1 && r <= total * .20 + 1)
                            cell.className = "ui label";
                        if (r > total * .20 + 1 && r <= total * .45 + 1)
                            cell.className = "ui label red";
                        if (r > total * .45 + 1 && ac > 0)
                            cell.className = "ui label teal";
                    }
                }
            } catch (e) {
                alert(e);
            }
        }
        metal();
        replay();
<?php if (isset($solution_json))
    echo "var solutions=$solution_json;" ?>
                                    var replay_index = 0;
            function replay() {
                replay_index = 0;
                window.setTimeout("add()", 1000);
            }
            function add() {
                if (replay_index >= solutions.length) return metal();
                var solution = solutions[replay_index];
                var tab = $("#rank");
                var row = findrow(tab, solution);
                if (row == null)
                    tab.append(newrow(tab, solution));
                row = findrow(tab, solution);
                update(tab, row, solution);
                replay_index++;
                sort(tab[0].rows);
                metal();
                window.setTimeout("add()", 5);
            }
            function sec2str(sec) {
                var ret = "";
                if (sec < 36000) ret = "0";
                ret += parseInt(sec / 3600);
                ret += ":";
                if (sec % 3600 / 60 < 10) ret += "0";
                ret += parseInt(sec % 3600 / 60);
                ret += ":";
                if (sec % 60 < 10) ret += "0";
                ret += parseInt(sec % 60);
                return ret;
            }
            function str2sec(str) {
                var s = str.split(":");
                var h = parseInt(s[0]);
                var m = parseInt(s[1]);
                var s = parseInt(s[2]);
                return h * 3600 + m * 60 + s;
            }
            function colorful(td, ac, num) {
                if (num < 0) num = -num; else num = 0;
                num *= 10
                if (num > 255) num = 255;
                if (ac && num > 200) num = 200;
                var rb = ac ? num : 255 - num;
                if (ac) {
                    //	td.className="well green";
                    td.style = "background-color: rgb(" + rb + ",255," + rb + ");";
                } else {
                    td.style = "background-color: rgb(255," + rb + "," + rb + ");";
                }
            }
            function update(tab, row, solution) {
                var col = parseInt(solution["num"]) + 5;
                var old = row.cells[col].innerHTML;
                var time = 0;
                if (old != "") time = parseInt(old);
                if (!(old.charAt(0) == '-' || old == '')) return;
                if (parseInt(solution["result"]) == 4) {
                    if (old.charAt(0) == '-' || old == '') {
                        var pt = time;
                        time = parseInt(solution["in_date"]) - time * 1200;

                        penalty = str2sec(row.cells[4].innerHTML);
                        penalty += time;
                        row.cells[4].innerHTML = sec2str(penalty);
                        row.cells[col].innerHTML = sec2str(parseInt(solution["in_date"]));
                        if (pt != 0)
                            row.cells[col].innerHTML += "(" + pt + ")";
                        colorful(row.cells[col], true, pt);
                    } else {
                        if (row.cells[col].className == "well green");
                    }
                    row.cells[3].innerHTML = parseInt(row.cells[3].innerHTML) + 1;
                } else {
                    time--;
                    row.cells[col].innerHTML = time;
                    colorful(row.cells[col], false, time);
                }
                /*
                if(parseInt(solution["result"])==4){
                     if(row.cells[col].className!="well green"){
                 }
                 row.cells[col].className="well green";
                }else{
                     if(row.cells[col].className!="well green") 
                     row.cells[col].className="well red";
                }
             */
            }
            function sort(rows) {
                for (var i = 1; i < rows.length; i++) {
                    for (var j = 1; j < i; j++) {
                        if (cmp(rows[i], rows[j])) {
                            swapNode(rows[i], rows[j]);
                        }
                    }

                }

            }
            function swapNode(node1, node2) {
                var parent = node1.parentNode;//父节点
                var t1 = node1.nextSibling;//两节点的相对位置
                var t2 = node2.nextSibling;
                $(node1).fadeToggle("slow");
                $(node2).fadeToggle("slow");
                //如果是插入到最后就用appendChild
                if (t1) parent.insertBefore(node2, t1);
                else parent.appendChild(node2);
                if (t2) parent.insertBefore(node1, t2);
                else parent.appendChild(node1);
                $(node1).fadeToggle("slow");
                $(node2).fadeToggle("slow");
            }
            function cmp(a, b) {
                if (parseInt(a.cells[3].innerHTML) > parseInt(b.cells[3].innerHTML))
                    return true;

                if (parseInt(a.cells[3].innerHTML) == parseInt(b.cells[3].innerHTML))
                    return str2sec(a.cells[4].innerHTML) < str2sec(b.cells[4].innerHTML);
            }
            function trim(str) { //删除左右两端的空格
                return str.replace(/(^\s*)|(\s*$)/g, "");
            }
            function newrow(tab, solution) {

                var row = "<tr><td></td><td>" + solution['user_id'] + "</td>";
                row += "<td>" + trim(solution['nick']) + "</td>";
                row += "<td>";
                var css = "grey";
                var time = 0;
                if (solution['result'] == 4) {
                    row += "1";
                    time = solution['in_date'];
                    count = sec2str(time);
                    css = "well green";
                } else {
                    row += "0";
                    css = "well red";
                    count = -1;
                }
                row += "</td>";
                var n = tab[0].rows[0].cells.length;
                row += "<td>" + sec2str(time) + "</td>";

                for (var i = 5; i < n; i++) {
                    if (i - 5 == solution['num'])
                        row += "<td class='" + css + "'>" + count + "</td>";
                    else
                        row += "<td></td>";
                }
                row += "</tr>";
                return row;
            }
            function findrow(tab, solution) {
                var rows = tab[0].rows;
                for (var i = 0; i < rows.length; i++) {
                    if (rows[i].cells[1].innerHTML == solution['user_id'])
                        return rows[i];

                }
                return null;
            }
        </script>
    <?php require("./template/bshark/footer.php"); ?>
    <?php require("./template/bshark/footer-files.php"); ?>
</body>

</html>