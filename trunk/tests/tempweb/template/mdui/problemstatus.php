<!DOCTYPE html>

<html lang="cn">

<head>
    <?php $page_title = '修改信息: '.$user; ?>
    <?php include('_includes/head.php'); ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <div class="jumbotron">
            <center>
                <h1><?php echo "题目: ".$id." 提交统计"; ?></h1>
                <table class="mdui-table" align=center width=70%>
                    <tr>
                        <td>
                            <table id="statics" class="table-hover table-striped" align=center width=90%>
                                <?php
                                $cnt = 0;
                                foreach ($view_problem as $row) {
                                    if ($cnt)
                                        echo "<tr class='oddrow'>";
                                    else
                                        echo "<tr class='evenrow'>";
                                    $i=1;
                                    foreach ($row as $table_cell) {
                                        if ($i==1)
                                            echo "<td class='text-center'>";
                                        else
                                            echo "<td class='text-right'>";

                                        echo $table_cell;
                                        echo "</td>";
                                        $i++;
                                    }
                                    echo "</tr>";
                                    $cnt = 1-$cnt;
                                }
                                ?>

                                <tr id=pie bgcolor=white>
                                    <td colspan=2>
                                        <center>
                                            <div id='PieDiv' style='position:relative; height:150px; width:200px;'>
                                            </div>
                                        </center>
                                    </td>
                                </tr>
                            </table>
                        </td>

                        <td>
                            <table id=problemstatus class="table-hover table-striped" align=center width=100%>
                                <thead>
                                    <tr class=toprow>
                                        <th style="cursor:hand" onclick="sortTable('problemstatus', 0, 'int');"
                                            class="text-center" width=10%>
                                            <?php echo $MSG_Number?>
                                        </th>
                                        <th class="text-center" width=10%>
                                            <?php echo $MSG_RUNID?>
                                        </th>
                                        <th class="text-center" width=15%>
                                            <?php echo $MSG_USER?>
                                        </th>
                                        <th class="text-center" width=10%>
                                            <?php echo $MSG_MEMORY?>
                                        </th>
                                        <th class="text-center" width=10%>
                                            <?php echo $MSG_TIME?>
                                        </th>
                                        <th class="text-center" width=10%>
                                            <?php echo $MSG_LANG?>
                                        </th>
                                        <th class="text-center" width=10%>
                                            <?php echo $MSG_CODE_LENGTH?>
                                        </th>
                                        <th class="text-center" width=20%>
                                            <?php echo $MSG_SUBMIT_TIME?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cnt = 0;
                                    foreach ($view_solution as $row) {
                                        if ( $cnt )
                                            echo "<tr class='oddrow'>";
                                        else
                                            echo "<tr class='evenrow'>";
                                        
                                        $i = 1;
                                        foreach ($row as $table_cell) {
                                            if ($i==1 || $i==8)
                                                echo "<td class='text-center'>";
                                            else if ($i==2 || $i==4 || $i==5 || $i==6  || $i==7)
                                                echo "<td class='text-right'>";
                                            else
                                                echo "<td>";

                                            echo $table_cell;
                                            echo "&nbsp";
                                            echo "</td>";
                                            $i++;
                                        }

                                        echo "</tr>";
                                        $cnt = 1-$cnt;
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <br>

                            <center>
                                <?php
                            echo "<a href='problemstatus.php?id=$id'>[TOP]</a>";
                            //echo "&nbsp;&nbsp;<a href='status.php?problem_id=$id'>[STATUS]</a>";
                            
                            if ($page>$pagemin) {
                                $page--;
                                echo "&nbsp;&nbsp;<a href='problemstatus.php?id=$id&page=$page'>[PREV]</a>";
                                $page++;
                            }

                            if ($page<$pagemax) {
                                $page++;
                                echo "&nbsp;&nbsp;<a href='problemstatus.php?id=$id&page=$page'>[NEXT]</a>";
                                $page--;
                            }
                            ?>
                            </center>

                        </td>
                    </tr>
                </table>
            </center>

            <script type="text/javascript" src="include/wz_jsgraphics.js"></script>
            <script type="text/javascript" src="include/pie.js"></script>
            <script language="javascript">
            var y = new Array();
            var x = new Array();
            var dt = document.getElementById("statics");
            var data = dt.rows;
            var n;
            for (var i = 3; dt.rows[i].id != "pie"; i++) {
                x.push(dt.rows[i].cells[0].innerHTML);
                n = dt.rows[i].cells[1];
                n = n.innerText || n.textContent;
                //alert(n);
                n = parseInt(n);
                y.push(n);
            }
            var mypie = new Pie("PieDiv");
            mypie.drawPie(y, x);
            //mypie.clearPie();
            </script>

        </div>
    </div>

    <!-- /container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>
    <script type="text/javascript" src="include/jquery.tablesorter.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $("#problemstatus").tablesorter();
    });
    </script>
</body>

</html>