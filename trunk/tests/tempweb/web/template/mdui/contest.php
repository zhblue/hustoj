<!DOCTYPE html>
<html lang="cn">

<head>
    <?php $page_title = $view_cid.' '.$view_title; ?>
    <?php include('_includes/head.php'); ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
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

        <table id="problemset" class="mdui-table mdui-table-hoverable mdui-m-y-3" width="90%">
            <thead>
                <tr>
                    <th></th>
                    <th style="cursor: hand;" onclick="sortTable('problemset', 1, 'int');">题目编号</th>
                    <th>标题</th>
                 <!--    <th>分类</th>  -->
                    <th style="cursor: hand;" onclick="sortTable('problemset', 4, 'int');">通过</th>
                    <th style="cursor: hand;" onclick="sortTable('problemset', 5, 'int');">提交</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $cnt=0;
                    foreach ($view_problemset as $row) {
                        echo "<tr>";
                        
                        foreach ($row as $table_cell) {
                            echo "<td>";
                            echo "\t".$table_cell;
                            echo "</td>";
                        }
                        echo "</tr>";
                        $cnt=1-$cnt;
                    }
                ?>
            </tbody>
        </table>
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
    <script>
        //table sort from http://dennis-zane.javaeye.com/blog/58864
        //类型转换器，将列的字段类型转换为可以排序的类型：String,int,float
        function convert(sValue, sDataType) {
            switch(sDataType) {
                case "int":
                    return parseInt(sValue);
                case "float":
                    return parseFloat(sValue);
                case "date":
                    return new Date(Date.parse(sValue));
                default:
                    return sValue.toString();
            
            }
        }
        
        //排序函数产生器，iCol表示列索引，sDataType表示该列的数据类型
        function generateCompareTRs(iCol, sDataType) {
    
            return  function compareTRs(oTR1, oTR2) {
                        var td1=oTR1.cells[iCol].firstChild;
                        var td2=oTR2.cells[iCol].firstChild;
                        
                        td1=td1.innerText || td1.textContent;
                        td2=td2.innerText || td2.textContent;
                        

                        var vValue1 = convert(td1, sDataType);
                        var vValue2 = convert(td2, sDataType);
    
                        if (vValue1 < vValue2) {
                            return -1;
                        } else if (vValue1 > vValue2) {
                            return 1;
                        } else {
                            return 0;
                        }
                    };
        }
        
        //排序方法
        function sortTable(sTableID, iCol, sDataType) {
            var oTable = document.getElementById(sTableID);
            var oTBody = oTable.tBodies[0];
            var colDataRows = oTBody.rows;
            var aTRs = new Array;
            
            //将所有列放入数组
            for (var i=0; i < colDataRows.length; i++) {
                aTRs[i] = colDataRows[i];
            }
                
            //判断最后一次排序的列是否与现在要进行排序的列相同，是的话，直接使用reverse()逆序
            if (oTable.sortCol == iCol) {
                aTRs.reverse();
            } else {
                //使用数组的sort方法，传进排序函数
                aTRs.sort(generateCompareTRs(iCol, sDataType));
            }
    
            var oFragment = document.createDocumentFragment();
            for (var i=0; i < aTRs.length; i++) {
                if(i%2==0)
                    aTRs[i].className='evenrow';
                else
                    aTRs[i].className='oddrow';
                oFragment.appendChild(aTRs[i]);
            }
    
            oTBody.appendChild(oFragment);
            //记录最后一次排序的列索引
            oTable.sortCol = iCol;
        }

    </script>
</body>

</html>
