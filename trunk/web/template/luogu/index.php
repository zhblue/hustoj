<?php $show_title="$MSG_HOME - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<div class="lg-home">
    <section class="lg-hero">
        <div class="lg-hero-copy">
            <h1>欢迎来到 <?php echo $OJ_NAME ?></h1>
            <p>在线评测 · 题目训练 · 竞赛排名，从这里开始你的编程之旅。</p>
            <div class="lg-hero-actions">
                <a class="lg-btn lg-btn-primary" href="problemset.php">题库</a>
                <a class="lg-btn lg-btn-ghost" href="contest.php">比赛</a>
                <a class="lg-btn lg-btn-ghost" href="ranklist.php"><?php echo $MSG_RANKLIST ?></a>
            </div>
        </div>
        <div class="lg-hero-art" aria-hidden="true">
            <svg viewBox="0 0 200 140" fill="none">
                <rect x="8" y="12" width="184" height="116" rx="8" fill="#ffffff" stroke="#dfeaf4" stroke-width="1.5"/>
                <circle cx="24" cy="26" r="3" fill="#fe4c61"/>
                <circle cx="35" cy="26" r="3" fill="#f7c948"/>
                <circle cx="46" cy="26" r="3" fill="#61b35c"/>
                <path d="M20 40h160" stroke="#eef3f8" stroke-width="1.5"/>
                <path d="M58 68 44 80l14 12" stroke="#3498db" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="m142 68 14 12-14 12" stroke="#3498db" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="m112 60-22 40" stroke="#61b35c" stroke-width="4" stroke-linecap="round"/>
                <rect x="62" y="102" width="76" height="6" rx="3" fill="#e8f4fc"/>
                <rect x="76" y="113" width="48" height="5" rx="2.5" fill="#f0f4f8"/>
            </svg>
        </div>
    </section>

    <div class="lg-home-grid">
        <div class="lg-home-main">
            <?php if(file_exists("image/slide1.jpg")){ ?>
            <div class="carousel">
                <div class="carousel-arrow left" onclick="prevSlide()">&lt;</div>
                <?php for($i=1;file_exists("image/slide$i.jpg");$i++){ ?>
                <div class="carousel-slide <?php if($i==1) echo "active";?>" style="background-image: url('image/slide<?php echo $i ?>.jpg')"></div>
                <?php } ?>
                <div class="carousel-arrow right" onclick="nextSlide()">&gt;</div>
                <div class="carousel-dots">
                    <span class="carousel-dot active" data-index="0"></span>
                    <span class="carousel-dot" data-index="1"></span>
                    <span class="carousel-dot" data-index="2"></span>
                </div>
            </div>
            <?php } ?>

            <section class="lg-card">
                <h2 class="lg-card-title"><?php echo $MSG_NEWS;?></h2>
                <table class="lg-news-list">
                    <tbody>
                        <?php
                        $sql_news = "select * FROM `news` WHERE `defunct`!='Y' AND `title`!='faqs.cn' ORDER BY `importance` desc,`time` DESC LIMIT 10";
                        $result_news = mysql_query_cache( $sql_news );
                        if ( $result_news ) {
                            foreach ( $result_news as $row ) {
                                echo "<tr><td><a href=\"viewnews.php?id=".$row["news_id"]."\">".$row["title"]."</a></td><td>".$row["time"]."</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan=\"2\">check database connection or account !</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <?php
                if(isset($pages) && $pages>1 ){
                    echo "<div class='lg-pagination-wrap'><div class='pagination'>";
                    echo "<li class='page-item'><a href='index.php?page=1'>&lt;&lt;</a></li>";
                    echo "<li class='page-item'><a href='index.php?page=".($page==1?1:($page-1))."'>&lt;</a></li>";
                    for($i=$spage; $i<=$epage; $i++){
                        echo "<li class='".($page==$i?"active ":"")."page-item'><a title='go to page' href='index.php?page=".$i."'>".$i."</a></li>";
                    }
                    echo "<li class='page-item'><a href='index.php?page=".($page==$pages?$page:($page+1))."'>&gt;</a></li>";
                    echo "<li class='page-item'><a href='index.php?page=".$pages."'>&gt;&gt;</a></li>";
                    echo "</div></div>";
                }
                ?>
            </section>

            <?php
            /* 本月之星  */
            $month_id=mysql_query_cache("select solution_id from solution where  in_date<date_add(curdate(),interval -day(curdate())+1 DAY) order by solution_id desc limit 1;");
            if(!empty($month_id) && isset($month_id[0][0])) $month_id=$month_id[0][0];else $month_id=0;
            $view_month_rank=mysql_query_cache("select user_id,nick,count(distinct(problem_id)) ac from solution where solution_id>$month_id and problem_id>0  $not_in_noip_contests and user_id not in (".$OJ_RANK_HIDDEN.")  and result=4 and first_time=1 group by user_id,nick order by ac desc limit 10");
            if ( !empty($view_month_rank) ) {
            ?>
            <section class="lg-card">
                <h2 class="lg-card-title">本月之星</h2>
                <table class="lg-rank-list">
                    <tbody>
                    <?php
                        $lg_rank_no = 1;
                        foreach ( $view_month_rank as $row ) {
                            echo "<tr><td>".$lg_rank_no."</td><td><a target='_blank' href='userinfo.php?user=".htmlentities($row[0],ENT_QUOTES,"UTF-8")."'>".htmlentities($row[0],ENT_QUOTES,"UTF-8")."</a></td><td>".($row[1])."</td><td>".($row[2])." AC</td></tr>";
                            $lg_rank_no++;
                        }
                    ?>
                    </tbody>
                </table>
            </section>
            <?php } ?>

            <section class="lg-card">
                <h2 class="lg-card-title"><?php echo $OJ_INDEX_NEWS_TITLE;?></h2>
                <div class="lg-section-body">
                    <?php
                    $sql_news = "select * FROM `news` WHERE `defunct`!='Y' AND `title`='$OJ_INDEX_NEWS_TITLE' ORDER BY `importance` ASC,`time` DESC LIMIT 10";
                    $result_news = mysql_query_cache( $sql_news );
                    if ( $result_news ) {
                        foreach ( $result_news as $row ) {
                            echo bbcode_to_html($row["content"]);
                        }
                    }
                    ?>
                    <div style="margin-top:14px;text-align:center;color:#8c8c8c;font-size:13px;">
                        Recent submission：<?php echo $speed; ?>
                        <div id="submission" style="width:100%;height:280px"></div>
                    </div>
                </div>
            </section>
        </div>

        <aside class="lg-home-side">
            <section class="lg-card">
                <h2 class="lg-card-title"><?php echo $MSG_RECENT_PROBLEM;?></h2>
                <table class="lg-news-list">
                    <tbody>
                    <?php
                        // 未解之谜
                        $noip_problems=array_merge(...mysql_query_cache("select problem_id from contest c left join contest_problem cp on start_time<'$now' and end_time>'$now' and  (c.title like ? or (c.contest_type & 20) >0) and c.contest_id=cp.contest_id","%$OJ_NOIP_KEYWORD%"));
                        $noip_problems=array_unique($noip_problems);
                        if(isset($_SESSION[$OJ_NAME."_user_id"])) $user_id=$_SESSION[$OJ_NAME."_user_id"]; else $user_id='guest';
                        $sql_problems = "select p.problem_id,title,max_in_date from (select problem_id,min(result) best,max(in_date) max_in_date from solution
                                where user_id=? and result>=4 and problem_id>0 group by problem_id ) s inner join problem p on s.problem_id=p.problem_id
                             where s.best>4 order by max_in_date desc  LIMIT 5";
                        $result_problems = mysql_query_cache( $sql_problems ,$user_id);
                        if ( !empty($result_problems) ) {
                            foreach ( $result_problems as $row ) {
                                if(in_array(strval($row['problem_id']),$noip_problems)) continue;
                                echo "<tr><td><a href=\"problem.php?id=".$row["problem_id"]."\">".$row["title"]."</a></td><td>".substr($row["max_in_date"],5,5)."</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan=\"2\" style=\"color:#8c8c8c\">暂无最近练习记录</td></tr>";
                        }
                    ?>
                    </tbody>
                </table>
            </section>

            <section class="lg-card lg-side-search">
                <h2 class="lg-card-title"><?php echo $MSG_SEARCH;?></h2>
                <form action="problem.php" method="get">
                    <div class="lg-search-box">
                        <svg class="lg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/></svg>
                        <input type="text" name="id" placeholder="<?php echo $MSG_PROBLEM_ID ;?> …" inputmode="numeric">
                    </div>
                </form>
            </section>

            <section class="lg-card">
                <h2 class="lg-card-title"><?php echo $MSG_RECENT_CONTEST ;?></h2>
                <table class="lg-news-list">
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
            </section>
        </aside>
    </div>
</div>
<?php include("template/$OJ_TEMPLATE/footer.php");?>
<?php if(file_exists("image/slide1.jpg")){ ?>
    <script>
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('.carousel-dot');
        let currentIndex = 0;
        let autoPlayInterval;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === index);
            });
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
        }

        function nextSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            showSlide(currentIndex);
        }

        function prevSlide() {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            showSlide(currentIndex);
        }

        autoPlayInterval = setInterval(nextSlide, 5000);

        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                const targetIndex = parseInt(dot.dataset.index);
                if (targetIndex !== currentIndex) {
                    currentIndex = targetIndex;
                    showSlide(currentIndex);
                    clearInterval(autoPlayInterval);
                    autoPlayInterval = setInterval(nextSlide, 5000);
                }
            });
        });

        const carousel = document.querySelector('.carousel');
        if (carousel) {
            carousel.addEventListener('mouseenter', () => clearInterval(autoPlayInterval));
            carousel.addEventListener('mouseleave', () => autoPlayInterval = setInterval(nextSlide, 5000));
        }
    </script>
<?php } ?>
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/js/echarts.min.js"></script>
<script type="text/javascript">
    $(function () {
        var all = <?php echo json_encode(array_column($chart_data_all,1))?>;
        var sub_echarts = echarts.init($("#submission")[0]);
        var option = {
            tooltip: {
                trigger: 'axis',
                formatter: '{b0}({a0}): {c0}<br />{b1}({a1}): {c1}'
            },
            legend: {
                data: ['<?php echo $MSG_SUBMIT?>','<?php echo $MSG_AC?>']
            },
            xAxis: {
                data: <?php echo json_encode(array_column($chart_data_ac,0))?>,
                inverse: true
            },
            yAxis: [
                {
                    type: 'value',
                    name: '<?php echo $MSG_SUBMIT?>'
                }
            ],
            series: [
                {
                    name: '<?php echo $MSG_SUBMIT?>',
                    type: 'bar',
                    data: all,
                    itemStyle: { color: '#3498db' }
                },
                {
                    name: '<?php echo $MSG_AC?>',
                    type: 'bar',
                    data: <?php echo json_encode(array_column($chart_data_ac,1))?>,
                    itemStyle: { color: '#52c41a' }
                }
            ]
        };
        sub_echarts.setOption(option);
    });
</script>
