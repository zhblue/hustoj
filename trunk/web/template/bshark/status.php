<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>
		<?php echo $MSG_STATUS; ?> - <?php echo $OJ_NAME; ?>
	</title>
	<?php require("./template/bshark/header-files.php"); ?>
</head>

<body>
	<?php require("./template/bshark/nav.php"); ?>
	<div class="ui container bsharkMain">
		<div class="card">
			<div class="card-body">
				<form id=simform class="ui mini form" action="status.php" method="get">
					<div class="inline stackable fields" style="white-space: nowrap; ">
						<label style="font-size: 1.2em; margin-right: 1px; ">
							<?php echo $MSG_PROBLEM_ID ?>：
						</label>
						<div class="field">
							<input name="problem_id" style="width: 100px; " type="text"
								value="<?php echo htmlspecialchars($problem_id, ENT_QUOTES) ?>">
						</div>
						<label style="font-size: 1.2em; margin-right: 1px; ">
							<?php echo $MSG_USER ?>：
						</label>
						<div class="field"><input name="user_id" style="width: 100px; " type="text"
								value="<?php echo htmlspecialchars($user_id, ENT_QUOTES) ?>"></div>

						<div class="field"><label style="font-size: 1.2em; margin-right: 1px; ">
								<?php echo $MSG_LANG ?>：
							</label>
							<div class="ui mini selection dropdown" style="width: 110px;">
								<input type="hidden" name="language">
								<i class="dropdown icon"></i>
								<div class="default text">
									<?php echo $MSG_LANG; ?>
								</div>
								<div class="scrollhint menu">
									<div class="item" data-value="-1">All</div>
									<?php
									if (isset($_GET['language'])) {
										$selectedLang = intval($_GET['language']);
									} else {
										$selectedLang = -1;
									}
									$lang_count = count($language_ext);
									$langmask = $OJ_LANGMASK;
									$lang = (~((int) $langmask)) & ((1 << ($lang_count)) - 1);
									for ($i = 0; $i < $lang_count; $i++) {
										if ($lang & (1 << $i))
											echo "<div  data-value=$i class='item" . ($selectedLang == $i ? " active" : "") . "'>
            " . $language_name[$i] . "
            </div>";
									}
									?>
								</div>
							</div>
						</div>
						<div class="field">
							<label style="font-size: 1.2em; margin-right: 1px;margin-left: 10px; ">状态：</label>
							<div class="ui mini selection dropdown" style="width: 110px;">
								<input type="hidden" name="jreresult">
								<i class="dropdown icon"></i>
								<div class="default text">
									<?php echo $MSG_RESULT; ?>
								</div>
								<div class="scrollhint menu">
									<?php if (isset($_GET['jresult']))
										$jresult_get = intval($_GET['jresult']);
									else
										$jresult_get = -1;
									if ($jresult_get >= 12 || $jresult_get < 0)
										$jresult_get = -1;
									if ($jresult_get == -1)
										echo "<div class='item active' data-value='-1'>All</div>";
									else
										echo "<div class='item' data-value='-1'>All</div>";
									for ($j = 0; $j < 12; $j++) {
										$i = ($j + 4) % 12;
										if ($i == $jresult_get)
											echo "<div class='item active' data-value='" . strval($jresult_get) . "'>" . $jresult[$i] . "</div>";
										else
											echo "<div class='item' data-value='" . strval($i) . "'>" . $jresult[$i] . "</div>";
									}
									?>
								</div>
							</div>
						</div>
						<?php if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'source_browser'])) {
							if (isset($_GET['showsim']))
								$showsim = intval($_GET['showsim']);
							else
								$showsim = 0;
							?>
							<div class="field">
								<label>相似度：</label>
								<div class="ui mini selection dropdown" style="width: 110px;">
									<input type="hidden" name="showsim"
										onchange="document.getElementById('simform').submit();">
									<i class="dropdown icon"></i>
									<div class="default text">SIM</div>
									<div class="scrollhint menu">
										<?php
										echo "<div data-value=0 class='item" . ($showsim == 0 ? ' active' : '') . "'>All</div>
								<div data-value=50 class='item" . ($showsim == 50 ? ' active' : '') . "'>50</div>
								<div data-value=60 class='item" . ($showsim == 60 ? ' active' : '') . "'>60</div>
								<div data-value=70 class='item" . ($showsim == 70 ? ' active' : '') . "'>70</div>
								<div data-value=80 class='item" . ($showsim == 80 ? ' active' : '') . "'>80</div>
								<div data-value=90 class='item" . ($showsim == 90 ? ' active' : '') . "'>90</div>
								<div data-value=100 class='item" . ($showsim == 100 ? ' active' : '') . "'>100</div>"; ?>
									</div>
								</div>
							</div>
						<?php
						}
						?>
						<button class="ui labeled icon mini blue button" type="submit" style="margin-left: 20px;">
							<i class="search icon"></i>
							<?php echo $MSG_SEARCH; ?>
						</button>
						<span class='ui mini grey button'>AWT:<?php echo round($avg_delay, 2) ?>s </span>
						<script>var AWT =<?php echo round($avg_delay * 500, 0) ?>;</script>
					</div>
				</form>
			</div>
		</div>
		<div class="card">
			<div class="card-body">
				<?php if (isset($cid) && $cid>0) { ?>
					<ul class="pagination">
						<li class="page-item"><a class="page-link" href='contest.php?cid=<?php echo $view_cid ?>'>
								<?php echo $MSG_CONTEST; ?>C<?php echo $cid; ?>
							</a></li>
						<li class="page-item"><a class="page-link" href='status.php?cid=<?php echo $view_cid ?>'>
								<?php echo $MSG_STATUS; ?>
							</a></li>
						<li class="page-item"><a class="page-link" href='contestrank.php?cid=<?php echo $view_cid ?>'>
								<?php echo $MSG_STANDING; ?>
							</a></li>
						<li class="page-item"><a class="page-link"
								href='contestrank-oi.php?cid=<?php echo $view_cid ?>'>OI-<?php echo $MSG_STANDING; ?></a>
						</li>
						<li class="page-item"><a class="page-link" href='conteststatistics.php?cid=<?php echo $view_cid ?>'>
								<?php echo $MSG_STATISTICS; ?>
							</a>
						</li>
					</ul>
				<?php } ?>
				<table id="result-tab" class="ui very basic fluid stackable table" style="width:100%">
					<thead>
						<tr class='toprow'>
							<th>
								#
							</th>
							<th>
								<?php echo $MSG_USER ?>
							</th>
							<th>
								<?php echo $MSG_NICK ?>
							</th>
							<th>
								<?php echo $MSG_PROBLEM ?>
							</th>
							<th width=20%>
								<?php echo $MSG_RESULT ?>
							</th>
							<th class='hidden-xs'>
								<?php echo $MSG_MEMORY ?>
							</th>
							<th class='hidden-xs'>
								<?php echo $MSG_TIME ?>
							</th>
							<th>
								<?php echo $MSG_LANG ?>
							</th>
							<th class='hidden-xs'>
								<?php echo $MSG_CODE_LENGTH ?>
							</th>
							<th>
								<?php echo $MSG_SUBMIT_TIME ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$cnt = 0;
						foreach ($view_status as $row) {
							echo "<tr>";
							$i = 0;
							foreach ($row as $table_cell) {
								if ($i > 9)
									continue;
								if ($i > 3 && $i != 8 && $i != 6)
									echo "<td class='hidden-xs'>";
								else
									echo "<td>";
								echo $table_cell;
								echo "</td>";
								$i++;
							}
							echo "</tr>\n";
							$cnt = 1 - $cnt;
						}
						?>
					</tbody>
				</table>
				<div class="ui container center aligned">
					<div class="ui borderless tiny menu pagination">
						<a class="item" href=<?php echo "status.php?" . $str2; ?>>顶页</a>
						<?php if (isset($_GET['prevtop'])) { ?>
							<a class="item"
								href="<?php echo "status.php?" . $str2 . "&top=" . intval($_GET['prevtop']); ?>">上一页</a>
						<?php } else { ?>
							<a class="item" href="<?php echo "status.php?" . $str2 . "&top=" . ($top + 20); ?>">上一页</a>
						<?php } ?>
						<a class="item"
							href="<?php echo "status.php?" . $str2 . "&top=" . $bottom . "&prevtop=" . $top; ?>">下一页</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php require("./template/bshark/footer.php"); ?>
	<?php require("./template/bshark/footer-files.php"); ?>
</body>
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
<script src="<?php echo $OJ_CDN_URL ?>template/bs3/auto_refresh.js"></script>

</html>
