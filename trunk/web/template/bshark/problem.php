<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>
		<?php echo $MSG_PROBLEM; ?> - <?php echo $OJ_NAME; ?>
	</title>
	<?php require("./template/bshark/header-files.php"); ?>
	<?php if (isset($OJ_MATHJAX) && $OJ_MATHJAX) { ?>
		<!--以下为了加载公式的使用而既加入-->
		<script>
			MathJax = {
				tex: { inlineMath: [['$', '$'], ['\\(', '\\)']] }
			};
		</script>

		<script id="MathJax-script" async src="template/<?php echo $OJ_TEMPLATE ?>/tex-chtml.js"></script>

	<?php } ?>
	<style>
		h4 {
			margin-top: 10px;
			margin-bottom: 10px;
			font-size: 24px;
		}
	</style>
	<style>
		.card-body h2 {
			margin-top: 10px;
			margin-bottom: 16px;
		}

		.card-body h4 {
			font-size: 24px;
		}

		.card-body h4:not(:first-child) {
			margin-top: 32px;
		}
	</style>
</head>

<body>
	<?php require("./template/bshark/nav.php"); ?>
	<div class="ui container bsharkMain">
		<div class="ui stackable grid">
			<div class="eleven wide column">
				<div class="card">
					<!--StartMarkForVirtualJudge-->
					<div class="card-body">
						<div class="problemHead">
							<?php
							if ($pr_flag) {
								echo "<h1>#$id. " . $row['title'] . "</h1>";
							} else {
								//$PID="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
								$id = $row['problem_id'];
								echo "<h1>$MSG_PROBLEM " . $PID[$pid] . ". " . $row['title'] . "</h1>";
							}
							if ($row['spj'])
								echo $MSG_SPJ . '/';
							?>
							<div class="ui teal label vTooltip">
								<i class="book icon"></i>
								<?php echo $row['spj'] ? "SPJ" : "传统题"; ?>
								<span class="vTooltiptext">评测方式</span>
							</div>
							<div class="ui orange label vTooltip">
								<i class="clock icon"></i>
								<?php echo $row['time_limit']; ?>s
								<span class="vTooltiptext"><?php echo $MSG_Time_Limit; ?></span>
							</div>
							<div class="ui violet label vTooltip">
								<i class="memory icon"></i>
								<?php echo $row['memory_limit']; ?>MB
								<span class="vTooltiptext"><?php echo $MSG_Memory_Limit; ?></span>
							</div>
							<?php if (!isset($OJ_OI_MODE) || !$OJ_OI_MODE) { ?>
								<div class="ui blue label">
									<i class="paper plane icon"></i>
									<?php echo $row['submit']; ?>
									<?php echo $MSG_SUBMIT; ?>
								</div>
								<div class="ui green label">
									<i class="check icon"></i>
									<?php echo $row['accepted']; ?>
									<?php echo $MSG_SOVLED; ?>
								</div>
							<?php } ?>
						</div>
						<div class="ui divider"></div>
						<?php
						if ($row['description']) {
							?>
							<h4>
								<?php echo $MSG_Description; ?>
							</h4>
							<?php
							echo "<span id='description'>".bbcode_to_html($row['description'])."</span>";
						}
						?>
						<?php
						if ($row['input']) {
							?>
							<h4>
								<?php echo $MSG_Input; ?>
							</h4>
							<?php
							echo "<span id='input'>".bbcode_to_html($row['input'])."</span>";
						}
						?>
						<?php
						if ($row['output']) {
							?>
							<h4>
								<?php echo $MSG_Output; ?>
							</h4>
							<?php
							echo "<span id='output'>".bbcode_to_html($row['output'])."</span>";
						}
						$sinput = str_replace("<", "&lt;", $row['sample_input']);
						$sinput = str_replace(">", "&gt;", $sinput);
						$soutput = str_replace("<", "&lt;", $row['sample_output']);
						$soutput = str_replace(">", "&gt;", $soutput);
						?>
						<div class="row" style="margin-top: 12px">
							<div class="col-md-6">
								<div id="qwqs1">
									<?php
									if (strlen($sinput)) {
										?>
										<h4><?php echo $MSG_Sample_Input; ?><a class="ui mini blue button"
												href="javascript:void(0)" id="qwqa1">复制</a></h4>
										<blockquote id="sampleInput">
											<pre id='sinput'><?php
											echo $sinput . "</pre></blockquote>";
									}
									?>
								</div>
							</div>
							<div class="col-md-6">
								<div id="qwqs2">
									<?php
									if (strlen($soutput)) {
										?><h4><?php echo $MSG_Sample_Output; ?><a class="ui mini blue button"
											href="javascript:void(0)" id="qwqa2">复制</a></h4><blockquote id="sampleOutput"><pre id=soutput ><?php
											echo $soutput . '</pre></blockquote>';
									}
									?>
								</div>
							</div>
						</div>
						<?php
						if ($row['hint']) {
							?><h4><?php echo $MSG_HINT; ?></h4><?php
							   echo "<span id=hint >".bbcode_to_html($row['hint'])."</span>";
						}
						?>
						<?php
						if ($pr_flag) {
							echo "<h4>$MSG_Source</h4>";
							$cats = explode(" ", $row['source']);
							foreach ($cats as $cat) {
								echo "<a href='problemset.php?search=" . urlencode(htmlentities($cat, ENT_QUOTES, 'utf-8')) . "'>" . htmlentities($cat, ENT_QUOTES, 'utf-8') . "</a>&nbsp;";
							}
						}
						?>
					</div>
					<!--EndMarkForVirtualJudge-->
				</div>
			</div>
			<div class="five wide column">
				<div class="card" style="padding: 0;">
					<div class="ui vertical fluid menu problemAction">
						  <a href="<?php if ($pr_flag) {
							  echo "submitpage.php?id=$id";
						  } else {
							  echo "submitpage.php?cid=$cid&pid=$pid&langmask=$langmask";
						  } ?>" class="item" aria-current="true">
							<?php echo $MSG_SUBMIT; ?>
						</a>
						<?php if (!$pr_flag)
							echo "<a class='item' href='contest.php?cid=$cid'>$MSG_PROBLEM$MSG_LIST</a>";
						?>
						<a href="<?php echo "status.php?problem_id=" . $row['problem_id']; ?>" class="item"><?php echo $MSG_STATUS; ?></a>
						<?php
						if (isset($_SESSION[$OJ_NAME . '_' . 'administrator']) || isset($_SESSION[$OJ_NAME . '_' . 'problem_manager'])) {
							require_once("include/set_get_key.php");
							?> 
																			<a href="swadmin/problem_edit.php?id=<?php echo $id ?>&getkey=<?php echo $_SESSION[$OJ_NAME . '_' . 'getkey'] ?>" class="item"><?php echo $MSG_EDIT; ?></a>
																			<a href='javascript:phpfm(<?php echo $row['problem_id']; ?>)' class="item"><?php echo $MSG_TESTDATA; ?></a>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php require("./template/bshark/footer.php"); ?>
<?php require("./template/bshark/footer-files.php"); ?>
<script>
	function phpfm(pid) {
		//alert(pid);
		$.post("admin/phpfm.php", {
			'frame': 3,
			'pid': pid,
			'pass': ''
		}, function (data, status) {
			if (status == "success") {
				document.location.href = "admin/phpfm.php?frame=3&pid=" + pid;
			}
		});
	}

	$(document).ready(function () {
<?php if (isset($OJ_MARKDOWN) && $OJ_MARKDOWN) { ?>
				$("div.md").each(function () {
					$(this).html(marked.parse($(this).html()));
				});
<?php } ?>

	});

	function CopyToClipboard(input) {
		var textToClipboard = input;

		var success = true;
		if (window.clipboardData) { // Internet Explorer
			window.clipboardData.setData("Text", textToClipboard);
		}
		else {
			// create a temporary element for the execCommand method
			var forExecElement = CreateElementForExecCommand(textToClipboard);

			/* Select the contents of the element 
			(the execCommand for 'copy' method works on the selection) */
			SelectContent(forExecElement);

			var supported = true;

			// UniversalXPConnect privilege is required for clipboard access in Firefox
			try {
				if (window.netscape && netscape.security) {
					netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
				}

				// Copy the selected content to the clipboard
				// Works in Firefox and in Safari before version 5
				success = document.execCommand("copy", false, null);
			}
			catch (e) {
				success = false;
			}

			// remove the temporary element
			document.body.removeChild(forExecElement);
		}

		if (success) {
			console.log("Success");
		}
		else {
			console.log("Can't");
		}
	}

	function CreateElementForExecCommand(textToClipboard) {
		var forExecElement = document.createElement("pre");
		// place outside the visible area
		forExecElement.style.position = "absolute";
		forExecElement.style.left = "-10000px";
		forExecElement.style.top = "-10000px";
		// write the necessary text into the element and append to the document
		forExecElement.textContent = textToClipboard;
		document.body.appendChild(forExecElement);
		// the contentEditable mode is necessary for the  execCommand method in Firefox
		forExecElement.contentEditable = true;

		return forExecElement;
	}

	function SelectContent(element) {
		// first create a range
		var rangeToSelect = document.createRange();
		rangeToSelect.selectNodeContents(element);

		// select the contents
		var selection = window.getSelection();
		selection.removeAllRanges();
		selection.addRange(rangeToSelect);
	}
	$('#qwqa1').click(function () {
		CopyToClipboard($('#sampleInput').text());
		Toast.fire({
			icon: "success",
			title: "样例输入复制成功"
		});
	});
	$('#qwqa2').click(function () {
		CopyToClipboard($('#sampleOutput').text());
		Toast.fire({
			icon: "success",
			title: "样例输出复制成功"
		});
	});
</script>
	</body>
</html>
