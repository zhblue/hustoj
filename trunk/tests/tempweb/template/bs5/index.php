<?php $show_title="$MSG_HOME - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="row">
    <div class="col-lg-8">
        <?php if(file_exists("image/slide1.jpg")){ ?>
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-image"></i> <?php echo $OJ_NAME ?></div>
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php for($i=1;file_exists("image/slide$i.jpg");$i++){ ?>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?php echo $i-1; ?>" <?php if($i==1) echo 'class="active"'; ?> aria-current="<?php if($i==1) echo 'true'; ?>"></button>
                    <?php } ?>
                </div>
                <div class="carousel-inner">
                    <?php for($i=1;file_exists("image/slide$i.jpg");$i++){ ?>
                    <div class="carousel-item <?php if($i==1) echo 'active'; ?>">
                        <img src="image/slide<?php echo $i ?>.jpg" class="d-block w-100" alt="Slide <?php echo $i ?>">
                    </div>
                    <?php } ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>
        <?php } ?>

        <!-- News Section -->
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-info-circle"></i> <?php echo $MSG_NEWS;?></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th><?php echo $MSG_TITLE;?></th>
                                <th><?php echo $MSG_TIME;?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_news = "select * FROM `news` WHERE `defunct`!='Y' AND `title`!='faqs.cn' ORDER BY `importance` desc,`time` DESC LIMIT 10";
                            $result_news = mysql_query_cache( $sql_news );
                            if ( $result_news ) {
                                foreach ( $result_news as $row ) {
                                    echo "<tr><td><a href=\"viewnews.php?id=".$row["news_id"]."\">".$row["title"]."</a></td><td>".$row["time"]."</td></tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if(isset($pages) && $pages>1){ ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item"><a class="page-link" href="index.php?page=1">&laquo;&laquo;</a></li>
                <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $page==1?1:$page-1?>">&laquo;</a></li>
                <?php for($i=$spage; $i<=$epage; $i++){ ?>
                <li class="page-item <?php if($page==$i) echo 'active'; ?>"><a class="page-link" href="index.php?page=<?php echo $i?>"><?php echo $i?></a></li>
                <?php } ?>
                <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $page==$pages?$page:$page+1?>">&raquo;</a></li>
                <li class="page-item"><a class="page-link" href="index.php?page=<?php echo $pages?>">&raquo;&raquo;</a></li>
            </ul>
        </nav>
        <?php } ?>

        <?php
        $month_id=mysql_query_cache("select solution_id from solution where in_date<date_add(curdate(),interval -day(curdate())+1 DAY) order by solution_id desc limit 1;");
        if(!empty($month_id) && isset($month_id[0][0]) ) $month_id=$month_id[0][0]; else $month_id=0;
        $view_month_rank=mysql_query_cache("select user_id,nick,count(distinct(problem_id)) ac from solution where solution_id>$month_id and problem_id>0 $not_in_noip_contests and user_id not in (".$OJ_RANK_HIDDEN.") and result=4 and first_time=1 group by user_id,nick order by ac desc limit 10");
        if ( !empty($view_month_rank) ) {
        ?>
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-star"></i> 本月之星</div>
            <div class="card-body p-0">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>用户</th>
                            <th>昵称</th>
                            <th>AC数</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $view_month_rank as $row ) { ?>
                        <tr>
                            <td><a href="userinfo.php?user=<?php echo htmlentities($row[0],ENT_QUOTES,"UTF-8")?>">⭐<?php echo htmlentities($row[0],ENT_QUOTES,"UTF-8")?>⭐</a></td>
                            <td><?php echo htmlentities($row[1],ENT_QUOTES,"UTF-8")?></td>
                            <td><?php echo $row[2]?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php } ?>

        <!-- Index News Content -->
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-newspaper"></i> <?php echo $OJ_INDEX_NEWS_TITLE;?></div>
            <div class="card-body">
                <?php
                $sql_news = "select * FROM `news` WHERE `defunct`!='Y' AND `title`='$OJ_INDEX_NEWS_TITLE' ORDER BY `importance` ASC,`time` DESC LIMIT 10";
                $result_news = mysql_query_cache( $sql_news );
                if ( $result_news ) {
                    foreach ( $result_news as $row ) {
                        echo bbcode_to_html($row["content"]);
                    }
                }
                ?>
                <hr>
                <p class="text-center text-body-secondary">Recent Submission: <?php echo $speed?></p>
                <div id="submission" style="width:100%;height:300px"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Recent Problems -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-clock-history"></i> <?php echo $MSG_RECENT_PROBLEM;?></div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
                    <thead>
                        <tr>
                            <th><?php echo $MSG_TITLE;?></th>
                            <th><?php echo $MSG_TIME;?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $noip_problems=array_merge(...mysql_query_cache("select problem_id from contest c left join contest_problem cp on start_time<'$now' and end_time>'$now' and (c.title like ? or (c.contest_type & 20) >0) and c.contest_id=cp.contest_id","%$OJ_NOIP_KEYWORD%"));
                        $noip_problems=array_unique($noip_problems);
                        if(isset($_SESSION[$OJ_NAME."_user_id"])) $user_id=$_SESSION[$OJ_NAME."_user_id"]; else $user_id='guest';
                        $sql_problems = "select p.problem_id,title,max_in_date from (select problem_id,min(result) best,max(in_date) max_in_date from solution where user_id=? and result>=4 and problem_id>0 group by problem_id ) s inner join problem p on s.problem_id=p.problem_id where s.best>4 order by max_in_date desc LIMIT 5";
                        $result_problems = mysql_query_cache( $sql_problems ,$user_id);
                        if ( !empty($result_problems) ) {
                            foreach ( $result_problems as $row ) {
                                if(in_array(strval($row['problem_id']),$noip_problems)) continue;
                                echo "<tr><td><a href=\"problem.php?id=".$row["problem_id"]."\">".$row["title"]."</a></td><td>".substr($row["max_in_date"],5,5)."</td></tr>";
                            }
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Search -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-search"></i> <?php echo $MSG_SEARCH;?></div>
            <div class="card-body">
                <form action="problem.php" method="get" class="d-flex">
                    <input class="form-control me-2" type="text" name="id" placeholder="<?php echo $MSG_PROBLEM_ID?>">
                    <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </div>

        <!-- Recent Contests -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-calendar-event"></i> <?php echo $MSG_RECENT_CONTEST ;?></div>
            <div class="card-body p-0">
                <table class="table table-hover table-sm mb-0">
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
                            foreach ( $result_contests as $row ) {
                                echo "<tr><td><a href=\"contest.php?cid=".$row["contest_id"]."\">".$row["title"]."</a></td><td>".substr($row["start_time"],5,5)."</td></tr>";
                            }
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/js/echarts.min.js"></script>
<script>
$(function(){
    var all = <?php echo json_encode(array_column($chart_data_all,1))?>;
    var chart = echarts.init(document.getElementById('submission'));
    var option = {
        tooltip: { trigger: 'axis', formatter: '{b0}({a0}): {c0}<br />{b1}({a1}): {c1}' },
        legend: { data: ['<?php echo $MSG_SUBMIT?>','<?php echo $MSG_AC?>'] },
        xAxis: { data: <?php echo json_encode(array_column($chart_data_ac,0))?>, inverse: true },
        yAxis: [{ type: 'value', name: '<?php echo $MSG_SUBMIT?>' }],
        series: [
            { name: '<?php echo $MSG_SUBMIT?>', type: 'bar', data: all },
            { name: '<?php echo $MSG_AC?>', type: 'bar', data: <?php echo json_encode(array_column($chart_data_ac,1))?> }
        ]
    };
    chart.setOption(option);
});
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>
