<!DOCTYPE html>
<html lang="cn">

<head>
    <?php include('_includes/head.php'); ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <div class="mdui-center mdui-row-sm-6 mdui-row-xs-1">
            <form id="simform" class="" action="status.php" method="get">
                <div class="mdui-textfield mdui-col">
                    <input class="mdui-textfield-input" type="text" name="problem_id"
                        value="<?php echo htmlspecialchars($problem_id, ENT_QUOTES); ?>"
                        placeholder="题目编号">
                </div>
                <div class="mdui-textfield mdui-col">
                    <input class="mdui-textfield-input" type="text" name="user_id"
                        value="<?php echo htmlspecialchars($user_id, ENT_QUOTES); ?>"
                        placeholder="用户">
                </div>
                <?php if (isset($cid)) { ?>
                    <input type="hidden" name="cid" value="<?php echo $cid; ?>">
                <?php } ?>

                <div class="mdui-col mdui-p-a-1 mdui-p-t-2">
                    <span class="mdui-m-r-2">语言</span>
                    <div style="width: calc(100% - 50px); display: inline-block;">
                        <select class="mdui-select" name="language" mdui-select="{position: 'bottom'}">
                            <option value="-1">All</option>
                            <?php
                                if (isset($_GET['language'])) {
                                    $selectedLang = intval($_GET['language']);
                                }
                                else {
                                    $selectedLang = -1;
                                }
                                $lang_count = count($language_ext);
                                $langmask = $OJ_LANGMASK;
                                $lang = (~((int)$langmask))&((1<<($lang_count))-1);
                                for ($i=0; $i<$lang_count; $i++) {
                                    if ($lang & (1<<$i)) {
                                        echo '<option value="'.$i.'" '.($selectedLang == $i ? "selected" : "").'>'
                                            .$language_name[$i]
                                            .'</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="mdui-col mdui-p-a-1 mdui-p-t-2">
                    <span class="mdui-m-r-2">结果</span>
                    <div style="width: calc(100% - 50px); display: inline-block;">
                        <select class="mdui-select mdui-col" name="jresult" mdui-select="{position: 'bottom'}">
                            <?php
                        if (isset($_GET['jresult']))
                            $jresult_get = intval($_GET['jresult']);
                        else
                            $jresult_get = -1;

                        if ($jresult_get>=12 || $jresult_get<0)
                            $jresult_get = -1;
                        if ($jresult_get==-1)
                            echo "<option value='-1' selected>All</option>";
                        else
                            echo "<option value='-1'>All</option>";
                            
                        for ($j=0; $j<12; $j++) {
                            $i = ($j+4)%12;
                            if ($i==$jresult_get)
                                echo "<option value='".$jresult_get."' selected>".$jresult[$i]."</option>";
                            else
                                echo "<option value='".$i."'>".$jresult[$i]."</option>";
                        }
                    ?>
                        </select>
                    </div>
                </div>

                <?php
                    if (isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'source_browser'])) {
                        if (isset($_GET['showsim']))
                            $showsim = intval($_GET['showsim']);
                        else
                            $showsim = 0;

                        echo '<div class="mdui-col mdui-p-a-1 mdui-p-t-2">'
                            .'<span class="mdui-m-r-2">SIM</span>'
                            .'<div style="width: calc(100% - 50px); display: inline-block;">'
                            .'<select id="appendedInputButton" class="mdui-select mdui-col" mdui-select="" name="showsim" onchange="document.getElementById(\'simform\').submit();">'
                            .'<option value=0 '.($showsim==0?'selected':'').'>All</option>'
                            .'<option value="50" '.($showsim==50?'selected':'').'>50</option>'
                            .'<option value="60" '.($showsim==60?'selected':'').'>60</option>'
                            .'<option value="70" '.($showsim==70?'selected':'').'>70</option>'
                            .'<option value="80" '.($showsim==80?'selected':'').'>80</option>'
                            .'<option value="90" '.($showsim==90?'selected':'').'>90</option>'
                            .'<option value="100" '.($showsim==100?'selected':'').'>100</option>'
                            .'</select>'
                            .'</div>'
                            .'</div>';
                    }
                ?>
                <button class="mdui-btn mdui-color-theme-accent mdui-ripple mdui-col mdui-m-t-2" type="submit">查询</button>
            </form>
        </div>

        <div class="mdui-table-fluid mdui-m-y-4">
            <table id="result-tab" class="mdui-table mdui-table-hoverable mdui-center" width=80%>
                <thead>
                    <tr>
                        <th class="mdui-table-col-numeric">提交编号</th>
                        <th>用户</th>
						<th>
							<?php echo $MSG_NICK?>
						</th>
                        <th>题目编号</th>
                        <th>结果</th>
                        <th>内存</th>
                        <th>耗时</th>
                        <th>语言</th>
                        <th>代码长度</th>
                        <th>提交时间</th>
                        <?php if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])) { ?>
                            <th><?php echo $MSG_JUDGER; ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $cnt = 0;
                        foreach ($view_status as $row) {
                            echo '<tr>';
                            foreach ($row as $table_cell) {
                                echo '<td>'.$table_cell.'</td>';
                            }
                            echo '</tr>';
                        }
                    ?>
                    <script> console.log(<?php echo json_encode($result); ?>); </script>
                </tbody>
            </table>
        </div>

        <div class="mdui-btn-group" style="width: 100% !important;">
            <?php
                echo '<a class="mdui-btn mdui-float-left" href="status.php?'.$str2.'">'
                    .'<i class="mdui-icon material-icons">first_page</i>'
                    .'<span class="mdui-m-l-1">Top</span>'
                    .'</a>';
                if (isset($_GET['prevtop'])) {
                    echo '<a class="mdui-btn mdui-float-left" href="status.php?'.$str2.'&top='.intval($_GET['prevtop']).'">'
                        .'<i class="mdui-icon material-icons">navigate_before</i>'
                        .'<span class="mdui-m-l-1">Prev</span>'
                        .'</a>';
                } else {
                    echo '<a class="mdui-btn mdui-float-left" href="status.php?'.$str2.'&top='.($top+50).'">'
                        .'<i class="mdui-icon material-icons">navigate_before</i>'
                        .'<span class="mdui-m-l-1">Prev</span>'
                        .'</a>';
                }
                echo '<a class="mdui-btn mdui-float-right" href="status.php?'.$str2.'&top='.$bottom.'&prevtop='.$top.'">'
                    .'<span class="mdui-m-r-1">Next</span>'
                    .'<i class="mdui-icon material-icons">navigate_next</i>'
                    .'</a>';
            ?>
        </div>
    </div>

<script>
	var i = 0;
	var judge_result = [<?php
	foreach ($judge_result as $result) {
		echo "'$result',";
	} ?>
	''];

	var judge_color = [<?php
	foreach ($judge_color as $result) {
		echo "'$result',";
	} ?>
	''];
</script>

<?php include("template/$OJ_TEMPLATE/css.php");?>
<script src="include/jquery-latest.js"></script>
<script src="template/<?php echo $OJ_TEMPLATE?>/auto_refresh.js?v=0.40"></script>
</body>

</html>
