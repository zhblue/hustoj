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
		<div class="ui stackable grid">
			<div class="eleven wide column">
				<div class="card">
					<div class="card-body">
						<?php
						if (isset($_GET['cid'])) {
							$cid = intval($_GET['cid']);
							$view_cid = $cid;
							//print $cid;
						
							//check contest valid
							$sql = "SELECT * FROM `contest` WHERE `contest_id`=?";
							$result = pdo_query($sql, $cid);

							$rows_cnt = count($result);
							$contest_ok = true;
							$password = "";

							if (isset($_POST['password']))
								$password = $_POST['password'];

							if (false) {
								$password = stripslashes($password);
							}

							if ($rows_cnt == 0) {
								$view_title = "比赛已经关闭!";
							} else {
								$row = $result[0];
								$view_private = $row['private'];

								if ($password != "" && $password == $row['password'])
									$_SESSION[$OJ_NAME . '_' . 'c' . $cid] = true;

								if ($row['private'] && !isset($_SESSION[$OJ_NAME . '_' . 'c' . $cid]))
									$contest_ok = false;

								if ($row['defunct'] == 'Y')
									$contest_ok = false;

								if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']))
									$contest_ok = true;

								$now = time();
								$start_time = strtotime($row['start_time']);
								$end_time = strtotime($row['end_time']);
								$view_description = $row['description'];
								$view_title = $row['title'];
								$view_start_time = $row['start_time'];
								$view_end_time = $row['end_time'];
							}
						}
						?>
						<h2>C<?php echo $view_cid; ?>: <?php echo $view_title; ?>
						</h2>
						<span class="ui label <?php
						if ($now > $end_time)
							echo "grey";
						else if ($now < $start_time)
							echo "green";
						else
							echo "red";
						?>">
							<?php
							if ($now > $end_time)
								echo $MSG_Ended;
							else if ($now < $start_time)
								echo $MSG_Contest_Pending;
							else
								echo $MSG_Running;
							?>
						</span>
						<span class="ui label <?php
						if ($view_private == '0')
							echo "blue";
						else
							echo "red";
						?>">
							<?php
							if ($view_private == '0')
								echo $MSG_Public;
							else
								echo $MSG_Private;
							?>
						</span>
						<h3>
							<?php echo $MSG_CONTEST; ?><?php echo $MSG_TIME; ?>
						</h3>
						<span class="ui label basic black">
							<?php echo $view_start_time; ?>
						</span>~<span class="ui label basic black"><?php echo $view_end_time; ?></span>
						<?php if (isset($OJ_RANK_LOCK_PERCENT) && $OJ_RANK_LOCK_PERCENT != 0) { ?>
							Lock Board Time: <?php echo date("Y-m-d H:i:s", $view_lock_time) ?><br>
						<?php } ?>
						现在:<span class="ui blue label" id="nowdate"></span>
					</div>
				</div>
				<div class="card">
					<div class="card-body">
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
								<a class="item" href=<?php echo "status.php?" . $str2; ?>><?php echo $MSG_TOP_OF_PAGE?></a>
								<?php if (isset($_GET['prevtop'])) { ?>
									<a class="item"
										href="<?php echo "status.php?" . $str2 . "&top=" . intval($_GET['prevtop']); ?>"><?php echo $MSG_PREV_PAGE?></a>
								<?php } else { ?>
									<a class="item"
										href="<?php echo "status.php?" . $str2 . "&top=" . ($top + 20); ?>"><?php echo $MSG_PREV_PAGE?></a>
								<?php } ?>
								<a class="item"
									href="<?php echo "status.php?" . $str2 . "&top=" . $bottom . "&prevtop=" . $top; ?>"><?php echo $MSG_NEXT_PAGE?></a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="five wide column">
				<div class="card" style="padding: 0;">
					<div class="ui vertical fluid menu problemAction">
						<a class="item" href='contest.php?cid=<?php echo $view_cid ?>'>
							<?php echo $MSG_CONTEST; ?>C<?php echo $cid; ?>
						</a></li>
						<a class="active item" href='status.php?cid=<?php echo $view_cid ?>'>
							<?php echo $MSG_STATUS; ?>
						</a>
						<a class="item" href='contestrank.php?cid=<?php echo $view_cid ?>'>
							<?php echo $MSG_STANDING; ?>
						</a>
						<a class="item" href='contestrank-oi.php?cid=<?php echo $view_cid ?>'>OI-<?php echo $MSG_STANDING; ?></a>
						<a class="item" href='conteststatistics.php?cid=<?php echo $view_cid ?>'>
							<?php echo $MSG_STATISTICS; ?>
						</a>
						<?php if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator'])) { ?>
							<a href="suspect_list.php?cid=<?php echo $view_cid ?>" class="item">
								<?php echo $MSG_IP_VERIFICATION ?>
							</a>
							<a href="user_set_ip.php?cid=<?php echo $view_cid ?>" class="item">
								<?php echo $MSG_SET_LOGIN_IP ?>
							</a>
							<a target="_blank" href="../../bsadmin/contest_edit.php?cid=<?php echo $view_cid ?>"
								class="item">
								<?php echo $MSG_EDIT . $MSG_CONTEST; ?>
							</a>
						<?php } ?>
					</div>
				</div>
				<div class="card">
					<div class="card-body">
						<form id=simform class="ui form" action="status.php" method="get">
							<?php if (isset($cid))
								echo "<input type='hidden' name='cid' value='$cid'>"; ?>
							<div class="field">
								<label>
									<?php echo $MSG_PROBLEM_ID ?>
								</label>
								<input name="problem_id" type="text"
									value="<?php echo htmlspecialchars($problem_id, ENT_QUOTES) ?>">
							</div>
							<div class="field">
								<label>
									<?php echo $MSG_USER ?>
								</label>
								<input name="user_id" type="text"
									value="<?php echo htmlspecialchars($user_id, ENT_QUOTES) ?>">
							</div>
							<div class="field">
								<label>
									<?php echo $MSG_LANG ?>
								</label>
								<div class="ui selection dropdown">
									<input type="hidden" name="jreresult">
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
												echo "<div  data-value=$i class='item" . ($selectedLang == $i ? "active" : "") . "'>
            " . $language_name[$i] . "
            </div>";
										}
										?>
									</div>
								</div>
							</div>
							<div class="field">
								<label>
									<?php echo $MSG_RESULT; ?>
								</label>
								<div class="ui selection dropdown">
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
									<div class="ui selection dropdown">
										<input type="hidden" name="showsim"
											onchange="document.getElementById('simform').submit();">
										<i class="dropdown icon"></i>
										<div class="default text">SIM</div>
										<div class="scrollhint menu">
											<?php
											echo "<div class='item' data-value=0 " . ($showsim == 0 ? 'selected' : '') . ">All</div>
								<div class='item' data-value=50 " . ($showsim == 50 ? 'selected' : '') . ">50</div>
								<div class='item' data-value=60 " . ($showsim == 60 ? 'selected' : '') . ">60</div>
								<div class='item' data-value=70 " . ($showsim == 70 ? 'selected' : '') . ">70</div>
								<div class='item' data-value=80 " . ($showsim == 80 ? 'selected' : '') . ">80</div>
								<div class='item' data-value=90 " . ($showsim == 90 ? 'selected' : '') . ">90</div>
								<div class='item' data-value=100 " . ($showsim == 100 ? 'selected' : '') . ">100</div>"; ?>
										</div>
									</div>
								</div>
							<?php
							}
							?>
							<button class="ui labeled icon blue button" type="submit" style="margin-left: 20px;">
								<i class="search icon"></i>
								<?php echo $MSG_SEARCH; ?>
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script>
		var diff = new Date("<?php echo date("Y/m/d H:i:s") ?>").getTime() - new Date().getTime();
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
			n = y + "-" + mon + "-" + d + " " + (h >= 10 ? h : "0" + h) + ":" + (m >= 10 ? m : "0" + m) + ":" + (s >= 10 ? s : "0" + s);
			//alert(n);
			document.getElementById('nowdate').innerHTML = n;
			setTimeout("clock()", 1000);
		}
		clock();
	</script>
	<?php require("./template/bshark/footer.php"); ?>
	<?php require("./template/bshark/footer-files.php"); ?>
</body>

</html>