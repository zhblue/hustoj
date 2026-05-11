<!DOCTYPE html>
<html lang="cn">

<head>
    <?php $page_title = '用户信息: '.$user; ?>
    <?php include('_includes/head.php'); ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <div class="jumbotron">
            <h1>用户信息</h1>
            <div class="mdui-row-md-2 mdui-row-sm-1 mdui-m-y-2">
                <table class="mdui-table mdui-table-hoverable mdui-col" id="statics">
                    <tbody>
                        <tr>
                            <td>排名</td>
                            <td><?php echo $Rank; ?></td>
                        </tr>
                        <tr>
                            <td><?php echo $MSG_SOVLED?></td>
                            <td><a href="status.php?user_id=<?php echo $user; ?>&jresult=4"><?php echo $AC; ?></a></td>
                        </tr>
                        <tr>
                            <td><?php echo $MSG_SUBMIT; ?></td>
                            <td><a href="status.php?user_id=<?php echo $user?>"><?php echo $Submit; ?></a></td>
                        </tr>
                        <?php
                            foreach($view_userstat as $row){
                                echo '<tr>'
                                    .'<td>'
                                    .$jresult[$row[0]]
                                    .'</td>'
                                    .'<td>'
                                    .'<a href="status.php?user_id='.$user.'&jresult='.$row[0].'">'
                                    .$row[1]
                                    .'</a>'
                                    .'</td>'
                                    .'</tr>';
                            }
                        ?>
                        <tr id="pie">
                            <td>统计</td>
                            <td><div id="PieDiv" style="position: relative; height: 105px; width: 120px;"></div></td>
                        </tr>
                        <script src="include/wz_jsgraphics.js"></script>
                        <script src="include/pie.js"></script>
                        <script>
                            var y = new Array();
                            var x = new Array();
                            var dt = document.getElementById("statics");
                            var data = dt.rows;
                            var n;
                            for (var i = 3; dt.rows[i].id != "pie"; i++) {
                                n = dt.rows[i].cells[0];
                                n = n.innerText || n.textContent;
                                x.push(n);
                                n = dt.rows[i].cells[1].firstChild;
                                n = n.innerText || n.textContent;
                                //alert(n);
                                n = parseInt(n);
                                y.push(n);
                            }
                            var mypie = new Pie("PieDiv");
                            mypie.drawPie(y, x);
                            //mypie.clearPie();
                        </script>
                        <tr>
                            <td>个性签名</td>
                            <td><?php echo $school; ?></td>
                        </tr>
                        <tr>
                            <td>邮箱</td>
                            <td><!-- [email protected] --><?php echo $email; ?></td>
                        </tr>
                    </tbody>
                </table>
                <div class="mdui-card mdui-col" style="min-height: 300px;">
                    <div class="mdui-card-primary">
                        <div class="mdui-card-primary-title">通过题目</div>
                    </div>
                    <div id="submission" class="mdui-card-content"> </div>
                    <script>
                    function p(id, c) {
                        $('#submission').append(`<a href="problem.php?id=${id}">${id}</a>&nbsp;<sup>(<a href="status.php?user_id=<?php echo $user; ?>&problem_id=${id}"'>${c}</a>)</sup>`);
                    }
                    <?php 
                        $sql = "SELECT `problem_id`, count(1) from solution where `user_id`=? and result=4 group by `problem_id` ORDER BY `problem_id` ASC";
                        if ($result = pdo_query($sql,$user)) { 
                            foreach($result as $row)
                            echo "p($row[0],$row[1]);";
                        } 
                    ?>
                    </script>
                </div>
            </div>
            <?php if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])) { ?>
            <h1>登录记录</h1>
            <table class="mdui-table mdui-table-hoverable">
                <thead>
                    <tr>
                        <th>UserID</th>
                        <th>Password</th>
                        <th>IP</th>
                        <th>Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                            $cnt=0;
                            foreach($view_userinfo as $row){
                                if ($cnt)
                                    echo "<tr class='oddrow'>";
                                else
                                    echo "<tr class='evenrow'>";
                                for($i=0;$i<count($row)/2;$i++){
                                    echo "<td>".$row[$i]."</td>";
                                }
                                echo "</tr>";
                                $cnt=1-$cnt;
                            }
                        ?>
                </tbody>
            </table>
            <?php } ?>
        </div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>

    <script language="javascript" type="text/javascript" src="include/jquery.flot.js"></script>
    <script type="text/javascript">
    $(function() {
        var d1 = [];
        var d2 = [];
        <?php
foreach($chart_data_all as $k=>$d){
?>
        d1.push([<?php echo $k?>, <?php echo $d?>]);
        <?php }?>
        <?php
foreach($chart_data_ac as $k=>$d){
?>
        d2.push([<?php echo $k?>, <?php echo $d?>]);
        <?php }?>
        //var d2 = [[0, 3], [4, 8], [8, 5], [9, 13]];
        // a null signifies separate line segments
        var d3 = [
            [0, 12],
            [7, 12], null, [7, 2.5],
            [12, 2.5]
        ];
        $.plot($("#submission"), [{
                label: "<?php echo $MSG_SUBMIT?>",
                data: d1,
                lines: {
                    show: true
                }
            },
            {
                label: "<?php echo $MSG_AC?>",
                data: d2,
                bars: {
                    show: true
                }
            }
        ], {
            xaxis: {
                mode: "time"
                //, max:(new Date()).getTime()
                //,min:(new Date()).getTime()-100*24*3600*1000
            },
            grid: {
                backgroundColor: {
                    colors: ["#fff", "#333"]
                }
            }
        });
    });
    //alert((new Date()).getTime());
    </script>
</body>

</html>