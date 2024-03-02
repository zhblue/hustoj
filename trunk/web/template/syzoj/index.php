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
                        }else{
                            echo "check database connection or account ! ";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
<?php
/* 本月之星  */
$month_id=mysql_query_cache("select solution_id from solution where  in_date<date_add(curdate(),interval -day(curdate())+1 DAY) order by solution_id desc limit 1;");
if(is_array( $month_id) && isset($month_id[0][0]) ) $month_id=$month_id[0][0];else $month_id=0;
if($NOIP_flag[0]==0)$view_month_rank=mysql_query_cache("select user_id,nick,count(distinct(problem_id)) ac from solution where solution_id>$month_id and problem_id>0 and result=4 group by user_id,nick order by ac desc limit 10");
            if ( is_array($view_month_rank) ) {
        ?>
            <h4 class="ui top attached block header"><i class="ui star icon"></i><?php echo "本月之星"?></h4>
            <div class="ui bottom attached segment">
                <table class="ui very basic center aligned table" style="table-layout: fixed; ">
                    <tbody>
        <?php
                            foreach ( $view_month_rank as $row ) {
                                    echo "<tr>".
                                            "<td><a target='_blank' href='userinfo.php?user=".htmlentities($row[0],ENT_QUOTES,"UTF-8")."'>".htmlentities($row[0],ENT_QUOTES,"UTF-8")."</a></td>".
                                            "<td>".($row[1])."</td>".
                                            "<td>".($row[2])."</td>".
                                            "</tr>";
                            }
        ?>
                    </tbody>
                </table>
            </div>
        <?php
            }
/* 本月之星  */
?>

            <h4 class="ui top attached block header"><i class="ui star icon"></i><?php echo $OJ_INDEX_NEWS_TITLE;?></h4>
            <div class="ui bottom attached segment">
                <table class="ui very basic left aligned table" style="table-layout: fixed; ">
                    <tbody>

                        <?php
                        $sql_news = "select * FROM `news` WHERE `defunct`!='Y' AND `title`='$OJ_INDEX_NEWS_TITLE' ORDER BY `importance` ASC,`time` DESC LIMIT 10";
                        $result_news = mysql_query_cache( $sql_news );
                        if ( $result_news ) {
                            foreach ( $result_news as $row ) {
                                echo "<tr>"."<td>"
                                    .bbcode_to_html($row["content"])."</td></tr>";
                            }
                        }
                        ?>
                         <tr><td>
                                <center> Recent submission :
                                        <?php echo $speed?> .
                                        <div id=submission style="width:80%;height:300px"></div>
                                </center>

                        </td></tr>

                    </tbody>
                </table>
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
                        // 未解之谜
                        if(isset($_SESSION[$OJ_NAME."_user_id"])) $user_id=$_SESSION[$OJ_NAME."_user_id"]; else $user_id='guest';
                        $sql_problems = "select p.problem_id,title,in_date from (select problem_id,min(result) best from solution
                                where user_id=? and result>=4 and problem_id>0 group by problem_id ) s inner join problem p on s.problem_id=p.problem_id
                             where s.best>4 order by p.problem_id  LIMIT 5";
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

  <script language="javascript" type="text/javascript" src="<?php echo $OJ_CDN_URL?>include/jquery.flot.js"></script>
        <script type="text/javascript">
                $( function () {
                        var d1 = <?php echo json_encode($chart_data_all)?>;
                        var d2 = <?php echo json_encode($chart_data_ac)?>;
                        $.plot( $( "#submission" ), [ {
                                label: "<?php echo $MSG_SUBMIT?>",
                                data: d1,
                                lines: {
                                        show: true
                                }
                        }, {
                                label: "<?php echo $MSG_SOVLED?>",
                                data: d2,
                                bars: {
                                        show: true
                                }
                        } ], {
                                grid: {
                                        backgroundColor: {
                                                colors: [ "#fff", "#eee" ]
                                        }
                                },
                                xaxis: {
                                        mode: "time" //,
                                                //max:(new Date()).getTime(),
                                                //min:(new Date()).getTime()-100*24*3600*1000
                                }
                        } );
                } );
                //alert((new Date()).getTime());
        </script>

