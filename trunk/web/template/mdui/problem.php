<!DOCTYPE html>
<html lang="cn">

<head>
    <?php $page_title = $id.' '.$row["title"]; ?>
    <?php include('_includes/head.php'); ?>
</head>


<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <div class="mdui-card">
            <div class="mdui-card-primary" style="text-align: center;">
                <div class="mdui-card-primary-title"><?php echo (isset($id) ? $id : chr($pid+ord('A'))).': '.$row["title"]; ?></div>
                <div class="mdui-card-primary-subtitle">
                    <div class="mdui-m-x-1" style="display: inline-block;">时间限制：<code><?php echo $row["time_limit"].'s'; ?></code></div>
                    <div class="mdui-m-x-1" style="display: inline-block;">内存限制：<code><?php echo $row["memory_limit"].'MB'; ?></code></div>
                    <?php if($row["spj"]) { ?>
                        <div class="mdui-color-yellow mdui-m-t-1" style="border: 1px solid transparent; border-radius: 5px; padding: .5px 5px; font-size: 85%; display: inline-block;">Special Judge</div>
                    <?php } ?>
                </div>
            </div>
            <div class="mdui-card-actions" style="text-align: center;">
                <?php if($pr_flag){ ?>
                    <a class="mdui-btn mdui-ripple mdui-color-teal-500" href="submitpage.php?id=<?php echo $id; ?>">提交</a>
                <?php } else { ?>
                    <a class="mdui-btn mdui-ripple mdui-color-teal-500" href="submitpage.php?cid=<?php echo $cid; ?>&pid=<?php echo $pid; ?>&langmask=<?php echo $langmask; ?>">提交</a>
                      <a class="mdui-btn mdui-ripple mdui-color-blue-600" href="contest.php?cid=<?php echo $cid; ?>">题目列表</a>
                <?php } ?>
                
                <?php if (!(isset($OJ_OI_MODE) && $OJ_OI_MODE)) { ?>
                      <a class="mdui-btn mdui-ripple mdui-color-blue-600" href="status.php?problem_id=<?php echo $row['problem_id']; ?>&jresult=4">通过: <?php echo $row["accepted"]; ?></a>
                      <a class="mdui-btn mdui-ripple mdui-color-blue-600" href="status.php?problem_id=<?php echo $row['problem_id']; ?>">提交: <?php echo $row["submit"]; ?></a>
                      <a class="mdui-btn mdui-ripple mdui-color-blue-600" href="problemstatus.php?id=<?php echo $row['problem_id']; ?>">统计</a>
                <?php } ?>

                <?php if (isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'contest_creator']) || isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])) { ?>
                    <?php require_once("include/set_get_key.php"); ?>
                     <a class='mdui-btn mdui-ripple mdui-color-green-600' href="admin/problem_edit.php?id=<?php echo $id; ?>&getkey=<?php echo htmlentities($_SESSION[$OJ_NAME.'_'.'getkey'], ENT_QUOTES, 'UTF-8'); ?>">编辑</a>
                    <a class='mdui-btn mdui-ripple mdui-color-green-600' href="javascript:phpfm(<?php echo $row['problem_id']; ?>)">测试数据</a>
                    <div class="mdui-dialog" id="testdata-dialog" style="min-height: 600px; min-width: 1000px;">
                        <b class="mdui-float-left mdui-p-y-2 mdui-p-x-3">文件管理器</b>
                        <button class="mdui-btn mdui-float-right" onclick="testdata_dialog.close()">
                            <i class="mdui-icon material-icons">close</i>
                        </button>
                        <iframe id="testdata-iframe" style="width: 100%; height: 100%"></iframe>
                    </div>
                    <script>
                        var testdata_dialog = new mdui.Dialog('#testdata-dialog');
                    </script>
                <?php } ?>
            </div>
            <hr class="mdui-m-t-2" style="border-top: 1px solid #cfcfcf88; margin: 5px 15px;">
            <div class="mdui-card-content mdui-p-a-4">
                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        <h3>题目描述</h3>
                    </div>
                    <div class='panel-body content'>
                        <?php echo $row['description']?>
                    </div>
                </div>

                <?php if($row['input']) { ?>
                    <div class='panel panel-default'>
                        <div class='panel-heading'>
                            <h3>输入格式</h3>
                        </div>
                        <div class='panel-body content'>
                            <?php echo $row['input']?>
                        </div>
                    </div>
                <?php } ?>
                <?php if($row['output']) { ?>
                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        <h3>输出格式</h3>
                    </div>
                    <div class='panel-body content'>
                        <?php echo $row['output']?>
                    </div>
                </div>
                <?php } ?>
                <?php  
                    $sinput = str_replace("<","&lt;",$row['sample_input']);
                    $sinput = str_replace(">","&gt;",$sinput);
                    $soutput = str_replace("<","&lt;",$row['sample_output']);
                    $soutput = str_replace(">","&gt;",$soutput);
                ?>
                <?php if(strlen($sinput)) { ?>
                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        <h3>
                            样例输入
                            <a class="mdui-btn mdui-btn-icon mdui-btn-dense mdui-color-theme-accent mdui-ripple mdui-float-right"
                                    href="javascript:CopyToClipboard($('#sampleinput').text())">
                                <i class="mdui-icon material-icons">content_copy</i>
                            </a>
                        </h3>
                    </div>
                    <div class='panel-body'>
                        <pre><code id="sampleinput" class="language-plaintext"><?php echo $sinput?></code></pre>
                    </div>
                </div>
                <?php } ?>

                <?php if(strlen($soutput)) { ?>
                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        <h3>
                            样例输出
                            <a class="mdui-btn mdui-btn-icon mdui-btn-dense mdui-color-theme-accent mdui-ripple mdui-float-right"
                                    href="javascript:CopyToClipboard($('#sampleoutput').text())">
                                <i class="mdui-icon material-icons">content_copy</i>
                            </a>
                        </h3>
                    </div>
                    <div class='panel-body'>
                        <pre><code id="sampleoutput" class="language-plaintext"><?php echo $soutput?></code></pre>
                    </div>
                </div>
                <?php } ?>

                <?php if($row['hint']) { ?>
                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        <h3>提示/说明</h3>
                    </div>
                    <div class='panel-body content'>
                        <?php echo $row['hint']?>
                    </div>
                </div>
                <?php }

                if($pr_flag){?>
                <div class='panel panel-default'>
                    <div class='panel-heading'>
                        <h3>分类</h3>
                    </div>

                    <div fd="source" style="word-wrap: break-word;" pid="<?php echo $row['problem_id']?>" class="panel-body content">
                        <?php 
                            $colors = [
                                "red",
                                "pink",
                                "purple",
                                "orange",
                                "light-blue",
                                "green",
                                "teal"
                                ];
                            foreach($result as $row) { 
                                $hash_num = hexdec(substr(md5($row["source"]),0,7));
                                $label_color = $colors[$hash_num%count($colors)];
                                $label_color = $label_color ? $label_color : "theme";
                                echo '<a class="mdui-btn mdui-btn-dense mdui-color-'.$label_color.'-accent mdui-ripple mdui-m-x-2" href="problemset.php?search='.urlencode($row["source"]).'">'.$row["source"].'</a>';
                            }
                        ?>
                    </div>

                </div>
                <?php }?>

            </div>
                <hr style="border-top: 1px solid #cfcfcf88; margin: 5px 15px;">
                <div class="mdui-float-right mdui-m-a-2">
                    <?php if ($pr_flag) { ?>
                        <a class="mdui-btn mdui-ripple mdui-color-teal-500" href="submitpage.php?id=<?php echo $id; ?>">提交</a>
                    <?php } else { ?>
                        <a class="mdui-btn mdui-ripple mdui-color-teal-500" href="submitpage.php?pid=<?php echo $pid; ?>&cid=<?php echo $cid; ?>&langmask=<?php echo $langmask; ?>">提交</a>
                    <?php } ?>
                </div>
            </div>
        </div>
        <script>
        function phpfm(pid) {
            $.ajax({
                async: false,
                url: "admin/phpfm.php",
                data: {
                    'frame': 3,
                    'pid': pid,
                    'pass': '',
                    'csrf': '<?php echo $token; ?>'
                },
                success: function(data) {
                    $('#testdata-iframe').attr("src", "admin/phpfm.php?frame=3&pid="+pid);
                    testdata_dialog.open();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            $("#creator").html("problem-ajax.php?pid=<?php echo $id?>");
        });

        function CopyToClipboard(input) {
            var textToClipboard = input;

            var success = true;
            if (window.clipboardData) { // Internet Explorer
                window.clipboardData.setData("Text", textToClipboard);
            } else {
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
                } catch (e) {
                    success = false;
                }

                // remove the temporary element
                document.body.removeChild(forExecElement);
            }

            if (success) {
                mdui.snackbar("复制成功！", { position: 'right-bottom' });
            } else {
                mdui.snackbar("复制失败！", { position: 'right-bottom' });
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
        </script>

</body>

</html>