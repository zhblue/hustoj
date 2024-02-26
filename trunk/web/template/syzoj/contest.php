<?php $show_title="Contest".$view_cid." - ".$view_title." - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<style>
.ui.label.pointing.below.left::before {
    left: 12%;
}

.ui.label.pointing.below.right::before {
    left: 88%;
}

.ui.label.pointing.below.left {
    margin-bottom: 0;
}

.ui.label.pointing.below.right {
    margin-bottom: 0;
    float: right;
}

#back_to_contest {
    display: none;
}
</style>

<div class="padding">
    <h1>Contest<?php echo $view_cid?> - <?php echo $view_title ?></h1>
    <div class="ui pointing below left label"><?php echo $view_start_time?></div>
    <div class="ui pointing below right label"><?php echo $view_end_time?></div>

    <div id="timer-progress" class="ui tiny indicating progress success" data-percent="50">
        <div class="bar" style="width: 0%; transition-duration: 300ms;"></div>
    </div>

    <div class="ui grid">
        <div class="row">
            <div class="column">
                <div class="ui buttons">
                    <a class="ui small blue button" href="contestrank.php?cid=<?php echo $view_cid?>">ACM<?php echo $MSG_STANDING?></a>
                    <a class="ui small yellow button" href="contestrank-oi.php?cid=<?php echo $view_cid?>">OI<?php echo $MSG_STANDING?></a>
                    <a class="ui small positive button" href="status.php?cid=<?php echo $view_cid?>"><?php echo $MSG_STATUS?></a>
                    <!-- <a class="ui small pink button" href="conteststatistics.php?cid=<?php echo $view_cid?>"><?php echo $MSG_STATISTICS?></a> -->
                </div>
                <div class="ui buttons right floated">

                    <?php
          if ($now>$end_time)
          echo "<span class=\"ui small button grey\">$MSG_Ended</span>";
          else if ($now<$start_time)
          echo "<span class=\"ui small button red\">$MSG_Contest_Pending</span>";
          else
          echo "<span class=\"ui small button green\">$MSG_Running</span>";
          ?>
                    <?php
          if ($view_private=='0')
          echo "<span class=\"ui small button blue\">$MSG_Public</span>";
          else
          echo "<span class=\"ui small button pink\">$MSG_Private</span>";
          ?>
        <?php if(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_m'.$cid ])) {?>
          <a href="suspect_list.php?cid=<?php echo $view_cid?>" class="ui small blue button"><?php echo $MSG_IP_VERIFICATION?></a>
          <a href="user_set_ip.php?cid=<?php echo $view_cid?>" class="ui small green button"><?php echo $MSG_SET_LOGIN_IP?></a>
          <a target="_blank" href="../../admin/contest_edit.php?cid=<?php echo $view_cid?>" class="ui small red button"><?php echo "EDIT"?></a>
        <?php } ?>

                    <span class="ui small button"><?php echo $MSG_Server_Time ?>:<span id=nowdate><?php echo date("Y-m-d H:i:s")?></span></span>
                </div>
            </div>
        </div>
        <?php if($view_description){ ?>
        <div class="row">
            <div class="column">
                <h4 class="ui top attached block header"><?php echo $MSG_Contest_Infomation ?></h4>
                <div class="ui bottom attached segment font-content">
                    <?php echo $view_description?>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="row">
            <div class="column">
                <table class="ui selectable celled table">
                    <thead>
                        <tr>
                            <th class="one wide" style="text-align: center">
                                    <?php if(isset($_SESSION[$OJ_NAME.'_'.'user_id'])) echo "状态" ?>
                            </th>
                            <th class="two wide" style="text-align: center"><?php echo $MSG_PROBLEM_ID ?></th>
                            <th><?php echo $MSG_TITLE ?></th>
                            <!-- <th><?php //echo $MSG_SOURCE ?></th>  -->
                            <th class="one wide center aligned"><?php echo $MSG_AC ?></th>
                            <th class="one wide center aligned"><?php echo $MSG_SUBMIT_NUM ?></th>
                        </tr>
                    </thead>
                    <tbody>
                   
                        <?php
                        foreach($view_problemset as $row){
                          echo "<tr>";
                          foreach($row as $table_cell){
                            echo "<td>".$table_cell."</td>";
                          }
                          echo "</tr>";
                        }
                        ?>
                       
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    $('#timer-progress').progress({
        value: Date.now() / 1000 - <?php echo strtotime($view_start_time)?>,
        total: <?php echo (strtotime($view_end_time)- strtotime($view_start_time))?>
    });
});

$(function() {
    setInterval(function() {
        $('#timer-progress').progress({
            value: Date.now() / 1000 - <?php echo strtotime($view_start_time)?>,
            total: <?php echo (strtotime($view_end_time)- strtotime($view_start_time))?>
        });
    }, 5000);
});
</script>
<script src="include/sortTable.js"></script>
<script>
var diff = new Date("<?php echo date("Y/m/d H:i:s")?>").getTime() - new Date().getTime();
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
    n = y + "-" + mon + "-" + d + " " + (h >= 10 ? h : "0" + h) + ":" + (m >= 10 ? m : "0" + m) + ":" + (s >= 10 ? s :
        "0" + s);
    //alert(n);
    document.getElementById('nowdate').innerHTML = n;
    setTimeout("clock()", 1000);
}
clock();
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
