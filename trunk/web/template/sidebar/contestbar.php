<?php $view_cid=$cid; ?>
        <div class="row">
            <div class="column">
                <div class="ui buttons">
                    <a class="ui small blue button" href="contestrank.php?cid=<?php echo $view_cid?>">ACM排行榜</a>
                    <a class="ui small yellow button" href="contestrank-oi.php?cid=<?php echo $view_cid?>">OI排行榜</a>
                    <a class="ui small positive button" href="status.php?cid=<?php echo $view_cid?>">提交记录</a>
                    <!-- <a class="ui small pink button" href="conteststatistics.php?cid=<?php echo $view_cid?>">比赛统计</a> -->
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
                    <span class="ui small button"><?php echo $MSG_Server_Time ?>:<span id=nowdate><?php echo date("Y-m-d H:i:s")?></span></span>
                </div>
            </div>
        </div>
