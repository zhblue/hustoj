<!DOCTYPE html>
<html lang="en">

<head>
    <?php $page_title = "提交: $id"; ?>
    <?php include('_includes/head.php') ?>
    <style>
    #source {
        width: 90%;
        height: 600px;
    }
    </style>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <div class="mdui-card">
            <center>
                <script src="include/checksource.js"></script>
                <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title">提交代码</div>
                    <div class="mdui-card-primary-subtitle">题目编号：<?php echo isset($id) ? $id : chr($pid+ord('A')) + "(Contest " + $cid + ")" ; ?></div>
                </div>
                <div class="mdui-card-content">
                    <form id="frmSolution" action="submit.php" method="post" onsubmit='do_submit()'>
                        <?php if (isset($id)){?>
                            <input id="problem_id" type="hidden" value="<?php echo $id; ?>" name="id">
                        <?php } else { ?>
                            <input id="cid" type="hidden" value="<?php echo $cid?>" name="cid">
                            <input id="pid" type="hidden" value="<?php echo $pid?>" name="pid">
                        <?php }?>

                        <span id="language_span">语言：
                            <select id="language" class="mdui-select"
                                name="language" onChange="reloadtemplate($(this).val());">
                                <?php
                                    $lang_count=count($language_ext);
                                    
                                    if(isset($_GET['langmask']))
                                    $langmask=$_GET['langmask'];
                                    else
                                    $langmask=$OJ_LANGMASK;
                                    
                                    $lang=(~((int)$langmask))&((1<<($lang_count))-1);
                                    
                                    if(isset($_COOKIE['lastlang'])) $lastlang=$_COOKIE['lastlang'];
                                    else $lastlang=0;
                                    
                                    for($i=0;$i<$lang_count;$i++){
                                    if($lang&(1<<$i))
                                        echo"<option value=$i ".( $lastlang==$i?"selected":"").">".$language_name[$i]."</option>";
                                    }
                                ?>
                            </select>
                        </span>

                        <?php if($OJ_ACE_EDITOR) { ?>
                            <pre style="width: 90%; height: 600; font-size: 13pt;" cols=180 rows=20
                                id="source"><?php echo htmlentities($view_src,ENT_QUOTES,"UTF-8")?></pre>
                            <input type=hidden id="hide_source" name="source" value="" />
                        <?php } else { ?>
                            <textarea style="width:80%; height:600" cols=180 rows=20 id="source"
                                name="source"><?php echo htmlentities($view_src,ENT_QUOTES,"UTF-8")?></textarea>
                        <?php }?>

                        <?php if (isset($OJ_TEST_RUN) && $OJ_TEST_RUN){?>
                            <div class="mdui-row-sm-1 mdui-row-md-2 mdui-m-a-2" style="width: 80%; text-align: left;">
                                <div class="mdui-col mdui-textfield mdui-textfield-floating-label">
                                    <label class="mdui-textfield-label">输入</label>
                                    <textarea id="input_text" name="input_text" class="mdui-textfield-input"><?php echo $view_sample_input?></textarea>
                                </div>
                                <div class="mdui-col mdui-textfield mdui-textfield-floating-label">
                                    <label class="mdui-textfield-label">输出</label>
                                    <textarea id="out" name="out" class="mdui-textfield-input" disabled>SHOULD BE:<?php echo $view_sample_output?></textarea>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if($OJ_VCODE) {?>
                            <div>
                                <div class="mdui-textfield mdui-textfield-floating-label" style="max-width: 200px; text-align: left; display: inline-block;">
                                    <label class="mdui-textfield-label">验证码</label>
                                    <input id="vcode-input" class="mdui-textfield-input" name="vcode" type="text" autocomplete=off  required>
                                    <div class="mdui-textfield-error">请输入验证码</div>
                                </div>
                                <img id="vcode" alt="click to change" class="mdui-m-b-4 mdui-m-x-3" onclick="this.src='vcode.php?'+Math.random()">
                            </div>
                        <?php } ?>

                        <div class="mdui-card-actions">
                            <div class="mdui-float-right" style="display: inline-block;">
                            <?php if (isset($OJ_ENCODE_SUBMIT) && $OJ_ENCODE_SUBMIT) {?>
                                <input  type="hidden" id="encoded_submit_mark" name="reverse2" value="reverse">
                                <button type="button" class="mdui-btn mdui-ripple mdui-color-teal-500" onclick="encoded_submit();">提交</button>
                            <?php } else { ?>
                                <button type="button" class="mdui-btn mdui-ripple mdui-color-teal-500" onclick="do_submit();">提交</button>
                            <?php } ?>
                            </div>
                            <?php if (isset($OJ_TEST_RUN)&&$OJ_TEST_RUN){?>
                                <div class="mdui-float-left" style="display: inline-block;">
                                    <button id="TestRun" class="mdui-btn mdui-ripple mdui-color-deep-purple" type="button"
                                        onclick="do_test_run()">自测调试</button>
                                    <div id="spinner" class="mdui-spinner" style="display: none; margin-bottom: -10px;"></div>
                                    <span id="result"></span>
                                </div>
                            <?php }?>
                        </div>
                    </form>
                </div>
            </center>
        </div>
    </div>
    <script>
    var sid = 0;
    var i = 0;
    var using_blockly = false;
    var judge_result = [<?php
      foreach($judge_result as $result){
        echo "'$result',";
      }?> ''];

    function print_result(solution_id) {
        sid = solution_id;
        $.ajax({
            url: 'status-ajax.php?tr=1&solution_id='+solution_id,
            method: 'GET',
            success: function(data) {
                $('#out').html(data);
            }
        });
    }

    function fresh_result(solution_id) {
        var tb = window.document.getElementById('result');
        if (solution_id == undefined) {
            tb.innerHTML = "Vcode Error!";
            if ($("#vcode") != null) {
                $("#vcode").attr('src', 'vcode.php?'+Math.random());
            }
            return;
        }
        sid = solution_id;
        $.ajax({
            method: 'GET',
            url: "status-ajax.php?solution_id=" + solution_id,
            success: function(data) {
                let ra = data.split(",");
                var tag = "span";

                if (ra[0] < 4) {
                    $('#spinner').show();
                    window.setTimeout("fresh_result(" + solution_id + ")", 2000);
                } else {
                    if (ra[0] == 11) {
                        tb.innerHTML = `<a href="ceinfo.php?sid=${solution_id}" target="_blank">${judge_result[ra[0]]}</a>`;
                    } else {
                        tb.innerHTML = `<a href="reinfo.php?sid=${solution_id}" target="_blank">${judge_result[ra[0]]}</a>`;
                    }

                    tb.innerHTML += "&nbsp;&nbsp;&nbsp;" + "Memory:" + ra[1];
                    tb.innerHTML += "&nbsp;&nbsp;&nbsp;" + "Time:" + ra[2];

                    window.setTimeout("print_result(" + solution_id + ")", 2000);
                    count = 1;
                    $('#spinner').hide();
                }
            }
        });
    }

    function getSID() {
        var ofrm1 = document.getElementById("testRun").document;
        var ret = "0";
        if (ofrm1 == undefined) {
            ofrm1 = document.getElementById("testRun").contentWindow.document;
            var ff = ofrm1;
            ret = ff.innerHTML;
        } else {
            var ie = document.frames["frame1"].document;
            ret = ie.innerText;
        }
        return ret + "";
    }

    var count = 0;

    function encoded_submit() {
        <?php if($OJ_VCODE) { ?>
        if (!$('#vcode-input').val()) {
            mdui.alert('请输入验证码！');
            return;
        }
        <?php } ?>
        
        var mark = "<?php echo isset($id)?'problem_id':'cid';?>";
        var problem_id = document.getElementById(mark);

        if (typeof(editor) != "undefined") {
            $("#hide_source").val(editor.getValue());
        }
        if (mark == 'problem_id') {
            problem_id.value = '<?php if(isset($id)) echo $id?>';
        }
        else {
            problem_id.value = '<?php if(isset($cid))echo $cid?>';
        }

        document.getElementById("frmSolution").target = "_self";
        document.getElementById("encoded_submit_mark").name = "encoded_submit";
        var source = $("#source").val();

        if (typeof(editor) != "undefined") {
            source = editor.getValue();
            $("#hide_source").val(encode64(utf16to8(source)));
        } else {
            $("#source").val(encode64(utf16to8(source)));
        }
        document.getElementById("frmSolution").submit();
    }

    function do_submit() {
        <?php if($OJ_VCODE) { ?>
        if (!$('#vcode-input').val()) {
            mdui.alert('请输入验证码！');
            return;
        }
        <?php } ?>

        <?php if($OJ_LONG_LOGIN && isset($_COOKIE[$OJ_NAME."_user"]) && isset($_COOKIE[$OJ_NAME."_check"])) { ?>
            let xhr = new XMLHttpRequest();
            xhr.open('GET', 'login.php', true);
            xhr.send();
        <?php } ?>

        if (using_blockly) {
            translate();
        }

        if (typeof(editor) != "undefined") {
            $("#hide_source").val(editor.getValue());
        }

        var mark = "<?php echo isset($id)?'problem_id':'cid';?>";
        var problem_id = document.getElementById(mark);

        if (mark == 'problem_id')
            problem_id.value = '<?php if (isset($id))echo $id?>';
        else
            problem_id.value = '<?php if (isset($cid))echo $cid?>';

        document.getElementById("frmSolution").target = "_self";
        document.getElementById("frmSolution").submit();
    }

    var handler_interval;

    function do_test_run() {
        <?php if($OJ_VCODE) { ?>
        if (!$('#vcode-input').val()) {
            mdui.alert('请输入验证码！');
            return;
        }
        <?php } ?>

        if (handler_interval) {
            window.clearInterval(handler_interval);
        }

        var tb = window.document.getElementById('result');
        var source = $("#source").val();

        if (typeof(editor) != "undefined") {
            source = editor.getValue();
            $("#hide_source").val(source);
        }

        if (source.length < 5) {
            mdui.alert("这么短的代码能行吗？");
            return;
        }

        if (tb != null) {
            $('#spinner').show();
        }

        var mark = "<?php echo isset($id) ? 'problem_id' : 'cid'; ?>";
        var problem_id = document.getElementById(mark);
        problem_id.value = -problem_id.value;
        document.getElementById("frmSolution").target = "testRun";
        $.ajax({
            method: 'POST',
            url: 'submit.php?ajax',
            data: $("#frmSolution").serialize(),
            success:  function(data) {
                fresh_result(data);
            }
        })
        $("#Submit").attr('disabled', 'disabled');
        $("#TestRun").attr('disabled', 'disabled');
        problem_id.value = -problem_id.value;
        count = 20;
        handler_interval = window.setTimeout("resume();", 1000);
    }

    function resume() {
        count--;
        var s = $("#Submit");
        var t = $("#TestRun");
        if (count < 0) {
            s.removeAttr('disabled');
            if (t != null) {
                t.removeAttr('disabled');
            }

            s.val("提交");

            if (t != null) {
                t.val("自测调试");
            }

            if (handler_interval) {
                window.clearInterval(handler_interval);
            }

            if ($("#vcode") != null) {
                $("#vcode").attr('src', 'vcode.php?'+Math.random());
            }
        } else {
            s.val("提交 (" + count + ")");

            if (t != null) {
                t.val("自测调试 (" + count + ")");
            }

            window.setTimeout("resume();", 1000);
        }
    }

    function switchLang(lang) {
        var langnames = new Array("c_cpp", "c_cpp", "pascal", "java", "ruby", "sh", "python", "php", "perl", "csharp",
            "objectivec", "vbscript", "scheme", "c_cpp", "c_cpp", "lua", "javascript", "golang");
        editor.getSession().setMode("ace/mode/" + langnames[lang]);
    }

    function reloadtemplate(lang) {
        console.log("lang=" + lang);
        document.cookie = "lastlang=" + lang;
        //alert(document.cookie);
        var url = window.location.href;
        var i = url.indexOf("sid=");
        if (i != -1) url = url.substring(0, i - 1);

        <?php if (isset($OJ_APPENDCODE)&&$OJ_APPENDCODE){?>
        if (confirm("<?php echo  $MSG_LOAD_TEMPLATE_CONFIRM?>"))
            document.location.href = url;
        <?php }?>
        switchLang(lang);
    }


    function openBlockly() {
        $("#frame_source").hide();
        $("#TestRun").hide();
        $("#language")[0].scrollIntoView();
        $("#language").val(6).hide();
        $("#language_span").hide();
        $("#EditAreaArroundInfos_source").hide();
        $('#blockly').html(
            '<iframe name=\'frmBlockly\' width=90% height=580 src=\'blockly/demos/code/index.html\'></iframe>');
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
        if (typeof(editor) != "undefined") editor.setValue(python.text());
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

    <?php if($OJ_ACE_EDITOR){ ?>
        <?php if(isset($MDUI_OFFLINE) && $MDUI_OFFLINE) { ?>
            <script src="<?php echo $OJ_CDN_URL.$path_fix."/"; ?>/ace/ace.min.js"></script>
            <script src="<?php echo $OJ_CDN_URL.$path_fix."/"; ?>/ace/ext-language_tools.min.js"></script>
            <script>
                ace.config.set('basePath', '<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"; ?>/assets/ace/');
            </script>
        <?php } else { ?>
            <script src="https://cdn.jsdelivr.net/npm/ace-builds@1.4.12/src-noconflict/ace.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/ace-builds@1.4.12/src-noconflict/ext-language_tools.min.js"></script>
            <script>
                ace.config.set('basePath', 'https://cdn.jsdelivr.net/npm/ace-builds@1.4.12/src-min-noconflict/');
            </script>
        <?php } ?>
        <script>
            ace.require("ace/ext/language_tools");
            var editor = ace.edit("source");
            editor.setTheme("ace/theme/chrome");
            switchLang(<?php echo isset($lastlang) ? $lastlang : 0 ;  ?>);
            editor.setOptions({
                enableBasicAutocompletion: true,
                enableSnippets: true,
                enableLiveAutocompletion: false,
                fontFamily: "Consolas",
                fontSize: "20px"
            });
        </script>
    <?php }?>

    <?php if ($OJ_VCODE) { ?>
    <script>
        document.addEventListener('DOMContentLoaded', (event) => { $("#vcode").attr("src", "vcode.php?" + Math.random()); });
    </script>
    <?php } ?>
</body>

</html>
