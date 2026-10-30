<?php $show_title="$MSG_PROBLEMS - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<?php
if (!isset($page)) $page = 1;
$page = intval($page);
if ($page < 1) $page = 1;
$view_total_page = isset($view_total_page) ? intval($view_total_page) : 1;
$section = 8;
$start = $page > $section ? $page - $section : 1;
$end = $page + $section > $view_total_page ? $view_total_page : $page + $section;
$lg_postfix = isset($postfix) ? htmlentities($postfix, ENT_QUOTES, 'UTF-8') : '';
$lg_logged = isset($_SESSION[$OJ_NAME.'_user_id']);
?>
<div class="lg-page lg-problemset">
    <div class="lg-card lg-toolbar">
        <form action="problemset.php" method="get">
            <div class="lg-search-box">
                <svg class="lg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/></svg>
                <input type="text" name="search" placeholder="搜索题目名称或来源" value="<?php if (isset($_GET['search'])) echo htmlentities($_GET['search'], ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <button class="lg-btn lg-btn-primary" type="submit">搜索</button>
        </form>
        <form class="lg-idjump" action="problem.php" method="get">
            <input type="text" name="id" placeholder="题目编号" inputmode="numeric">
            <button class="lg-btn lg-btn-ghost" type="submit">跳转</button>
        </form>
        <div class="lg-toolbar-right">
            <button type="button" class="lg-switch is-on" id="show_tag" aria-pressed="true">
                <span class="lg-switch-track"><span class="lg-switch-thumb"></span></span>
                <span><?php echo $MSG_SHOW_TAGS; ?></span>
            </button>
            <a class="lg-btn lg-btn-ghost" href="category.php"><?php echo $MSG_SHOW_ALL_TAGS; ?></a>
        </div>
    </div>

    <div class="lg-card lg-list-card">
        <div class="lg-list-head">
            <h2>题目列表</h2>
            <span class="lg-list-count">第 <?php echo $page; ?> 页<?php if ($view_total_page > 1) echo " / 共 $view_total_page 页"; ?></span>
        </div>
        <style id="show_tag_style"></style>
        <table class="lg-table">
            <thead>
                <tr>
                    <?php if ($lg_logged) { ?>
                        <th class="lg-col-status"><?php echo $MSG_STATUS; ?></th>
                    <?php } ?>
                    <th class="lg-col-id"><?php echo $MSG_PROBLEM_ID; ?></th>
                    <th><?php echo $MSG_TITLE; ?></th>
                    <th class="lg-col-stat"><?php echo $MSG_SOVLED."/".$MSG_SUBMIT; ?></th>
                    <th class="lg-col-rate"><?php echo $MSG_PASS_RATE; ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            $i = 0;
            if (!empty($result)) {
                foreach ($result as $row) {
                    $percentage = $row['submit'] > 0 ? round(100 * $row['accepted'] / $row['submit'], 2) : 0;
                    $rate_text = rtrim(rtrim(number_format($percentage, 2, '.', ''), '0'), '.');
                    $rate_class = $percentage <= 0 ? ' is-zero' : ($percentage < 20 ? ' is-low' : '');
                    echo "<tr>";
                    if ($lg_logged) {
                        if (isset($sub_arr[$row['problem_id']])) {
                            if (isset($acc_arr[$row['problem_id']])) {
                                echo "<td><span class=\"lg-status lg-status-ac\" title=\"".$MSG_AC."\">✓</span></td>";
                            } else {
                                echo "<td><span class=\"lg-status lg-status-wa\" title=\"".$MSG_SUBMIT_RECORD."\">✗</span></td>";
                            }
                        } else {
                            echo "<td><span class=\"lg-status lg-status-none\"></span></td>";
                        }
                    }
                    echo "<td class=\"lg-col-id\"><b>".$row['problem_id']."</b></td>";
                    echo "<td class=\"lg-problem-title-cell\">";
                    echo "<a href=\"problem.php?id=".$row['problem_id']."\">".$row['title']."</a>";
                    if ($row['defunct'] == 'Y') {
                        echo "<a href=\"admin/problem_df_change.php?id=".$row['problem_id']."&getkey=".htmlentities($_SESSION[$OJ_NAME.'_getkey'])."\"><span class=\"label label-danger\">未公开</span></a>";
                    }
                    if (intval($row['coin']) > 0) {
                        echo str_repeat("<span style='color:#f39c11' title='奖励题目'>✦</span>", min(intval($row['coin']), 5));
                    }
                    echo "<div class=\"lg-problem-tags show_tag_controled\">".$view_problemset[$i][3]."</div>";
                    echo "</td>";
                    echo "<td class=\"lg-col-stat\"><a href=\"status.php?problem_id=".$row['problem_id']."&jresult=4\">".$row['accepted']."</a><span style=\"color:#d0d0d0\"> / </span><a href=\"status.php?problem_id=".$row['problem_id']."\" style=\"color:#8c8c8c\">".$row['submit']."</a></td>";
                    echo "<td class=\"lg-col-rate\"><div class=\"lg-rate".$rate_class."\"><span class=\"lg-rate-text\">".$rate_text."%</span><span class=\"lg-rate-bar\"><i style=\"width:".min(100, $percentage)."%\"></i></span></div></td>";
                    echo "</tr>";
                    $i++;
                }
            } else {
                echo "<tr><td colspan=\"".($lg_logged ? 5 : 4)."\" class=\"lg-empty\">没有找到符合条件的题目</td></tr>";
            }
            ?>
            </tbody>
        </table>

        <?php if (!isset($_GET['list']) && $view_total_page > 1) { ?>
        <div class="lg-pagination-wrap">
            <div class="ui pagination menu">
                <a class="icon item" href="problemset.php?page=1<?php echo $lg_postfix; ?>" title="首页"><i class="fast backward icon"></i></a>
                <a class="icon item<?php if ($page == 1) echo ' disabled'; ?>" href="problemset.php?page=<?php echo max(1, $page - 1).$lg_postfix; ?>" id="page_prev"><i class="left chevron icon"></i></a>
                <?php
                for ($i = $start; $i <= $end; $i++) {
                    echo "<a class=\"".($page == $i ? "active " : "")."item\" href=\"problemset.php?page=".$i.$lg_postfix."\">".$i."</a>";
                }
                ?>
                <a class="icon item<?php if ($page == $view_total_page) echo ' disabled'; ?>" href="problemset.php?page=<?php echo min($view_total_page, $page + 1).$lg_postfix; ?>" id="page_next"><i class="right chevron icon"></i></a>
                <a class="icon item" href="problemset.php?page=<?php echo $view_total_page.$lg_postfix; ?>" title="末页"><i class="fast forward icon"></i></a>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<script>
(function () {
    var showTag = document.getElementById('show_tag');
    var style = document.getElementById('show_tag_style');
    if (!showTag || !style) return;
    var wantShow = localStorage.getItem('show_tag') !== '0';

    function apply(on) {
        showTag.classList.toggle('is-on', on);
        showTag.setAttribute('aria-pressed', on ? 'true' : 'false');
        style.innerHTML = on ? '' : '.show_tag_controled{display:none!important}';
        localStorage.setItem('show_tag', on ? '1' : '0');
    }

    apply(wantShow);
    showTag.addEventListener('click', function () {
        apply(!showTag.classList.contains('is-on'));
    });
})();
</script>
<?php include("template/$OJ_TEMPLATE/footer.php");?>
