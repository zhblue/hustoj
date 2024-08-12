<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>
		<?php echo $MSG_SUBMIT; ?> - <?php echo $OJ_NAME; ?>
	</title>
	<?php require("./template/bshark/header-files.php"); ?>
	<style>
		#source {
			width: 100%;
			height: 600px;
			margin: 0;
		}
	</style>
</head>

<body>
	<?php require("./template/bshark/nav.php"); ?>
	<div class="ui container bsharkMain">
		<div class="ui stackable grid">
			<div class="eleven wide column">
				<div class="card" style="display: none; height: 600px;" id="blockly" class="center">Blockly</div>
				<div class="card" style="padding: 0; height: 600px;">
					<div style="">
						<?php if ($OJ_ACE_EDITOR) { ?>
							<pre style="width:100%;height:600px;font-size:15px" cols=180 rows=20
								id="source"><?php echo htmlentities($view_src, ENT_QUOTES, "UTF-8") ?></pre><br>
							<input form="frmSolution" type=hidden id="hide_source" name="source" value="" />
						<?php } else { ?>
							<textarea form="frmSolution" style="width:100%;height:600px" cols=180 rows=20 id="source"
								name="source"><?php echo htmlentities($view_src, ENT_QUOTES, "UTF-8") ?></textarea><br>
						<?php } ?>
					</div>
				</div>
				<?php if (isset($OJ_TEST_RUN) && $OJ_TEST_RUN) { ?>
					<div class="card">
						<div class="card-body">
							<div class="ui two column grid">
								<div class="column">
									<?php echo $MSG_Input ?>:<textarea form="frmSolution" class="form-control"
										style="width:100%" cols=40 rows=5 id="input_text"
										name="input_text"><?php echo $view_sample_input ?></textarea>
								</div>
								<div class="column">
									<?php echo $MSG_Output ?>:
									<textarea form="frmSolution" class="form-control" style="width:100%" cols=10 rows=5
										id="out" name="out" disabled="true"><?php echo $view_sample_output ?></textarea>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			</div>

			<div class="five wide column">
				<div class="card">
					<div class="card-body">
						<script src="<?php echo $OJ_CDN_URL ?>include/checksource.js"></script>
						<form id=frmSolution action="submit.php" method="post" onsubmit='do_submit()' class="ui form">
							<?php if (isset($id)) { ?>
								Problem <span class=blue><b><?php echo $id ?></b></span>
								<input id=problem_id type='hidden' value='<?php echo $id ?>' name="id"><br>
							<?php } else {
								//$PID="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
//if ($pid>25) $pid=25;
								?>
								<?php echo $MSG_PROBLEM; ?> <span class=blue><b>
										<?php echo chr($pid + ord('A')) ?>
									</b></span> of <?php echo $MSG_CONTEST; ?> <span class=blue><b>
										<?php echo $cid ?>
									</b></span><br>
								<input id="cid" type='hidden' value='<?php echo $cid ?>' name="cid">
								<input id="pid" type='hidden' value='<?php echo $pid ?>' name="pid">
							<?php } ?>
							<div class="field">
								<label>
									<?php echo $MSG_LANG; ?>
								</label>
								<div class="ui selection dropdown">
									<?php
									$lang_count = count($language_ext);
									if (isset($_GET['langmask']))
										$langmask = $_GET['langmask'];
									else
										$langmask = $OJ_LANGMASK;
									$lang = (~((int) $langmask)) & ((1 << ($lang_count)) - 1);
									$lastlang = 1;
									if (isset($_COOKIE['lastlang']))
										$lastlang = $_COOKIE['lastlang'];
									if ($lastlang == "undefined")
										$lastlang = 1;
									?>
									<input type="hidden" name="language" id="language"
										onChange="reloadtemplate($(this).val());" value="<?php echo $lastlang; ?>">
									<i class="dropdown icon"></i>
									<div class="default text">
										<?php echo $MSG_LANG; ?>
									</div>
									<div class="scrollhint menu">
										<?php
										for ($i = 0; $i < $lang_count; $i++) {
											if ($lang & (1 << $i))
												echo "<div data-value='$i' class='item " . ($lastlang == $i ? "active" : "") . "'>
" . $language_name[$i] . "
</div>";
										}
										?>
									</div>
								</div>
							</div>
							<?php if ($OJ_VCODE) { ?>
								<div class="field">
									<label><?php echo $MSG_VCODE ?></label>
									<input name="vcode" size=4 type=text  autocomplete=off ><img id="vcode" alt="click to change"
										onclick="this.src='vcode.php?'+Math.random()">
								</div>
							<?php } ?>
							<div class="field">
								<input id="Submit" class="ui button primary" type=button
									value="<?php echo $MSG_SUBMIT ?>" onclick="do_submit();">
								<?php if (isset($OJ_ENCODE_SUBMIT) && $OJ_ENCODE_SUBMIT) { ?>
									<input class="ui button green" title="WAF gives you reset ? try this." type=button
										value="Encoded <?php echo $MSG_SUBMIT ?>" onclick="encoded_submit();">
									<input type=hidden id="encoded_submit_mark" name="reverse2" value="reverse" />
								<?php } ?>

								<?php if (isset($OJ_TEST_RUN) && $OJ_TEST_RUN) { ?>
									<input id="TestRun" class="ui button yellow" type=button value="<?php echo $MSG_TR ?>"
										onclick=do_test_run();>
									<span class="btn" id=result>
										<?php echo $MSG_STATUS; ?>
									</span>
								<?php } ?>
								<?php if (isset($OJ_BLOCKLY) && $OJ_BLOCKLY) { ?>
									<input id="blockly_loader" type=button class="ui button red" onclick="openBlockly()"
										value="<?php echo $MSG_BLOCKLY_OPEN ?>">
									<input id="transrun" type=button class="ui button violet" onclick="loadFromBlockly() "
										value="<?php echo $MSG_BLOCKLY_TEST ?>">
								<?php } ?>
							</div>
						</form>
					</div>
				</div>
				<div class="card" style="padding: 0;">
					<div class="ui vertical fluid menu problemAction">
						<a href="<?php if (isset($id)) {
							echo "problem.php?id=$id";
						} else {
							echo "problem.php?cid=$cid&pid=$pid";
						} ?>" class="item" aria-current="true">
							返回<?php echo $MSG_PROBLEM; ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php require("./template/bshark/footer.php"); ?>
	<?php require("./template/bshark/footer-files.php"); ?>
	<script>
		var sid = 0;
		var i = 0;
		var using_blockly = false;
		var judge_result = [<?php
		foreach ($judge_result as $result) {
			echo "'$result',";
		}
		?> ''];
		function print_result(solution_id) {
			sid = solution_id;
			$("#out").load("status-ajax.php?tr=1&solution_id=" + solution_id);
		}
		function fresh_result(solution_id) {
			var tb = window.document.getElementById('result');
			if (solution_id == undefined) {
				tb.innerHTML = "Vcode Error!";
				if ($("#vcode") != null) $("#vcode").click();
				return;
			}
			sid = solution_id;
			var xmlhttp;
			if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			}
			else {// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange = function () {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var r = xmlhttp.responseText;
					var ra = r.split(",");
					// alert(r);
					// alert(judge_result[r]);
					var loader = "<img width=18 src=image/loader.gif>";
					var tag = "span";
					if (ra[0] < 4) tag = "span disabled=true";
					else tag = "a";
					{
						if (ra[0] == 11)

							tb.innerHTML = "<" + tag + " href='ceinfo.php?sid=" + solution_id + "' class='badge badge-info' target=_blank>" + judge_result[ra[0]] + "</" + tag + ">";
						else
							tb.innerHTML = "<" + tag + " href='reinfo.php?sid=" + solution_id + "' class='badge badge-info' target=_blank>" + judge_result[ra[0]] + "</" + tag + ">";
					}
					if (ra[0] < 4) tb.innerHTML += loader;
					tb.innerHTML += "Memory:" + ra[1] + "kb&nbsp;&nbsp;";
					tb.innerHTML += "Time:" + ra[2] + "ms";
					if (ra[0] < 4)
						window.setTimeout("fresh_result(" + solution_id + ")", 2000);
					else {
						window.setTimeout("print_result(" + solution_id + ")", 2000);
						count = 1;
					}
				}
			}
			xmlhttp.open("GET", "status-ajax.php?solution_id=" + solution_id, true);
			xmlhttp.send();
		}
		function getSID() {
			var ofrm1 = document.getElementById("testRun").document;
			var ret = "0";
			if (ofrm1 == undefined) {
				ofrm1 = document.getElementById("testRun").contentWindow.document;
				var ff = ofrm1;
				ret = ff.innerHTML;
			}
			else {
				var ie = document.frames["frame1"].document;
				ret = ie.innerText;
			}
			return ret + "";
		}
		var count = 0;

		function encoded_submit() {

			var mark = "<?php echo isset($id) ? 'problem_id' : 'cid'; ?>";
			var problem_id = document.getElementById(mark);

			if (typeof (editor) != "undefined")
				$("#hide_source").val(editor.getValue());
			if (mark == 'problem_id')
				problem_id.value = '<?php if (isset($id))
					echo $id ?> ';
																																																																	else
				problem_id.value = '<?php if (isset($cid))
					echo $cid ?> ';

				document.getElementById("frmSolution").target = "_self";
				document.getElementById("encoded_submit_mark").name = "encoded_submit";
				var source = $("#source").val();
				if (typeof (editor) != "undefined") {
					source = editor.getValue();
					$("#hide_source").val(encode64(utf16to8(source)));
				} else {
					$("#source").val(encode64(utf16to8(source)));
				}
				//      source.value=source.value.split("").reverse().join("");
				//      alert(source.value);
				document.getElementById("frmSolution").submit();
			}

			function do_submit() {
				if (using_blockly)
					translate();
				if (typeof (editor) != "undefined") {
					$("#hide_source").val(editor.getValue());
				}
				var mark = "<?php echo isset($id) ? 'problem_id' : 'cid'; ?>";
			var problem_id = document.getElementById(mark);
			if (mark == 'problem_id')
				problem_id.value = '<?php if (isset($id))
					echo $id ?> ';
																																																																	else
				problem_id.value = '<?php if (isset($cid))
					echo $cid ?> ';
				document.getElementById("frmSolution").target = "_self";
				document.getElementById("frmSolution").submit();
			}
			var handler_interval;
			function do_test_run() {
				if (handler_interval) window.clearInterval(handler_interval);
				var loader = "<img width=18 src=image/loader.gif>";
				var tb = window.document.getElementById('result');
				var source = $("#source").val();
				if (typeof (editor) != "undefined") {
					source = editor.getValue();
					$("#hide_source").val(source);
				}
				if (source.length < 10) return alert("too short!");
				if (tb != null) tb.innerHTML = loader;

				var mark = "<?php echo isset($id) ? 'problem_id' : 'cid'; ?>";
			var problem_id = document.getElementById(mark);
			problem_id.value = -problem_id.value;
			document.getElementById("frmSolution").target = "testRun";
			//$("#hide_source").val(editor.getValue());
			//document.getElementById("frmSolution").submit();
			$.post("submit.php?ajax", $("#frmSolution").serialize(), function (data) { fresh_result(data); });
			$("#Submit").prop('disabled', true);
			$("#TestRub").prop('disabled', true);
			problem_id.value = -problem_id.value;
			count = 20;
			handler_interval = window.setTimeout("resume();", 1000);
		}
		function resume() {
			count--;
			var s = $("#Submit")[0];
			var t = $("#TestRub")[0];
			if (count < 0) {
				s.disabled = false;
				if (t != null) t.disabled = false;
				s.value = "<?php echo $MSG_SUBMIT ?>";
				if (t != null) t.value = "<?php echo $MSG_TR ?>";
				if (handler_interval) window.clearInterval(handler_interval);
				if ($("#vcode") != null) $("#vcode").click();
			} else {
				s.value = "<?php echo $MSG_SUBMIT ?>(" + count + ")";
				if (t != null) t.value = "<?php echo $MSG_TR ?>(" + count + ")";
				window.setTimeout("resume();", 1000);
			}
		}
		function switchLang(lang) {
			var langnames = new Array("c_cpp", "c_cpp", "pascal", "java", "ruby", "sh", "python", "php", "perl", "csharp", "objectivec", "vbscript", "scheme", "c_cpp", "c_cpp", "lua", "javascript", "golang");
			editor.getSession().setMode("ace/mode/" + langnames[lang]);
		}
		function autoSave() {
			var mark = "<?php echo isset($id) ? 'problem_id' : 'cid'; ?>";
			var problem_id = $("#" + mark).val();
			if (!!localStorage) {
				let key = "<?php echo $_SESSION[$OJ_NAME . '_user_id'] ?>source:" + location.href;
				if (typeof (editor) != "undefined")
					$("#hide_source").val(editor.getValue());
				localStorage.setItem(key, $("#hide_source").val());
				//console.log("autosaving "+key+"..."+new Date());
			}
		}
		$(document).ready(function () {
			if (!!localStorage) {
				let key = "<?php echo $_SESSION[$OJ_NAME . '_user_id'] ?>source:" + location.href;
				let saved = localStorage.getItem(key);
				if (saved != null && saved != "" && saved != editor.getValue()) {
					Swal.fire({
						text: "发现自动保存的源码，是否加载？（仅有一次机会）",
						icon: 'info',
						showCancelButton: true,
						confirmButtonColor: '#2180db',
						cancelButtonColor: '#aaa'
					}).then((result) => {
						if (result.isConfirmed) {
							if (typeof (editor) != "undefined")
								editor.setValue(saved);
						}
					})
				}
			}
		})
		window.setInterval('autoSave();', 5000);
		function reloadtemplate(lang) {
			console.log("lang=" + lang);
			document.cookie = "lastlang=" + lang.value;
			//alert(document.cookie);
			var url = window.location.href;
			var i = url.indexOf("sid=");
			if (i != -1) url = url.substring(0, i - 1);
			//  if(confirm("<?php echo $MSG_LOAD_TEMPLATE_CONFIRM ?>"))
			//       document.location.href=url;
			switchLang(lang);
		}
		function openBlockly() {
			$("#frame_source").hide();
			$("#TestRun").hide();
			$("#language")[0].scrollIntoView();
			$("#language").val(6).hide();
			$("#language_span").hide();
			$("#EditAreaArroundInfos_source").hide();
			$('#blockly').show()
			$('#blockly').html('<iframe name=\'frmBlockly\' width=100% height=600 src=\'blockly/demos/code/index.html\'></iframe>');
			$("#blockly_loader").hide();
			$("#transrun").show();
			$("#Submit").prop('disabled', true);
			using_blockly = true;

		}
		function translate() {
			var blockly = $(window.frames['frmBlockly'].document);
			var tb = blockly.find('td[id=tab_python]');
			var python = blockly.find('pre[id=content_python]');
			tb.click();
			blockly.find('td[id=tab_blocks]').click();
			if (typeof (editor) != "undefined") editor.setValue(python.text());
			else $("#source").val(python.text());
			$("#language").val(6);

		}
		function loadFromBlockly() {
			translate();
			do_test_run();
			$("#frame_source").hide();
			//  $("#Submit").prop('disabled', false);
		}
	</script>
	<script language="Javascript" type="text/javascript" src="include/base64.js"></script>
	<?php if ($OJ_ACE_EDITOR) { ?>
		<script src="ace/ace.js"></script>
		<script src="ace/ext-language_tools.js"></script>
		<script>
			ace.require("ace/ext/language_tools");
			var editor = ace.edit("source");
			editor.setTheme("ace/theme/chrome");
			switchLang(<?php echo $lastlang ?>);
			editor.setOptions({
				enableBasicAutocompletion: true,
				enableSnippets: true,
				enableLiveAutocompletion: false,
				fontFamily: "Consolas",
				fontSize: "20px"
			});
			reloadtemplate($("#language").val());

		</script>
	<?php } ?>
	<?php if ($OJ_VCODE) { ?>
		<script>
			$(document).ready(function () {
				$("#vcode").attr("src", "vcode.php?" + Math.random());
			})
		</script>
	<?php } ?>
</body>

</html>