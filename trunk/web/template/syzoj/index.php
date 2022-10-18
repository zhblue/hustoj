<?php $show_title="首页 - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<div class="padding">
    <div class="ui three column grid">
        <div class="eleven wide column">
            <h4 class="ui top attached block header"><i class="ui info icon"></i><?php echo $MSG_NEWS;?></h4>
            <div class="ui bottom attached segment">
                <table class="ui very basic table">
                    <thead>
                        <tr>
                            <th><?php echo $MSG_TITLE;?></th>
                            <th><?php echo $MSG_TIME;?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql_news = "select * FROM `news` WHERE `defunct`!='Y' AND `title`!='faqs.cn' ORDER BY `importance` ASC,`time` DESC LIMIT 10";
                        $result_news = mysql_query_cache( $sql_news );
                        if ( $result_news ) {
                            foreach ( $result_news as $row ) {
                                echo "<tr>"."<td>"
                                    ."<a href=\"viewnews.php?id=".$row["news_id"]."\">"
                                    .$row["title"]."</a></td>"
                                    ."<td>".$row["time"]."</td>"."</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <h4 class="ui top attached block header"><i class="ui signal icon"></i><?php echo $MSG_RANKLIST;?></h4>
            <div class="ui bottom attached segment">
                <table class="ui very basic center aligned table" style="table-layout: fixed; ">
                    <thead>
                        <tr>
                            <th style="width: 50px; ">#</th>
                            <th style="width: 170px; "><?php echo $MSG_USER_ID;?></th>
                            <th><?php echo $MSG_SIGN ;?></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $sql_users = "select * FROM `users` where user_id not in (".$OJ_RANK_HIDDEN.") and defunct='N' ORDER BY `solved` DESC LIMIT 15";
                        $result_users = mysql_query_cache( $sql_users );
                        if ( $result_users ) {
                            $i = 1;
                            foreach ( $result_users as $row ) {
                                echo "<tr>"."<td>".$i++."</td>"."<td>"
                                    ."<a href=\"userinfo.php?user=".$row["user_id"]."\">"
                                    .$row["user_id"]."</a></td>"
                                    ."<td>".$row["school"]."</td>"."</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
                  <h4 class="ui top attached block header"><i class="ui beer icon"></i>视频推送</h4>
            <div class="ui bottom attached segment">
                <div class="panel-body content" id="data_ref_vedio">
<body>
<div class="Dplayer_box">
    <div class="player_av">
        <div id="player1"></div>
    </div>
</div>
<script src="/template/syzoj/js/DPlayer.min.js"></script>
<script>
   
    var dp = new DPlayer({
        
        element: document.getElementById('player1'),
        video: {
              url: 'https://noiresources.ccf.org.cn/NOI2022%E5%AE%A3%E4%BC%A0%E7%89%87.mp4',
               pic: 'image/video.png',
        },
        
        
    });
</script>
</body>
</html>
					<!--<embed src="http://47.110.135.197/movie/demo1.mp4" width="650" height="357.5" autostart="false" loop="false">-->
					</div>
            </div>
        </div>
        <div class="right floated five wide column">
            <h4 class="ui top attached block header"><i class="ui rss icon"></i> <?php echo $MSG_RECENT_PROBLEM;?> </h4>
            <div class="ui bottom attached segment">
                <table class="ui very basic center aligned table">
                    <thead>
                        <tr>
                            <th width="60%"><?php echo $MSG_TITLE;?></th>
                            <th width="40%"><?php echo $MSG_TIME;?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $sql_problems = "select * FROM `problem` where defunct='N' ORDER BY `problem_id` DESC LIMIT 5";
                        $result_problems = mysql_query_cache( $sql_problems );
                        if ( $result_problems ) {
                            $i = 1;
                            foreach ( $result_problems as $row ) {
                                echo "<tr>"."<td>"
                                    ."<a href=\"problem.php?id=".$row["problem_id"]."\">"
                                    .$row["title"]."</a></td>"
                                    ."<td>".substr($row["in_date"],0,10)."</td>"."</tr>";
                            }
                        }
                    ?>
                    </tbody>
                </table>
            </div>
            <h4 class="ui top attached block header"><i class="ui search icon"></i><?php echo $MSG_SEARCH;?></h4>
            <div class="ui bottom attached segment">
                <form action="problem.php" method="get">
                    <div class="ui search" style="width: 100%; ">
                        <div class="ui left icon input" style="width: 100%; ">
                            <input class="prompt" style="width: 100%; " type="text" placeholder="<?php echo $MSG_PROBLEM_ID ;?> …" name="id">
                            <i class="search icon"></i>
                        </div>
                        <div class="results" style="width: 100%; "></div>
                    </div>
                </form>
            </div>
            <h4 class="ui top attached block header"><i class="ui calendar icon"></i><?php echo $MSG_RECENT_CONTEST ;?></h4>
            <div class="ui bottom attached center aligned segment">
                <table class="ui very basic center aligned table">
                    <thead>
                        <tr>
                            <th><?php echo $MSG_CONTEST_NAME;?></th>
                            <th><?php echo $MSG_START_TIME;?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $sql_contests = "select * FROM `contest` where defunct='N' ORDER BY `contest_id` DESC LIMIT 5";
                        $result_contests = mysql_query_cache( $sql_contests );
                        if ( $result_contests ) {
                            $i = 1;
                            foreach ( $result_contests as $row ) {
                                echo "<tr>"."<td>"
                                    ."<a href=\"contest.php?cid=".$row["contest_id"]."\">"
                                    .$row["title"]."</a></td>"
                                    ."<td>".$row["start_time"]."</td>"."</tr>";
                            }
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include("template/$OJ_TEMPLATE/footer.php");?>
